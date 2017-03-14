    <?php

    /**
     * Performs server-side composer update
     * @author Daniel Kouba <dan@brainz.cz>
     *
     */

    echo '<pre>      ______
      / ____/___  ____ ___  ____  ____  ________  _____
     / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
    / /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
    \____/\____/_/ /_/ /_/ .___/\____/____/\___/_/  INSTALL
                        /_/

';


    //Access Check - TODO! implement better solution - i.e. IP whitelist
    date_default_timezone_set('CET');
    if( @$_GET['date']==!date('Y-m-d',time()) ) {
        //die('Access Denied - security token not provided');
    }

    //Configuration
    define('ROOT_DIR',realpath('../'));
    define('EXTRACT_DIRECTORY', ROOT_DIR. '/storage/composer');
    define('HOME_DIRECTORY', ROOT_DIR. '/storage/composer/home');
    define('COMPOSER_INSTALLED', file_exists(ROOT_DIR.'/vendor'));
    set_time_limit(100);
    ini_set('memory_limit',-1);  //could be forbidden on server
    if (!getenv('HOME') && !getenv('COMPOSER_HOME')) {  putenv("COMPOSER_HOME=".HOME_DIRECTORY);  }


    //Extracting Composer library
    if (file_exists(EXTRACT_DIRECTORY.'/vendor/autoload.php') == true) {
        echo "Extracted autoload already exists. Skipping phar extraction as presumably it's already extracted.\n";
    } else {
        $composerPhar = new Phar("../composer.phar");
        //php.ini setting phar.readonly must be set to 0
        $composerPhar->extractTo(EXTRACT_DIRECTORY);
    }

    // change directory to root
    chdir(ROOT_DIR);

    //This requires the phar to have been extracted successfully.
    require_once (EXTRACT_DIRECTORY.'/vendor/autoload.php');

    //Use the Composer classes
    use Composer\Console\Application;
    use Composer\Command\UpdateCommand;
    use Symfony\Component\Console\Input\ArrayInput;


    //Create the commands
    if(!COMPOSER_INSTALLED) {
        echo "This is first composer run: --no-scripts option is applies\n";
        $args = array('command'=>'install','--no-scripts'=>true);
    } else {
        $args = array('command'=>'install');
    }
    $input = new ArrayInput($args);

    //Create the application and run it with the commands
    $application = new Application();
    $application->setAutoExit(false);
    $application->setCatchExceptions(false);
    try {
        //Running commdand php.ini allow_url_fopen=1 && proc_open() function available
        $exitCode = $application->run($input);
    } catch (\Exception $e) {
        $exitCode = 1;
        echo 'Error: '.$e->getMessage()."\n";
    }


    //Result message
    if($exitCode ==0) {
        echo "Successfully Done";
    } elseif($exitCode ==2) {
        echo "Composer Failed due to dependency solving error";
    } else {
        echo "Composer Failed due to generic error";
    }
    die('</pre>');