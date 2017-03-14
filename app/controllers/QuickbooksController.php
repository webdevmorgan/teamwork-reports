<?php
class QuickbooksController extends BaseController {

    public function indexAction() {

        $quick = new QuickbooksAPI();
        $res = $quick->checkConnection();
        return View::make('quickbook_index')->with(
            array(
                'quickbooks_is_connected'=>$res,
                'quickbooks_menu_url' => Config::get('quickbook.quickbooks_menu_url'),
                'quickbooks_oauth_url'=> Config::get('quickbook.quickbooks_oauth_url')
            )
        );

    }

    public function oauthAction() {
        $quick = new QuickbooksAPI();
        return $quick->checkOAuth();
    }

    public function successAction() {
        return View::make('quickbook_success');
    }

    public function menuUrlAction() {
        $quick = new QuickbooksAPI();
        $quick->checkConnection();
        return View::make('quickbook_menu')->with(
            array(
                'IntuitAnywhere'=>$quick->getIntuit(),
                'the_username' => Config::get('quickbook.the_username'),
                'the_tenant' => Config::get('quickbook.the_tenant')
            )
        );

    }


    //get all new consultants
    public function checkMembersAction(){
        $tw = new TeamworkApi();
        $projects = $tw->getAllProjects();
        $valid_roles = array('consultant');

        $project_list = $projects['content']['projects'];
        $people_ids = array();
        $i = 0;
        if($project_list){
            foreach($project_list as $project){
                $project_roles = $tw->getProjectRoles($project['id']);
                $roles = $project_roles['content']['roles'];
                if($roles){
                    foreach($roles as $role){
                        if(in_array(strtolower($role['roleName']), $valid_roles)){
                            $people = $role['people'];
                            if($people){
                                foreach($people as $person){


                                    //add new member to database
                                    $data = $tw->getPersonDetails($person['id']);
                                    $member_data = $data['content']['person'];
                                    $person_data = array(
                                        'tw_id' => $member_data['id'],
                                        'first_name' => $member_data['first-name'],
                                        'last_name' => $member_data['last-name'],
                                        'role' => $role['roleName'],
                                        'rate' => 1
                                    );

                                    if($this->memberExists($person)){
                                        $member = Member::where('tw_id','=',$member_data['id'])->first();
                                    } else {
                                        $member = Member::create($person_data);
                                    }

                                    if($member->qb_id == ''){
                                        //add to quickbooks
                                        $vendor_id = $this->addVendorAction($member_data);
                                        if($vendor_id){
                                            $member->qb_id = $vendor_id;
                                            $member->save();

                                            echo 'New Vendor Added - '.$member->id.'('.$vendor_id.'). <br />';
                                        }
                                    } else {
                                       echo $member->first_name.' '.$member->last_name.' already exists on the system. <br />';
                                    }

                                }
                            }
                        }
                    }
                }
            }
        }

        //return $people_ids;
    }

    public function addVendorAction($memberData = array()) {
        $quick = new QuickbooksAPI();
        $quick->checkConnection();
        $IPP = new QuickBooks_IPP(Config::get('quickbook.db_quickbooks'));
        $creds = $quick->getIntuit()->load(Config::get('quickbook.the_username'), Config::get('quickbook.the_tenant'));
        // Tell the framework to load some data from the OAuth store
        $IPP->authMode(
            QuickBooks_IPP::AUTHMODE_OAUTH,
            Config::get('quickbook.the_username'),
            $creds);

        $realm = $creds['qb_realm'];

        $response = '';
        $vendor_id = '';
        if ($Context = $IPP->context())
        {
            if(!empty($memberData)){

                $display_name = $memberData['first-name'].' '.$memberData['last-name'];

                $IPP->version(QuickBooks_IPP_IDS::VERSION_3);
                $VendorService = new QuickBooks_IPP_Service_Vendor();
                $Vendor = new QuickBooks_IPP_Object_Vendor();
                $Vendor->setTitle(Utilities::limitChars($memberData['title'], 15));
                $Vendor->setGivenName(Utilities::limitChars($memberData['first-name'], 25));
                $Vendor->setMiddleName('');
                $Vendor->setFamilyName(Utilities::limitChars($memberData['last-name'], 25));
                $Vendor->setDisplayName(Utilities::limitChars($display_name, 100));
                $Vendor->setCompanyName(Utilities::limitChars($memberData['company-name'], 50));
                $Vendor->setPrimaryEmailAddr($memberData['email-address']);
                if ($resp = $VendorService->add($Context, $realm, $Vendor))
                {
                    $vendor_id = $resp;
                }
                else
                {
                }
            } else {
            }

        } else {
            die('Unable to load a context...?');
        }

        return $vendor_id;
    }

