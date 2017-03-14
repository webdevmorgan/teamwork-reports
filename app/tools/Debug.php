<?php
/**
 * User: Precy
 * Date: 3/24/14
 * Time: 10:55 AM
 */
class Debug {

    /**
     * @param $var any value/array
     */
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

} 