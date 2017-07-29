<?php

namespace Controller;

use Modules\Helpers\Reporting\PublicAlert;
use Modules\Request;

class User extends Request
{
    public static function logout()
    {
        unset($GLOBALS['user']);        // if the destructor is called we want to make sure any sterilized data is then removed
        \Model\User::clearInstance();   // remove sterilized data
        session_unset();
        session_destroy();
        session_start();
        $_SESSION = [];
        startApplication( 'login/' );
    }

    public function login($client = null)
    {
        switch ($this->set($client)->alnum()) {
            case "clear":
                $this->cookie( 'UserName', 'FullName', 'UserImage' )->clearCookies();
                return false;
            case 'FaceBook':
                return $this->facebook();
            default:

        }

        list($UserName, $this->FullName, $UserImage)
            = $this->cookie( 'UserName', 'FullName', 'UserImage' )->alnum();

        if (empty($_POST)) return false;  // If forum already submitted

        $username = $this->post( 'username' )->alnum();
        $password = $this->post( 'password' )->value();
        $rememberMe = $this->post( 'RememberMe' )->int();

        if (!$rememberMe) {
            $this->cookie( 'username', 'password', 'RememberMe' )->clearCookies();
        }

        if (!$username || !$password)
            throw new PublicAlert('Sorry, but we need your username and password.');

        return [$username, $password, $rememberMe];
    }

    public function createTeam()
    {
        if (empty($_POST)) return false;
        list($teamName, $schoolName) = $this->post( 'teamName', 'schoolName' )->text();
        return (empty($teamName) || empty($schoolName)) ? [$teamName, $schoolName] : false;
    }

    public function joinTeam()
    {
        if (empty($_POST)) return false;

        if (!$teamCode = $this->post( 'teamCode' )->alnum())
            PublicAlert::warning("Sorry, your team code appears to be invalid");
        
        return $teamCode;
    }

    public function facebook()
    {
        if ((include SERVER_ROOT . 'Application/Services/Social/fb-callback.php') == false)
            throw new PublicAlert('Sorry, we could not connect to Facebook. Please try again later.');
        return true;
    }

    public function register()
    {
        if (empty($_POST)) return false;

        list($this->username, $this->firstName, $this->lastName, $this->gender, $this->userType, $this->teamCode)
            = $this->post( 'username', 'firstname', 'lastname', 'gender', 'UserType', 'teamCode' )->alnum();

        list($this->teamName, $this->schoolName)
            = $this->post( 'teamName', 'schoolName' )->text();

        list($this->password, $verifyPass)
            = $this->post( 'password', 'password2' )->value();  // unsanitized

        $this->email = $this->post( 'email' )->email();

        $terms = $this->post( 'Terms' )->int();

        if (!$this->username)
            $this->alert['warning'] = 'Please enter a username with only numbers & letters!';

        elseif (!$this->gender)
            $this->alert['warning'] = 'Sorry, please enter your gender.';

        elseif (!$this->userType || !($this->userType == 'Coach' || $this->userType == 'Athlete'))
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

    public function activate($email, $email_code = null)
    {
        $email = $this->set( $email )->base64_decode()->email();
        $email_code = $this->set( $email_code )->base64_decode()->value();

        if (!$email){
            PublicAlert::warning( 'Sorry the url submitted is invalid.' );
            return startApplication( true ); // who knows what state we're in, best just restart.
        } return [$email, $email_code];
    }

    public function recover($user_email = null, $user_generated_string = null)
    {
        if (!empty($user_email) && !empty($user_generated_string)){

            list($user_email, $user_generated_string) = $this->set( $user_email, $user_generated_string )->base64_decode()->value();

            if (!$this->set( $user_email )->email()) throw new PublicAlert('The code provided appears to be invalid.');

            return [$user_email, $user_generated_string];
        }

        if (empty($_POST)) return false;
        
        if (!$this->user_email = $this->post( 'user_email' )->email())
            throw new PublicAlert('You have entered an invalid email address.');
        else return [$this->user_email, false];
    }

    public function profile($user_id)
    {
        global $first, $last, $email, $gender, $dob, $password, $profile_pic, $about_me;

        if ($user_id) return $this->set( $user_id )->alnum();

        if (empty($_POST)) return false;

        list($first, $last, $gender) = $this->post('first_name','last_name', 'gender')->word();

        $dob = $this->post( 'datepicker' )->date();

        $email = $this->post( 'email' )->email();

        $password = $this->post( 'password' )->value();

        $about_me = $this->post( 'about_me' )->text();

        // if file was attached
        if ($_FILES['FileToUpload']['error'] != UPLOAD_ERR_NO_FILE)
            $profile_pic = $this->files( 'FileToUpload' )->storeFiles();
        
        return true;
    }

    public function settings()
    {
        if ($this->user->email_confirmed == 0)
            $this->alert['warning'] = "There are over seven million high school student-athletes in the United States. Standing out as a athlete can be difficult, but made easier with the paired accompaniments in your academia. The information you present here should be considered public, to be seen by peers and coaches alike; so please keep it classy.";
        return false;
    }

}



