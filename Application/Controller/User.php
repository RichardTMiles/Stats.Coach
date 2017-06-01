<?php

namespace Controller;

use Model\Helpers\UserRelay;
use Modules\Request;
use Psr\Singleton;
use View\View;

class User
{
    use Singleton;

    private $request;

    public function __construct()
    {
        $this->request = new Request;
    }

    ############################## These function depend on each other
    public static function getApp_id(callable $callable = null)
    {
        return (array_key_exists('id', $_SESSION) ? (is_callable( $callable ) ? $callable() : $_SESSION['id']): false);
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
        $_SESSION['id'] = false;
        if (WRAPPING_REQUIRES_LOGIN) View::newInstance();
        startApplication();
    }
    ############################## / end dependency


    public function login()
    {
        if (empty($_POST)) return false;  // If forum already submitted

        $this->username = $this->request->post( 'username' )->alnum();
        $this->password = $this->request->post( 'password' )->value();

        if (!$this->username || !$this->password) {
            $this->alert = 'Sorry, but we need your username and password.';
            return false;
        } return true;
    }


    public function register()
    {
        if (empty($_POST))
            return false;

        list($this->username, $this->firstName, $this->lastName) = $this->request->post( 'username', 'firstname', 'lastname' )->alnum();
        list($this->password, $verifyPass )= $this->request->post( 'password', 'password2' )->value();  // unsanitized
        $this->email = $this->request->post( 'email' )->email();
        $terms = $this->request->post('Terms')->int();

        //sortDump($terms);

        if (!$this->username)
            $this->alert = 'Please enter a username with only numbers & letters!';

        elseif (!$this->password && $len = strlen( $this->password ) < 6 && $len > 16)
            $this->alert = 'Your password must be between 6 and 16 characters!';

        elseif ($this->password != $verifyPass)
            $this->alert = 'The passwords entered must match!';

        elseif (!$this->email)
            $this->alert = 'Please enter a valid email address!';

        elseif (!$this->firstName)
            $this->alert = 'Please enter your first name!';

        elseif (!$this->lastName)
            $this->alert = 'Please enter your last name!';

        elseif (!$terms)
            $this->alert = 'You must agree to the terms and conditions.';
        else return true;
        return false;
    }

    public function recover($id = null)
    {
        $this->email = $this->request->post( 'email' )->email();

        if (empty($this->email)) $this->alert = 'You have entered an invalid email address.';

        if (isset($this->alert) === true) $this->parameter = 'verify';
    }

    public function profile($user = null)
    {
        return true;
    }

}



