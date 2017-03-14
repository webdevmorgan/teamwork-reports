<?php

class IndexController extends BaseController {

    public function indexAction() {
        $request = Input::all();
        if(isset($request['changeaccount'])) {
            Session::flush();
            return View::make('index');
        } else if(Session::get('remember_me')== true && Session::has('api_key')) {
            return Redirect::to('/projects');
        } else {
            return View::make('index');
        }
    }

    public function projectListsAction(){
        if (Request::isMethod('post')){
            $requestData = Input::all();
            $remember = isset($requestData['remember_me']) ? true: false;
            $tw = new TeamworkApi();
            $res = $tw->authenticate($requestData['api_key']);

            //get account data
            $config_data = Config::get('teamwork.ACCOUNTS');
            $account_data = Utilities::searchForKey($requestData['api_key'], $config_data);

            if(!empty($res['content'])) {
               if($res['content']['account']['companyid'] == $account_data['COMPANY_ID']){
                    Session::put('api_key', $requestData['api_key']);
                    Session::put('remember_me', $remember);
                    $tw2 = new TeamworkApi();
                    //$acc = $tw2->getResults('/account.json');
                    $action2 =  "/projects.json";
                    $projects = $tw2->getResults($action2);
                    Session::put('account', $res['content']['account']);
                    return View::make('projects')->with(
                        array(
                            'company'=>$res['content']['account'],
                            'projects'=>$projects['content']['projects']
                        )
                    );
                } else {
                    return Redirect::away('http://cto2go.ca/teamwork/');
                }

            } else {
                return Redirect::to('/')->with('message', 'Invalid API Key.');
            }
        } else {
            if ((Session::get('remember_me') == true) && Session::has('api_key')){
                $tw = new TeamworkApi();
                //$acc = $tw->getResults('/account.json');
                $acc = Session::get('account');
                $action2 =  "/projects.json";
                $projects = $tw->getResults($action2);
                return View::make('projects')->with(
                    array(
                        'company'=>$acc,
                        'projects'=>$projects['content']['projects']
                    )
                );
            } else {
                return Redirect::to('/');
            }
        }

    }

    public function reportAction() {
        if (Session::has('api_key')){
            $requestData = Input::all();
            $tw = new TeamworkApi();
            $action = "/projects/".$requestData['project'].".json";
            $proj = $tw->getResults($action);
            $lists = $tw->getTasklistProject($requestData['project']);
            $cont = $lists['content']['todo-lists'];
            $filteredm = $this->getMilestones($cont);
            $total_time_res = $tw->getProjectTotal($requestData['project']);
            $total_time = $total_time_res['content']['projects'][0];
            $comp_name = $total_time['company']['name'];
            $project_name = $total_time['name'];
            $project = $tw->getProjectDetails($requestData['project']);
            $project_logo = $project['content']['project']['logo'];
            $start = $proj['content']['project']['startDate'] != "" ? date("F j, Y",strtotime($proj['content']['project']['startDate'])): "&nbsp;";
            $end =  $proj['content']['project']['endDate'] != "" ? date("F j, Y",strtotime($proj['content']['project']['endDate'])): "&nbsp;";
            $time = $tw->getAllTime($requestData['project']);
            $grouped_time = array();
            if(isset($time['content']['time-entries'])) {
                $first_time = $time['content']['time-entries'];
                if($time['headers']['X-Pages'] > 1) {
                    for($i=2; $i <= $time['headers']['X-Pages']; $i++){
                        $tw = new TeamworkApi();
                        $time_cont = $tw->getAllTime($requestData['project'], $i);
                        $time_entries = $time_cont['content']['time-entries'];
                        foreach ($time_entries as $time_ent) {
                            $first_time[] = $time_ent;
                        }

                    }
                }
                $grouped_time = $this->groupTimeEntries($first_time);
            }
            Session::put('project_start', $proj['content']['project']['startDate']);
            Session::put('project_end', $proj['content']['project']['endDate']);
            Session::put('project', $requestData['project']);
            Session::put('tasklists', $filteredm);
            Session::put('time_entries', $time);
            Session::put('total_time', $total_time);
            Session::put('grouped_time', $grouped_time);

            Requirement::javascripts(
                array(
                    'js/main.js'
                ));

            return View::make('report')->with(
                array(
                    'project_logo'=>$project_logo,
                    'project_name'=>$project_name,
                    'comp_name'=>$comp_name,
                    'start'=>$start,
                    'end'=>$end,
                    'total_time'=>$total_time,
                    'milestones'=> $filteredm['contents'],
                    'time'=>$grouped_time,
                    'checking'=>$filteredm['milestones'],
                    'company'=> Session::get('account'),
                    'comments'=> $filteredm['comments']
                )
            )->render();

        } else {

            return Redirect::to('/');
        }

    }

