<?php

namespace Controller;

use Modules\Request;
use Modules\Singleton;

class User
{
    use Singleton;

    private $request;

    public function __construct()
    {
        $this->request = new Request;
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
        unset($GLOBALS['user']);        // if the destructor is called we want to make sure any sterilized data is then removed
        \Model\User::clearInstance();   // remove sterilized data
        $_SESSION['id'] = false;
        unset($_SESSION['id']);
        startApplication(true);
    }
    
    public function login()
    {
        if (isset($this->client) && $this->client == "clear") {
            $this->request->cookie()->except( 'PHPSESSID' )->clearCookies();
            return false;
        }

        list($this->UserName, $this->FullName, $this->UserImage)
            = $this->request->cookie('UserName', 'FullName', 'UserImage')->value();

        if (empty($_POST))
            return false;  // If forum already submitted

        $this->username = $this->request->post( 'username' )->alnum();
        $this->password = $this->request->post( 'password' )->value();
        $this->rememberMe = $this->request->post('RememberMe')->int();

        if (!$this->rememberMe) $this->request->cookie()->except('PHPSESSID')->clearCookies();

        if (!$this->username || !$this->password) {
            $this->alert['warning'] = 'Sorry, but we need your username and password.';
            return false;
        } return true;
    }

    public function joinTeam()
    {
        if (empty($_POST))
            return false;
        if (!$this->teamCode = $this->request->post( 'teamCode' )->alnum()) {
            $this->alert['warning'] = "Sorry, your team code appears to be invalid";
            return false;
        } return true;
    }

    public function facebook()
    {
        if ((include SERVER_ROOT . 'Application/Services/Social/fb-callback.php') == false)
            $this->alert = 'Sorry, we could not connect to Facebook. Please try again later.';
        else return true;
        return startApplication(true);     // This will load the login page
    }

    public function register()
    {
        if (empty($_POST))
            return false;

        list($this->username, $this->firstName, $this->lastName, $this->gender, $this->userType, $this->teamCode)
            = $this->request->post( 'username', 'firstname', 'lastname', 'gender', 'UserType', 'teamCode')->alnum();

        list($this->teamName, $this->schoolName)
            = $this->request->post( 'teamName', 'schoolName' )->text();
        
        list($this->password, $verifyPass )
            = $this->request->post( 'password', 'password2' )->value();  // unsanitized

        $this->email = $this->request->post( 'email' )->email();

        $terms = $this->request->post('Terms')->int();

        if (!$this->username)
            $this->alert['warning'] = 'Please enter a username with only numbers & letters!';

        elseif (!$this->gender)
            $this->alert['warning'] = 'Sorry, please enter your gender.';

        elseif (!$this->userType || !($this->userType == 'Coach' || $this->userType == 'Athlete') )
            $this->alert['warning'] = 'Sorry, please choose an account type. This can be changed later in the web application.';

        elseif ($this->userType == "Coach" && !$this->teamName)
            $this->alert['warning'] = "Sorry, the team name you have entered appears invalid.";

        elseif (!$this->password || ($len = strlen( $this->password )) < 6 || $len > 16)
            $this->alert['warning'] = 'Sorry, your password must be between 6 and 16 characters!';

        elseif ($this->password != $verifyPass)
            $this->alert['warning'] = 'The passwords entered must match!';

        elseif (!$this->email)
            $this->alert['warning'] = 'Please enter a valid email address!';

        elseif (!$this->firstName)
            $this->alert['warning'] = 'Please enter your first name!';

        elseif (!$this->lastName)
            $this->alert['warning'] = 'Please enter your last name!';

        elseif (!$terms)
            $this->alert['warning'] = 'You must agree to the terms and conditions.';
        else return true;
        return false;
    }
    
    public function activate() 
    {
        $this->email = $this->request->set( $this->email )->email();
        $this->email_code = $this->request->set( $this->email_code )->value();

        if (!$this->email || !$this->email_code)
            $this->alert['warning'] = 'Sorry the url submitted is invalid.';
        else return true;
        return false;
    }

    public function recover()
    {
        $this->email = $this->request->post( 'email' )->email();

        if ($this->email) $this->alert['warning'] = 'You have entered an invalid email address.';

        if (isset($this->alert) === true) $this->parameter = 'verify';
    }

    public function profile()
    {
        $this->alert['warning'] = "There are over seven million high school student-athletes in the United States. Standing out as a athlete can be difficult, but made easier with the paired accompaniments in your academia. The information you present here should be considered public, to be seen by peers and coaches alike; so please keep it classy.";
        return false;
    }

}



