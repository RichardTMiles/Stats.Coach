<?php


use Modules\Session;    // We store the session in our db, and look up via IP and Session id

use Modules\Request;    // Easy string / data validation

use Modules\View;

use Modules\Error\ErrorCatcher;

// Initial requests will be VMVC, pjax = MVC, events = HMVC => MVC

function CarbonPHP($PHP): callable
{
    function startApplication($restartURI = false): void
    {
        global $view;

        if ($restartURI):                                          // This will always be se in a socket
            Request::changeURI($restartURI ?: '/');         // Dynamically using pjax + headers
            $_POST = [];                                           // Only PJAX + AJAX can post
        endif;

        Session::update($restartURI === true);                // Get User. Setting RestartURI = true hard restarts app

        $view = View::getInstance($restartURI === true);     // Send the wrapper? only run once. (singleton)

        include SERVER_ROOT . BOOTSTRAP;                            // Router
    }

    ################  Filter Malicious Requests  #################
    if (pathinfo($_SERVER['REQUEST_URI'] ?? '/', PATHINFO_EXTENSION) != null) {
        if ($_SERVER['REQUEST_URI'] == '/robots.txt') {
            echo include SERVER_ROOT . 'robots.txt';
            exit(1);
        }
        ob_start();
        echo inet_pton($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Go away.' . PHP_EOL;
        echo "\n\n\t\n" . $_SERVER['REQUEST_URI'];
        $report = ob_get_clean();
        $file = fopen(SERVER_ROOT . 'Data/Logs/Request/url_' . time() . '.log', "a");
        fwrite($file, $report);
        fclose($file);
        exit(0);    // A request has been made to an invalid file
    }


    ############# Basic Information  ##################
    define('SITE_TITLE', $PHP['SITE_TITLE'] ?? 'Define a site title in carbon_config');

    define('SITE_VERSION', $PHP['SITE_VERSION'] ?? phpversion());

    define('SYSTEM_EMAIL', $PHP['SYSTEM_EMAIL'] ?? false);

    define('REPLY_EMAIL', $PHP['REPLY_EMAIL'] ?? false);

    ################# Application Paths ########################
    # Dynamically Find the current url on the server
    define('URL', (isset($_SERVER['SERVER_NAME']) ?
        ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] : null), true);


    define('URI', ltrim(urldecode(parse_url($_SERVER['REQUEST_URI'] ?? $_SERVER['REQUEST_URI'] = '/', PHP_URL_PATH)), '/'), true);


    /** Mark out for app local testing
     * if (URL !== true && $_SERVER['SERVER_NAME'] != $PHP['URL']) {
     * throw new Error('Invalid Server Name');
     * die(1);
     * }
     * **/

    /**
     * The following constants MUST be used wherever applicable
     * The template and dependence path
     *
     * @constant SITE_ROOT          The current url is automaticaly produced
     * @constant VENDOR             Composers Vendor File
     * @constant ERROR_LOG          Path to the runtime errors file, this can be turned on or off in the Error Class
     * @constant CONTENT_ROOT       Location for the sites template files .tpl.php or .php
     * @constant CONTENT_WRAPPER    Location of the outer content wrapper from <html></html>
     * @constant TEMPLATE_ROOT      Path to the a pre-built template. If template is custom built enter SERVER_ROOT
     * @constant TEMPLATE_PATH      Path to the template for public use i.e. relative path for .css includes
     * @constant WRAPPING_REQUIRES_LOGIN  Bool  If the template wrapper is dependant apon being logged in.
     */
    define('SITE', url . DS, true);                                    // http(s)://example.com/  - do not change

    define('BOOTSTRAP', $PHP['ROUTES'] ?? false);

    define('CONTENT', DS . $PHP['CONTENT'] ?? false);                  // TODO - I changed this from /public/statscoach

    define('VENDOR', DS . $PHP['VENDOR'] ?? false);

    define('TEMPLATE', DS . $PHP['TEMPLATE'] ?? false);                     // Path to the template for public use i.e. relative path for .css includes

    define('VENDOR_ROOT', SERVER_ROOT . $PHP['VENDOR'] ?? false);

    define('TEMPLATE_ROOT', SERVER_ROOT . $PHP['TEMPLATE'] ?? false);

    define('CONTENT_ROOT', SERVER_ROOT . $PHP['CONTENT'] ?? false);

    define('CONTENT_WRAPPER', SERVER_ROOT . $PHP['CONTENT_WRAPPER'] ?? false);

    define('WRAPPING_REQUIRES_LOGIN', $PHP['WRAPPING_REQUIRES_LOGIN'] ?? false);                                     // I use the same headers every where

    ################    Database    ####################
    /**
     * The following constants are used by the Database
     * Which uses a MYSQL database with a PDO wrapper
     *
     * @constant DB_HOST The databases Host i.e. localhost
     * @constant DB_NAME The name of the location on the database
     * @constant DB_USER The user name if required
     * @constant DB_PASS The users password if applicable
     *
     */
    define('DB_HOST', $PHP['DB_HOST'] ?? false);

    define('DB_NAME', $PHP['DB_NAME'] ?? false);

    define('DB_USER', $PHP['DB_USER'] ?? false);

    define('DB_PASS', $PHP['DB_PASS'] ?? false);

    ###############   Autoloading   ####################
    # PSR4 Autoloader, with common case first added for namespace = currentDir
    # Composer Autoloader
    ####################################################
    if (false == (include SERVER_ROOT . 'Application/Modules/AutoLoad.php') ||
        false == (include SERVER_ROOT . 'Data/vendor/autoload.php')) {
        print 'App Map Error \n';
        die(1);
    }

    $thankGod = new Modules\Autoload;   // start the class!
    foreach ($PHP['AUTOLOAD'] as $name => $path)
        $thankGod->addNamespace($name, $path);

    ################  Up the render speed ? ####################
    define('MINIFY_CONTENTS', $PHP['MINIFY_CONTENTS']);

    ################    Socket      ####################
    if (!defined('SOCKET')) {
        define('SOCKET', false);
        if (($PHP['SOCKET'] ?? false) && 'webcache' !== getservbyport(($PHP['SOCKET']['PORT'] ?? 8080), 'tcp'))
            \Modules\Helpers\Fork::safe(function () {
                shell_exec('server.php');           // when threading is supported ill do more, until then I wait
            });

    }
    ################  Ajax Refresh  ####################

    // Must return a non empty value
    define('PJAX', (isset($_GET['_pjax']) || (isset($_SERVER["HTTP_X_PJAX"]) && $_SERVER["HTTP_X_PJAX"])));

    // (PJAX == true) return required, else (!PJAX && AJAX) return optional (socket valid)
    define('AJAX', (PJAX || ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))));


    define('AJAX_OUT', $PHP['AJAX_OUT'] ?? false);

    // We only allow post requests through ajax/pjax
    if (!AJAX) $_POST = [];

    // This should return the template
    define('HTTPS', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'));

    // This will not return data
    define('HTTP', !(HTTPS || SOCKET || AJAX));

    // If we're using https our htaccess should handle url resolution before this point
    if (!$PHP['HTTP'] && HTTP) throw new Exception('Failed to switch to https, please contact the server administrator.');

    ################    Session     ####################
    #if (!SOCKET)
    new \Modules\Session();

    Session::updateCallback($PHP['SESSION_UPDATE_CALLBACK']); // Pull From Database, manage socket ip

    forward_static_call_array(['Modules\Helpers\Serialized', 'start'], $PHP['SERIALIZE']);    // Pull theses from session, and store on shutdown

    ################    Reporting   ####################
    date_default_timezone_set('America/Phoenix');

    error_reporting($PHP['REPORTING']['LEVEL']);

    ini_set('display_errors', 1);

    $_SESSION['id'] = array_key_exists('id', $_SESSION ?? []) ? $_SESSION['id'] : false;

    define('ERROR_LOG', SERVER_ROOT . 'Data/Logs/Error/Log_' . $_SESSION['id'] . '_' . time() . '.log');
    define('FULL_REPORTS', $PHP['REPORTING']['FULL'] ?? true);


    ErrorCatcher::start();    // Catch application errors and lo


    function dump(...$argv)
    {
        echo '<pre>';
        var_dump(count($argv) == 1 ? array_shift($argv) : $argv);
        echo '</pre>';
    }

    function sortDump($mixed, $fullReport = false, $die = true)
    {
        // Notify or error
        alert(__FUNCTION__);

        // Generate Report
        ob_start();
        echo '####################### VAR DUMP ########################<br><pre>';
        var_dump($mixed);
        echo '</pre><br><br><br>';
        if ($fullReport) {
            echo '####################### MIXED DUMP ########################<br><pre>';
            $mixed = (is_array($mixed) && count($mixed) == 1 ? array_pop($mixed) : $mixed);
            echo '<pre>';
            //debug_zval_dump( $mixed ?: $GLOBALS );
            echo '</pre><br><br>';
            echo '####################### BACK TRACE ########################<br><pre>';
            var_dump(debug_backtrace());
            echo '</pre>';
        };

        $report = ob_get_clean();
        // Output to file
        $file = fopen(SERVER_ROOT . 'Data/Logs/Dumped/Sort_' . time() . '.log', "a");
        fwrite($file, $report);
        fclose($file);

        print $report . PHP_EOL;

        // Output to browser
        // $view = \View\View::getInstance();
        //if ($view->ajaxActive()) echo $report;
        // else $view->currentPage = base64_encode( $report );
        if ($die) exit(1);
    }

    function alert($string = "Stay woke.")
    {
        static $count = 0;
        $count++;
        print "<script>alert('( #$count )  $string')</script>";
    }

    // http://php.net/manual/en/debugger.php
    function console_log($data)
    {
        ob_start();
        echo $data;
        $report = ob_get_clean();
        $file = fopen(SERVER_ROOT . 'Data/Logs/Log_' . time() . '.log', "a");
        fwrite($file, $report);
        fclose($file);
        echo '<script>console.log(\'' . json_encode($data) . '\')</script>';
    }


    return function () {
        startApplication();
    }; // HTTP , AJAX, PJAX.. AKA NOT SOCKET
}