    public function twMemberExists($user_data){
        $usercount = Member::whereRaw('tw_id = ? AND first_name = ? AND last_name = ?', array($user_data['id'], $user_data['firstName'], $user_data['lastName']))->count();

        if($usercount > 0)
            return true;

        return false;
    }

    public function memberExists($user_data){
        $user = Member::whereRaw('tw_id = ?', array($user_data['id']))->first();

        if($user)
            return $user->id;

        return false;
    }

    //compute monthly amount for consultation
    public function computeMonthlyHoursAction(){
        //get all members with quickbooks account
        $members = Member::where('qb_id', '!=', '')->get();

        $tw = new TeamworkApi();
        $all_times = $this->getAllTimeEntries();

        //account ref
        //check if the ref account exists else create new
        if($this->queryAccountRef()){
            //query account id
            $account_id = $this->queryAccountRef();
        } else {
            $account_id = $this->addAccount();
        }

        if($account_id){
            foreach($members as $member){
                $time_per_person = Utilities::searchArr($all_times, 'person-id', $member->tw_id);
                $total_time = Utilities::sumTime($time_per_person);
                if($total_time['billable'] > 0){
					$billable_amount = $total_time['billable'] / 60;
				} else {
					$billable_amount = 0;
				}
                $rate = 1;
                if($member->rate){
                    $rate = Utilities::cleanNumber($member->rate);
                }
                $member['amount'] = $billable_amount * $rate;
                $this->addBillAction($member, $account_id);
            }
        }

    }

    public function getAllTimeEntries(){
        $tw = new TeamworkApi();
        $time = $tw->getTimeEntriesByMonth();
        $pages = $time['headers']['X-Pages'];
        $all_times = array();
        $grouped_time = array();
        if(isset($time['content']['time-entries'])) {
            $all_times = $time['content']['time-entries'];
            if($pages > 1) {
                for($i=2; $i <= $pages; $i++){
                    $time_cont = $tw->getTimeEntriesByMonth($i);
                    $time_entries = $time_cont['content']['time-entries'];
                    foreach ($time_entries as $time_ent) {
                        $all_times[] = $time_ent;
                    }

                }
            }
        }

        return $all_times;
    }

    public function groupTimeEntriesByPerson($data) {
        $arr = array();
        foreach($data as $key => $item) {
            if(isset($item['person-id']) &&  $item['person-id'] != ""){
                $arr[$item['person-id']][$key] = $item;
            }
        }
        return $arr;
    }

