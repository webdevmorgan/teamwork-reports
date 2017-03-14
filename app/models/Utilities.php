<?php
class Utilities {

     public static function createStatus ($data) {
        if ($data['status'] == 'completed') {
            return "Completed";
        } else if ($data['progress'] == 0 && $data['completed'] == false && !($data['timeIsLogged'])) {
            return "Not Started";
        } else if (($data['progress'] > 0 || $data['timeIsLogged']) && $data['completed'] == false) {
            return "Started";
        }
    }

    public static function createStatusClass($data) {
        $last_changed =date("Ymd", strtotime($data['last-changed-on']));
        $due_date = date("Ymd", strtotime($data['due-date']));
        $current_date = date("Ymd");
        $upcoming_date = date("Ymd", strtotime('+7 days'));
        $start_date =  isset($data['start-date']) ? date("Ymd", strtotime($data['start-date'])) : 0;
        if($data['status'] == 'completed'){
            return 'completed';
        } else if($data['completed'] == false && ($last_changed > $due_date || $current_date > $due_date)){
            return "late";
        //} else if ($data['progress'] == 0 && ($data['completed'] == false) && ($last_changed < $due_date) && ($due_date <= $upcoming_date) && ($current_date >= $due_date) ) {
        } else if ($data['progress'] == 0 && ($data['completed'] == false) && ($last_changed < $due_date) && ($due_date <= $upcoming_date)) {
            return 'upcoming';
        }
    }

    public static function getProjectStartEnd($arr) {
        $date = date("F j, Y", strtotime($arr['created-on']));
        return $date;
    }

    public static function getMileStones($arr) {
        return $arr['milestones'];
    }

    public static function getProjectName($arr){
        return $arr['project-name'];
    }

    public static function getCompanyName($arr){
        return $arr['company-name'];
    }

    public static function getDaysLate($data){
        $current = date('Y-m-d');
        $datetime1 = new DateTime($data['deadline']);
        $datetime2 = new DateTime($current);
        $difference = $datetime1->diff($datetime2);
        return $difference;
    }

    public static function getEstimated($time) {
        //return  date('H:i', mktime(0,$time));
        $hours  = floor($time/60); //round down to nearest minute.
        $minutes = $time % 60;
        $hrs = $hours > 1 ? "hrs" : "hr";
        $mins = $minutes > 1 ? "mins" : "min";
        if($hours > 0 && $minutes > 0) {
            return $hours." ".$hrs." ".$minutes." ".$mins;
        } else if ($hours > 0 && $minutes == 0) {
            return $hours." ".$hrs;
        } else if ($hours == 0 && $minutes > 0) {
            return $minutes." ".$mins;
        } else {
            return "None";
        }
    }

    public static function searchArr($array, $key, $value) {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, self::searchArr($subarray, $key, $value));
            }
        }

        return $results;

    }

    public static function sumTime($arr) {
        $totals = array();
        $mins = 0;
        $hours = 0;
        $billable_mins = 0;
        $billable_hrs = 0;


        foreach ($arr as $time){

            if ($time['isbillable']) {
                $billable_mins += $time['minutes'];
                $billable_hrs += $time['hours'];
            } else {
                $mins += $time['minutes'];
                $hours += $time['hours'];
            }
        }
        $total = $mins + ($hours * 60);
        $totals['total'] = $total;
        $totals['non_billable'] = $total;
        $billable =  $billable_mins + ($billable_hrs * 60);
        $totals['billable'] = $billable;
        return $totals;



    }
	
	public static function dump($var)
    {
        echo '<pre>';
        echo 'File : '.xdebug_call_file();
        echo "<br/>";
        echo 'Line : '.xdebug_call_line();
        echo '<br/>';
        print_r($var);
        echo '</pre>';
    }

    public static function cleanImageSource($path){
        $prefix = 'https://';
        $trail = str_replace($prefix,'', $path);
        $trail = $prefix.$trail;

        return $trail;
    }

    public static function limitChars($message, $length = 100) {
        if(strlen($message) >= $length) {
            $message = substr($message, 0, $length);
        }

        return $message;
    }

    public static function generate_random_alphanumeric($length = 9) {
        $alphabets = range('A','Z');
        $numbers = range('0','9');
        $additional_characters = array();
        $final_array = array_merge($numbers,$alphabets,$additional_characters);

        $combination = '';

        while($length--) {
            $key = array_rand($final_array);
            $combination .= $final_array[$key];
        }

        return $combination;
    }

    /**
     * @param $value
     * @return mixed
     * Remove commas in number
     */
    public static function cleanNumber($value){
        return str_replace(",","", $value);
    }

    public static function searchForKey($id, $array) {
        foreach ($array as $key => $val) {
            if ($val['API_KEY'] === $id) {
                return $val;
            }
        }
        return null;
    }
}