<?php

class TestController extends BaseController {

    public function indexAction(){
        $api_key = '';
        $companies = Config::get('teamwork.COMPANIES');
        Utilities::dump($companies);
        /*$index = Utilities::searchForKey($api_key, $companies);
        Utilities::dump($index);*/
    }

    public function index5555Action(){
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
            $Bill->setVendorRef('{-101}');

            /*$Line = new QuickBooks_IPP_Object_Line();
            $Line->setAmount(1000);
            $Line->setDetailType('AccountBasedExpenseLineDetail');

            $AccountBasedExpenseLineDetail = new QuickBooks_IPP_Object_AccountBasedExpenseLineDetail();
            $AccountBasedExpenseLineDetail->setAccountRef('{-16}');

            $Line->setAccountBasedExpenseLineDetail($AccountBasedExpenseLineDetail);*/
            $Line = new QuickBooks_IPP_Object_Line();
            $Line->setDetailType('SalesItemLineDetail');
            $Line->setAmount(12.95 * 2);
            $Line->setDescription('Test description goes here.');

            $SalesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
            $SalesItemLineDetail->setItemRef('8');
            $SalesItemLineDetail->setUnitPrice(12.95);
            $SalesItemLineDetail->setQty(2);

            $Line->addSalesItemLineDetail($SalesItemLineDetail);

            $Bill->addLine($Line);


            if ($id = $BillService->add($Context, $realm, $Bill))
            {
                print('New bill id is: ' . $id);
            }
            else
            {
                print('Bill add failed...? ' . $BillService->lastError());
            }

        }
        else
        {
            die('Unable to load a context...?');
        }
    }

    public function index4324234Action() {
        $tw = new TeamworkApi();
        $projects = $tw->getAllProjects();
        $valid_roles = array('primary developer contact');

        $project_list = $projects['content']['projects'];
        $people_list = array();
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
                                    if(!$this->memberExists($person)){
                                        $people_list[] = $person['id'];
                                        //add new member to database and add to quickbooks
                                        $data = $tw->getPersonDetails($person['id']);

                                        $member_data = $data['content']['person'];
                                        $person_data = array(
                                            'id' => $member_data['id'],
                                            'first_name' => $member_data['first-name'],
                                            'last_name' => $member_data['last-name'],
                                            'role' => $role['roleName']
                                        );

                                        //add new member to db
                                        $member = Member::create($person_data);
                                        if($member){
                                            //add to quickbooks
                                            $vendor = $this->addVendorAction($member_data);
                                            echo $vendor;
                                        }
                                    }

                                }
                            }
                        }
                    }
                }
            }
        }

        //Utilities::dump($people_list);

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
        if ($Context = $IPP->context())
        {
            if(!empty($memberData)){
                $IPP->version(QuickBooks_IPP_IDS::VERSION_3);
                $VendorService = new QuickBooks_IPP_Service_Vendor();
                $Vendor = new QuickBooks_IPP_Object_Vendor();
                $Vendor->setTitle($memberData['title']);
                $Vendor->setGivenName($memberData['first-name']);
                $Vendor->setMiddleName('');
                $Vendor->setFamilyName($memberData['last-name']);
                $Vendor->setDisplayName($memberData['first-name'].' '.$memberData['last-name']);
                if ($resp = $VendorService->add($Context, $realm, $Vendor))
                {
                    $response = 'New Vendor Created';
                }
                else
                {
                    $response = $VendorService->lastError($Context);
                }
            } else {
                $response = 'No data available';
            }

        } else {
            die('Unable to load a context...?');
        }

        return $response;
    }

    public function memberExists($user_data){
        $usercount = Member::whereRaw('id = ? AND first_name = ? AND last_name = ?', array($user_data['id'], $user_data['firstName'], $user_data['lastName']))->count();

        if($usercount > 0)
            return true;

        return false;
    }

    public function getNewConsoltants(){
        $tw = new TeamworkApi();
        $projects = $tw->getAllProjects();
        $valid_roles = array('primary developer contact');

        $project_list = $projects['content']['projects'];
        $people_list = array();
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
                                    if(!$this->memberExists($person)){
                                        $people_list[] = $person['id'];
                                    }

                                }
                            }
                        }
                    }
                }
            }
        }

        return $people_list;
    }
    

}