    public function addBillAction($billingData = array(), $account_id = false) {
        $quick = new QuickbooksAPI();
        $quick->checkConnection();
        $IPP = new QuickBooks_IPP(Config::get('quickbook.db_quickbooks'));
        $creds = $quick->getIntuit()->load(Config::get('quickbook.the_username'), Config::get('quickbook.the_tenant'));
        // Tell the framework to load some data from the OAuth store
        $IPP->authMode(
            QuickBooks_IPP::AUTHMODE_OAUTH,
            Config::get('quickbook.the_username'),
            $creds);

        $realm = $creds['qb_realm'];

        if($account_id){
            // Load the OAuth information from the database
            if ($Context = $IPP->context())
            {
                // Set the IPP version to v3
                $IPP->version(QuickBooks_IPP_IDS::VERSION_3);

                $BillService = new QuickBooks_IPP_Service_Bill();

                $Bill = new QuickBooks_IPP_Object_Bill();

                $current_date = date('Y-m-d');
                $Bill->setDocNumber(Utilities::generate_random_alphanumeric());
                $Bill->setTxnDate($current_date);
                $Bill->setVendorRef($billingData['qb_id']);

                $Line = new QuickBooks_IPP_Object_Line();
                $Line->setAmount($billingData['amount']);
                $Line->setDetailType('AccountBasedExpenseLineDetail');

                $AccountBasedExpenseLineDetail = new QuickBooks_IPP_Object_AccountBasedExpenseLineDetail();
                $AccountBasedExpenseLineDetail->setAccountRef($account_id);

                $Line->setAccountBasedExpenseLineDetail($AccountBasedExpenseLineDetail);

                $Bill->addLine($Line);

                if ($id = $BillService->add($Context, $realm, $Bill))
                {
                    echo 'New bill id is: ' . $id.' <br />';
                }
                else
                {
                    echo 'Bill add failed...? ' . $BillService->lastError().' <br />';
                }

            }
            else
            {
                die('Unable to load a context...?');
            }
        } else {
            echo 'Account not set. <br />';
        }

    }

    public function addAccount(){
        $quick = new QuickbooksAPI();
        $quick->checkConnection();
        $IPP = new QuickBooks_IPP(Config::get('quickbook.db_quickbooks'));
        $creds = $quick->getIntuit()->load(Config::get('quickbook.the_username'), Config::get('quickbook.the_tenant'));
        // Tell the framework to load some data from the OAuth store
        $IPP->authMode(
            QuickBooks_IPP::AUTHMODE_OAUTH,
            Config::get('quickbook.the_username'),
            $creds);

        $realm = $creds['qb_realm'];
        $account_number = 0;
        // Load the OAuth information from the database
        if ($Context = $IPP->context())
        {
            // Set the IPP version to v3
            $IPP->version(QuickBooks_IPP_IDS::VERSION_3);

            $AccountService = new QuickBooks_IPP_Service_Account();

            $Account = new QuickBooks_IPP_Object_Account();

            $Account->setName('Consultation');
            $Account->setDescription('Consultation Fee');
            $Account->setAccountType('Income');

            if ($resp = $AccountService->add($Context, $realm, $Account))
            {
                echo 'Our new Account ID is: [' . $resp . '] <br />';
                $account_number = $resp;
            }
            else
            {
                echo $AccountService->lastError().' <br />';
            }

        }
        else
        {
            die('Unable to load a context...?');
        }

        return $account_number;
    }

    public function queryAccountRef(){
        $quick = new QuickbooksAPI();
        $quick->checkConnection();
        $IPP = new QuickBooks_IPP(Config::get('quickbook.db_quickbooks'));
        $creds = $quick->getIntuit()->load(Config::get('quickbook.the_username'), Config::get('quickbook.the_tenant'));
        // Tell the framework to load some data from the OAuth store
        $IPP->authMode(
            QuickBooks_IPP::AUTHMODE_OAUTH,
            Config::get('quickbook.the_username'),
            $creds);

        $realm = $creds['qb_realm'];
        $account_number = false;
        // Load the OAuth information from the database
        if ($Context = $IPP->context())
        {
            // Set the IPP version to v3
            $IPP->version(QuickBooks_IPP_IDS::VERSION_3);

            $AccountService = new QuickBooks_IPP_Service_Account();

            $accounts = $AccountService->query($Context, $realm, "SELECT * FROM Account WHERE name = 'Consultation' ORDER BY id DESC");

            if(!empty($accounts)){
                if($accounts[0]){
                    $account_number = $accounts[0]->getId();
                } else {
                    $account_number = false;
                }
            }else{
                $account_number = false;
            }


        } 
        else
        {
            die('Unable to load a context...?');
        }


        return $account_number;
    }

	public function testAction(){
		if($this->queryAccountRef()){
            //query account id
            echo 'old';
        } else {
            echo 'new';
        }
	}
}