    /*for deletion*/
    public function ajaxContentAction() {
        $project_id = Session::get('project');
        $tw = new TeamworkApi();
        $filteredm =  Session::get('tasklists');
        $total_time = Session::get('total_time');
        $comp_name = $total_time['company']['name'];
        $project_name = $total_time['name'];
        $start = date("F j, Y", strtotime(Session::get('project_start')));
        $end =  date("F j, Y", strtotime(Session::get('project_end')));
        $grouped_time = Session::get('grouped_time');

        $html = View::make('content')->with(
            array(
                'project_name'=>$project_name,
                'comp_name'=>$comp_name,
                'start'=>$start,
                'end'=>$end,
                'total_time'=>$total_time,
                'milestones'=> $filteredm['contents'],
                'time'=>$grouped_time,
                'checking'=>$filteredm['milestones']
            )
        )->render();
        return Response::json(
            array(
                'html' => $html
            ));
    }

    /*for deletion*/
    public function getTasksAction() {
        $requestData = Input::all();
        $tasks = $this->getMilestoneTasklist($requestData['milestone_id'],$requestData['tasklist_id'],Session::get('tasklists'));
        $times = Session::get('time_entries');
        $first_time = $times['content']['time-entries'];
        if($times['headers']['X-Pages'] > 1) {
            for($i=2; $i <= $times['headers']['X-Pages']; $i++){
                $tw = new TeamworkApi();
                $time_cont = $tw->getAllTime($requestData['project_id'], $i);
                $time_entries = $time_cont['content']['time-entries'];
                foreach ($time_entries as $time_ent) {
                    $first_time[] = $time_ent;
                }

            }
        }
        $grouped_time = $this->groupTimeEntries($first_time);
        return View::make('tasks')->with(
            array(
                'tasks'=>$tasks,
                'params'=>$requestData,
                'time_entries'=>$grouped_time
            )
        );

    }

