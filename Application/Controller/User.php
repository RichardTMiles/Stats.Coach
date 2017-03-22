<?php

namespace Controller;

use Modules\Request;
use Psr\Singleton;

class User
{
    use Singleton;

    private $request;


    public function __construct()
    {
        $this->request = Request::getInstance();
    }

    public static function loggedIn()
    {
        return (isset($_SESSION['id']) ? $_SESSION['id'] : false);
    }

    public static function loggedOut()
    {
        if (self::loggedIn()) {
            echo '<script type="text/javascript"> window.location ='. SITE_ROOT .'</script>';
            exit();
        }
    }

    public static function protectPage()
    {
        if (!self::loggedIn()) self::logout();
    }


    public function login()
    {
        if (!empty($_POST)) {   // If forum already submitted

            $this->username = $this->request->post( 'username' )->alnum();
            $this->password = $this->request->post( 'password' )->alnum();

            if (!isset($this->username) || !isset($this->password)) {
                $this->alert = 'Sorry, but we need your username and password.';
            } else {
                $this->state = 'verify';      // We can now check server side
            }
        }
    }

    // If one of these doesn't work... You're doing something wrong
    // header( "LOCATION: http://example.com/" );  // Note that this will not change url / state
    // <head><meta http-equiv="refresh" content="2;url=http://example.com" /></head>
    // <script type="text/javascript"> window.location = "http://www.example.com/" </script>

    public static function logout()
    {
        session_unset();
        session_destroy();
        session_start();
        echo '<head><meta http-equiv="refresh" content="2;url='. SITE_ROOT .'Login/" />
        <script type="text/javascript"> window.location = "'. SITE_ROOT .'Login/" </script></head>';
        die();
    }

    private function register()
    {
        $this->register = false;
        if (!empty($_POST)) {
            $this->username = $this->request->post( 'username' )->alnum();
            $this->password = $this->request->post( 'password' )->value();  // unsanitized
            $this->email    = $this->request->post( 'email'    )->email();
            $this->firstName= $this->request->post( 'firstname')->alnum();
            $this->lastName = $this->request->post( 'lastname' )->alnum();


            if (!isset($this->username)) {
                $this->alert = 'Please enter a Username with only numbers & letters!';

            } elseif (!isset($this->password) && $len = strlen($this->password) < 6 && $len > 16) {
                $this->alert = 'Your password must be between 6 and 16 characters!';

            } elseif (!isset($this->email)) {
                $this->alert = 'Please enter a valid email address!';

            } elseif (!isset($this->firstName)) {
                $this->alert = 'Please enter your first name!';

            } elseif (!isset($this->lastName)) {
                $this->alert = 'Please enter your last name!';
            } else { $this->register = true; }

        }
    }

    public function recover($id = null)
    {
        $this->email = $this->request->post( 'email' )->email();

        if (empty($this->email)) $this->alert = 'You have entered an invalid email address.';

        if (isset($this->alert) === true) $this->parameter = 'verify';
    }

    public function profile($user = null)
    {
        // validate something
    }

}



