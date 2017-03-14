<?php
class TeamworkApi {
    public $channel;
    public $milestones;
    public $ptasklists;
    public $res;
    public $company;
    private $api_key;
    private $default_api_key;

    function __construct(){
        $default_key = Config::get('teamwork.default_api_key');
        $api_key = Session::get('api_key') ? Session::get('api_key') : $default_key;
        $config_data = Config::get('teamwork.ACCOUNTS');
        $account_data = Utilities::searchForKey($api_key, $config_data);

        $this->api_key = $api_key;
        $this->company = $account_data['COMPANY'];
        $this->default_api_key = $default_key;

    }

    private function executeMulti($actions) {
        $ch = array();
        $results = array();
        $mh = curl_multi_init();
        foreach($actions as $key => $val) {
            $ch[$key] = curl_init();
            curl_setopt($ch[$key], CURLOPT_URL, $val);
            curl_multi_add_handle($mh, $ch[$key]);
        }
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        }
        while ($running > 0);
        // Get content and remove handles.
        foreach ($ch as $key => $val) {
            $results[$key] = curl_multi_getcontent($val);
            curl_multi_remove_handle($mh, $val);
        }
        curl_multi_close($mh);
        return $results;
    }

    public function authenticate($key) {
        $url = 'http://authenticate.teamworkpm.net/authenticate.json';
        $headers = ['Authorization: BASIC '. base64_encode(
            $key . ':xxx'
        )];
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $i = 0;
        while ($i < 5) {
            $data        = curl_exec ($ch);
            $status      = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers     = $this->parseHeaders(substr($data, 0, $header_size));
            if ($status === 400 &&
                (int) $headers['X-RateLimit-Remaining'] === 0) {
                $i ++;
                sleep(10);
            } else {
                break;
            }
        }
        $body        = substr($data, $header_size);
        $errorInfo   = curl_error($ch);
        $error       = curl_errno($ch);
        curl_close($ch);
        if ($error) {
            throw new Exception($errorInfo);
        }
        $result['headers'] = $headers;
        $result['content'] = json_decode($body, true);
        $this->storeETag($headers['ETag']);
        return $result;
    }

    private function execute($action) {

        $etag =  isset($_SESSION['ETag'])? $_SESSION['ETag'] : "";
        $url = 'http://'.  $this->company . '.teamworkpm.net/'. $action;
        $headers = ["If-None-Match:".$etag,'Authorization: BASIC '. base64_encode(
            $this->api_key . ':xxx'
        )];
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $i = 0;
        while ($i < 5) {
            $data        = curl_exec ($ch);
            $status      = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers     = $this->parseHeaders(substr($data, 0, $header_size));
            if ($status === 400 &&
                (int) $headers['X-RateLimit-Remaining'] === 0) {
                $i ++;
                sleep(10);
            } else {
                break;
            }
        }
        $body        = substr($data, $header_size);
        $errorInfo   = curl_error($ch);
        $error       = curl_errno($ch);
        curl_close($ch);
        if ($error) {
            throw new Exception($errorInfo);
        }
        $result['headers'] = $headers;
        $result['content'] = json_decode($body, true);
        $this->storeETag($headers['ETag']);
        return $result;
    }

    private function executeDefault($action) {

        $etag =  isset($_SESSION['ETag'])? $_SESSION['ETag'] : "";
        $url = 'http://'.  $this->company . '.teamworkpm.net/'. $action;
        $headers = ["If-None-Match:".$etag,'Authorization: BASIC '. base64_encode(
            $this->api_key . ':xxx'
        )];
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $i = 0;
        while ($i < 5) {
            $data        = curl_exec ($ch);
            $status      = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers     = $this->parseHeaders(substr($data, 0, $header_size));
            if ($status === 400 &&
                (int) $headers['X-RateLimit-Remaining'] === 0) {
                $i ++;
                sleep(10);
            } else {
                break;
            }
        }
        $body        = substr($data, $header_size);
        $errorInfo   = curl_error($ch);
        $error       = curl_errno($ch);
        curl_close($ch);
        if ($error) {
            throw new Exception($errorInfo);
        }
        $result['headers'] = $headers;
        $result['content'] = json_decode($body, true);
        $this->storeETag($headers['ETag']);
        return $result;
    }

    private function storeETag($etag) {
        Session::put('ETag', $etag);
    }

    private function parseHeaders($stringHeaders){
        $headers = [];
        $stringHeaders = trim($stringHeaders);
        if ($stringHeaders) {
            $parts = explode("\n", $stringHeaders);
            foreach ($parts as $header) {
                $header = trim($header);
                if ($header && false !== strpos($header, ':')) {
                    list($name, $value) = explode(':', $header, 2);
                    $value = trim($value);
                    $name  = trim($name);
                    if (isset($headers[$name])) {
                        if (is_array($headers[$name])) {
                            $headers[$name][] = $value;
                        } else {
                            $_val = $headers[$name];
                            $headers[$name] = [$_val, $value];
                        }
                    } else {
                        $headers[$name] = $value;
                    }
                }
            }
        }
        return $headers;
    }

    public function getResults($action) {
        $res = $this->execute($action);
        return $res;
    }

    public function getMilestones($pid) {
        $action = "/projects/".$pid."/milestones.json?&showTaskLists=true";
        $res = $this->execute($action);
        return $res;
    }

    public function getTasklistProject($pid) {
        $action = "/projects/".$pid."/todo_lists.json?nestSubTasks=yes&showMilestones=yes&&status=all&&showTasks=yes&&filter=all&&includeOverdue=yes&includeCompletedSubtasks=true";
        $res = $this->execute($action);
        return $res;
    }

    public function getTasklists($action) {
        $res = $this->execute($action);
        return $res;
    }

    public function getMilestonebyProject($project_id) {
        $project_id = (int) $project_id;
        if ($project_id <= 0) {
            throw new Exception('Invalid param project_id');
        }
        return $this->execute("projects/$project_id/$this->action");
    }

    public function getTaskbyTasklist ($id) {
        $tid = (int) $id;
        $action = "tasklists/".$id."/tasks.json?nestSubTasks=yes&&getSubTasks=yes&&includeCompletedTasks=true&&includeCompletedSubtasks=true";
        if ($tid <= 0) {
            throw new Exception('Invalid param tasklist id');
        }
        return $this->execute($action);
    }

    public function getAllTime($pid, $page= 1) {
        $action = "/projects/".$pid."/time_entries.json?page=".$page;
        $res = $this->execute($action);
        return $res;
    }

    public function getTaskComment($tid, $page=1) {
        $action = "/tasks/".$tid."/comments.json?page=".$page."&pageSize=50";
        $res = $this->execute($action);
        return $res;
    }

    public function getProjectTotal($pid){
        $action = "projects/".$pid."/time/total.json";
        $res = $this->execute($action);
        return $res;

    }

    public function getCompany($pid) {
        $action = "projects/".$pid."/companies.json";
        $res = $this->execute($action);
        return $res;
    }

    public function getProjectDetails($pid){
        $action = "projects/".$pid.".json";
        $res = $this->execute($action);
        return $res;

    }

    /*Data is from the default user not from the logged user*/
    public function getAllProjects(){
        $action = "projects.json";
        $res = $this->executeDefault($action);
        return $res;
    }

    /*Data is from the default user not from the logged user*/
    public function getProjectRoles($projectId){
        $action = "/projects/".$projectId."/projectroles.json";
        $res = $this->executeDefault($action);
        return $res;
    }

    /*Data is from the default user not from the logged user*/
    public function getPersonDetails($personId){
        $action = "/people/".$personId.".json";
        $res = $this->executeDefault($action);
        return $res;
    }

    public function getTimeEntriesByMonth($page= 1) {
        $start_date = date('Ym01');
        $end_date = date('Ymt');
        /*$start_date = date('20140801');
        $end_date = date('20141231');*/

        $action = "/time_entries.json?page=".$page."&fromdate=".$start_date."&todate=".$end_date;
        $res = $this->executeDefault($action);
        return $res;
    }


}