#################   Development   ######################
/**
 * This will run the application in an MVC style.
 * The classes will be defined in the the first param while
 * the method inside the second param.
 *
 * The flow of the application is
 *
 *  `Controller` -> `Model` -> `View`
 *
 * The controller should work with the request class to help validate all data
 * received from the user. If all data is preceived to be valid (by type) then
 * we should return true or a value which will evaluate to true with (==) double equal,
 * else false.
 *
 * The model will only be fetched and method executed if the previous controller
 * returns true (or mixed). The mixed value will be passed to the models constructor and method.
 * This will communicate with the database (if applicable) to
 * further validate, update, or fetch information.
 *
 * If no errors have been raised then the view will be executed. We created a self
 * stored instance in the index to handle the request throughout the application. This
 * allows us to call the view->contents() method statically even though it is defined as
 * a private method.
 *
 * Singletons functionality in the view ensures constructor is called and reset
 * when needed & in conjunction to any sterilized data also present.
 *
 * We keep the contents function private and call this way to allow frontend developers
 * to include global valuables using `$this->`
 *
 * @param $class string Name of the class within the controller and model folder
 *  and a folder name in the CONTENT_ROOT
 *
 * @param $method string Name of the method in the above parameter, and file name
 *  for the template.
 *
 * @return void The View->contents() procedure will exit(1)
 */


##################   DEV Tools   #################
// This will cleanly print the var_dump function and kill the execution of the application

/**
 * This will cleanly print the var_dump function and kill the execution of the application.
 *
 * This function is for development purposes. The function accepts one value to printed on
 * the browser. If the value passes is empty or null the function will print all variables
 * in the $GLOBAL scope.
 *
 * @param mixed $mixed Will be run throught the var_dump function.
 *
 * @return die(1);
 */


/**
 * This ports the javascript alert function to work in PHP. Note output is sent to the browser
 *
 * @param string $string will be placed in the javascript alert function.
 *
 * @return null
 */


