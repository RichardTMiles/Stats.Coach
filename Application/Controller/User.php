<?php

namespace Controller;

use Modules\Error\PublicAlert;
use Modules\Request;

class User extends Request
{
    public static function logout()
    {
        unset($GLOBALS['user']);        // if the destructor is called we want to make sure any sterilized data is then removed
        \Model\User::clearInstance();   // remove sterilized data
        session_unset();                // This wont clear the user session row, just data in row
        session_destroy();
        #session_regenerate_id( TRUE );
        $_SESSION['id'] = false;
        startApplication( true );
    }

    public function login($client = null)
    {
        global $UserName, $FullName, $UserImage;    // validate cookies

        switch ($this->set( $client )->alnum()) {
            case "clear":
                $this->cookie( 'UserName', 'FullName', 'UserImage' )->clearCookies();
                return false;
            case 'FaceBook':

                alert('hellllo');
                $this->facebook();
                //sortDump($GLOBALS);
            default:
        }

        list($UserName, $FullName) = $this->cookie( 'UserName', 'FullName' )->alnum();

        $UserImage = $this->cookie( 'UserImage' )->value();

        $UserImage = file_exists( SERVER_ROOT . $UserImage ) ? SITE . $UserImage : false;

        $rememberMe = $this->post( 'RememberMe' )->int();

        if (!$rememberMe)
            $this->cookie( 'username', 'password', 'RememberMe' )->clearCookies();

        if (empty($_POST)) return false;  // If forum already submitted

        $username = $this->post( 'username' )->alnum();

        $password = $this->post( 'password' )->value();

        if (!$username || !$password)
            throw new PublicAlert( 'Sorry, but we need your username and password.' );

        return [$username, $password, $rememberMe];
    }

    public function facebook()
    {
        if ((include SERVER_ROOT . 'Application/Services/Social/fb-callback.php') == false)
            throw new PublicAlert( 'Sorry, we could not connect to Facebook. Please try again later.' );


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

        if (!$email) {
            PublicAlert::warning( 'Sorry the url submitted is invalid.' );
            return startApplication( true ); // who knows what state we're in, best just restart.
        }
        return [$email, $email_code];
    }

    public function recover($user_email = null, $user_generated_string = null)
    {
        if (!empty($user_email) && !empty($user_generated_string)) {

            list($user_email, $user_generated_string) = $this->set( $user_email, $user_generated_string )->base64_decode()->value();

            if (!$this->set( $user_email )->email()) throw new PublicAlert( 'The code provided appears to be invalid.' );

            return [$user_email, $user_generated_string];
        }

        if (empty($_POST)) return false;

        if (!$this->user_email = $this->post( 'user_email' )->email())
            throw new PublicAlert( 'You have entered an invalid email address.' );
        else return [$this->user_email, false];
    }

    public function profile($user_id = false)
    {
        if ($user_id) return $this->set( $user_id )->alnum();

        if (empty($_POST)) return false;                // dont go onto the model

        if (!$this->post( 'Terms' )->int())
            throw new PublicAlert( 'Sorry, you must accept the terms and conditions.', 'warning' );

        global $first, $last, $email, $gender, $dob, $password, $profile_pic, $about_me;

        list($first, $last, $gender) = $this->post( 'first_name', 'last_name', 'gender' )->word();

        $dob = $this->post( 'datepicker' )->date();

        $email = $this->post( 'email' )->email();

        $password = $this->post( 'password' )->value();

        $about_me = $this->post( 'about_me' )->text();

        $profile_pic = $this->files( 'FileToUpload' )->storeFiles( 'Data/Uploads/Pictures/Profile/' );

        return true;
    }

    public function settings()
    {
        return false;
    }

}