    /*for deletion*/
    public function createPDFAction() {
        $params = Input::all();
        $title = 'Project Report';
        $objPHPExcel = new PHPExcel();

        $rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
        $rendererLibrary = 'dompdf';
        $rendererLibraryPath = base_path().'/vendor/'.$rendererLibrary;

        if (!PHPExcel_Settings::setPdfRenderer(
            $rendererName,
            $rendererLibraryPath
        )) {
            die(
                'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
                    '<br />' .
                    'at the top of this script as appropriate for your directory structure'
            );
        }
        $objPHPExcel->getActiveSheet()->setShowGridLines(true);
        $cols = array('A','B','C','D','E', 'F', 'G', 'H', 'I', 'J', 'K');
        foreach($cols as $col) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $title)
            ->mergeCells('A1:K1')
            ->getStyle('A1:K1')->applyFromArray(array('font' => array('size' => 14)));

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Project Start Date:')
            ->setCellValue('B2', 'Date Here')
            ->setCellValue('C2', 'End Date:')
            ->setCellValue('D2', 'Date Here')
            ->getStyle("A2:D2")->applyFromArray(array('font' => array('size' => 8)));
        $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray(array('font' => array( 'bold' => true)));
        $objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray(array('font' => array( 'bold' => true)));

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A3','Milestones and associated tasks')
            ->mergeCells('A3:K3')
            ->getStyle("A3:K3")->applyFromArray(array('font' => array('bold' => true,'size' => 10)));

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A4','Total Hours:  Time Here  Total Estimated Time: ')
            ->mergeCells('A4:K4')
            ->getStyle("A4:K4")->applyFromArray(array('font' => array('bold' => false,'size' => 7)));

        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12)->setAutoSize(false);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10)->setAutoSize(false);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A6', '')
            ->setCellValue('B6', 'Milestone')
            ->setCellValue('C6', 'Description')
            ->setCellValue('D6', 'Date Due')
            ->setCellValue('E6', 'Responsible')
            ->setCellValue('F6', 'Status')
            ->setCellValue('G6', 'Days Late')
            ->setCellValue('H6', 'Date Completed')
            ->getStyle("A6:H6")->applyFromArray(array("font" => array( "bold" => true,'size' => 7)));






        // Save Excel2003/CSV/PDF file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
        $filename = "project_report.pdf";

        $objWriter->save(self::downloadDirectory().$filename);

        return Response::json( array( "filename" => $filename ) );

    }

    /*pdf download*/
    public function createDomPDFAction(){
      error_log("createDomPDFAction()", 0);
        $total_time = Session::get('total_time');
        $filename = $total_time['name']."_".date('ymd').".pdf";
        $pdf = new PDFConverter();
        $pdf->createPDF(Input::get('content'), $filename);
        return Response::json( array( "filename" => $filename ) );
    }

    public function createmPDFAction(){
        $mpdf= new mPDF();



    }

    public function downloadAction() {
        $file = Input::get('file');
        $link = self::downloadDirectory().$file;

        return Response::download($link,
            $file,
            array(
                'Content-Type: application/octet-stream',
                'Content-Disposition: attachment'
            ));

    }

    public static function downloadDirectory() {
        return base_path()."/public/documents/";
    }

    /**/
    private function getMilestoneTasklist($mid, $tid, $arr) {
        $res = $arr[$mid]['tasklists'][$tid]['todo-items'];
        return $res;
    }

    public function groupTimeEntries($data) {
        $arr = array();
        foreach($data as $key => $item) {
            if(isset($item['todo-item-id']) &&  $item['todo-item-id'] != ""){
                $arr[$item['todo-item-id']][$key] = $item;
            }
        }
        return $arr;
    }

    public function groupComments($data) {
        $arr = array();
        foreach($data as $key => $item) {
            if(isset($item['todo-item-id']) &&  $item['todo-item-id'] != ""){
                $arr[$item['todo-item-id']][$key] = $item;
            }
        }
        return $arr;
    }

    public function getMilestones($arr) {
        $test2 = array();
        $arr_2 = array();
        foreach($arr as $milestone) {
            if(!empty($milestone['milestone'])) {
                $test[] = $this->createTasklistData($milestone);
                if (array_key_exists($milestone['milestone-id'], $test2)) {
                    $test2[$milestone['milestone-id']]['tasklists'][$milestone['id']] = $this->createTasklistData($milestone);
                } else {
                    $test2[$milestone['milestone-id']] = $this->createMilestone($milestone['milestone']);
                    $test2[$milestone['milestone-id']]['tasklists'][$milestone['id']] = $this->createTasklistData($milestone);
                }
            }
        }
        if(empty($test2)){
            $arr_2['milestones'] = false;
            $arr_2['contents'] = $arr;

        } else {
            $arr_2['milestones'] = true;
            $arr_2['contents'] = $test2;

        }

        $comments_arr = array();
        foreach($arr as $tasklist) {
            $tasks = $tasklist['todo-items'];
            if(!empty($tasks)){
                foreach($tasks as $task) {
                    if($task['comments-count']) {
                        $comments_arr[$task['id']] = $this->getComments($task['id']);
                    }
                    if(!empty($task['subTasks'])){
                        foreach($task['subTasks'] as $subTask) {
                            if($subTask['comments-count'] != 0) {
                                $comments_arr[$subTask['id']] = $this->getComments($subTask['id']);
                            }
                        }
                    }
                }
            }
        }

        //var_dump($comments_arr);

        $arr_2['comments'] = $comments_arr;
        return $arr_2;

    }

    private function getComments($taskid) {
        ini_set('max_execution_time', 300);
        $tw = new TeamworkApi();
        $comments = $tw->getTaskComment($taskid);
        $last_3 = $comments['content']['comments'];;
        if(count($last_3) > 3){
            $last_3 = array_slice($comments['content']['comments'], -3, 3);
        }

        return $last_3;

    }

    private function createTasklistData($arr) {
        $arr_ = array();
        $arr_['project_id'] = $arr['project_id'];
        $arr_['name'] = $arr['name'];
        $arr_['milestone-id'] = $arr['milestone-id'];
        $arr_['description'] = $arr['description'];
        $arr_['todo-items'] = $arr['todo-items'];
        $arr_['complete'] = $arr['complete'];
        $arr_['id'] = $arr['id'];
        return $arr_;
    }

    private function createMilestone($arr){
        $arr_ = array();
        $arr_['id'] = $arr['id'];
        $arr_['title'] = $arr['title'];
        $arr_['created-on'] = $arr['created-on'];
        $arr_['last-changed-on'] = $arr['last-changed-on'];
        $arr_['status'] = $arr['status'];
        $arr_['completed'] = $arr['completed'];
        $arr_['description'] = $arr['description'];
        $arr_['deadline'] = $arr['deadline'];
        $arr_['responsible-party-names'] = $arr['responsible-party-names'];
        if(isset($arr['completed-on'])){
            $arr_['completed-on'] = $arr['completed-on'];
        }
        return $arr_;
    }

    public function getTodofirst($arr) {
        return $arr[0]['company-name'];
    }

    public function testAction() {
    }

    public function downloadtestAction() {

        $pdf = new PDFConverter();
        $pdf->createPDF(base_path()."/public/documents/test.html", 'sample3.pdf');

    }

    public function checkMembersAction() {
        /*Get People (within a Company)

        GET /companies/{company_id}/people.json*/
        //http://onemotion.teamwork.com/companies/19447/people.xml

        /*COMPANY NAME 	one test Sandbox Company
        COMPANY ID 	1315137830
        MERCHANT ID  Merchant Account (9999999904069279)
        INDUSTRY 	Construction Trades */

        if ((Session::get('remember_me') == true) && Session::has('api_key')){
            $tw = new TeamworkApi();
            $account_data = Session::get('account');
            $company_id = $account_data['companyid'];
            $action = '/companies/'.$company_id.'/people.json';
            $persons = $tw->getResults($action);

            return View::make('checkmembers')->with(
                array(
                    'persons'=>$persons
                )
            );
        } else {
            return Redirect::to('/');
        }
    }

    public function computeHoursAction() {
        return View::make('computehours');
    }
}