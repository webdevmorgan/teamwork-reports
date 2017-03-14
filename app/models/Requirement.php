<?php
    class Requirement {

    protected static $pathArrays = array();

    public static function buildScript($paths = array())
    {
        $scripts = '';

        self::$pathArrays = array_merge(self::$pathArrays, $paths);

        foreach(self::$pathArrays  as $path) {
            $scripts .= '<script type="text/javascript" src="'.$path.'"></script>';
        }

        return $scripts;
    }


    public static function javascripts($paths)
    {
        $scripts = self::buildScript($paths);

        View::share(array(
            'requireJS' => $scripts
        ));

        return $scripts;
    }

    public static function requireJS()
    {
       return View::shared('requireJS');
    }
}