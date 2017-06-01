<?php

namespace Model;

use Model\Helpers\UserRelay;
use Modules\Route;
use Modules\StoreFiles;
use Psr\Singleton;


class User
{
    use Singleton;

    private $relay;

    public function __construct()
    {
        $this->relay = UserRelay::getInstance();
    }
 
    public function login()
    {

       
        try {
            if (!$this->relay->user_exists( $this->username ))
                throw new \Exception( 'Sorry, this Username and Password combination doesn\'t match out records.' );

            if (!$this->relay->email_confirmed( $this->username ))
                throw new \Exception( 'Sorry, you need to activate your account. Please check your email!' );

            // If ->login() fails exception is thrown
            $this->relay->login( $this->username, $this->password );

            $this->relay->userProfile($_SESSION['id']);

            // restart();

            alert("LOGGED IN YO");

            startApplication();     // restart
            
        } catch (\Exception $e) {
            $this->alert = $e->getMessage();
        }
    }

    public function register()
    {
        try {
            if ($this->relay->user_exists( $this->username ))
                throw new \Exception ( 'That username already exists' );

            if ($this->relay->email_exists( $this->email ))
                throw new \Exception ( 'That email already exists.' );


            $this->relay->register( $this->username, $this->password, $this->email, $this->firstName, $this->lastName );

            $this->relay->login( $this->username, $this->password );
            
            startApplication();

        } catch (\Exception $e) {
            $this->alert = $e->getMessage();
        }
    }

    public function activate()
    {
        // Need to validate the success with database
        try {
            if (!$this->relay->email_exists( $this->email ))
                throw new \Exception( 'Please make sure the Url you have entered is correct.' );

            if (!$this->relay->activate( $this->email, $this->email_code ))      //Push to server - run activate
                throw new \Exception( 'Sorry, we have failed to activate your account' );


            $login = $this->relay->fetch_info( 'id', 'email', $this->email );

            session_destroy();
            session_regenerate_id( true );

            $_SESSION['id'] = $login;

            header( 'Location:' . SITE_ROOT );

        } catch (\Exception $e) {
            $this->alert = $e->getMessage();
        }
    }

    public function recover()
    {
        try {
            if (isset($parameter) & isset($unique)) {
                if (!$this->relay->email_exists( $email ))
                    throw new \Exception ( "Sorry, we have detected an invalid url. Please contact us for further support." );

                if (!$this->relay->recover( $email, $unique ))
                    throw new \Exception ( "Sorry, something went wrong and we could not recover your password." );

                return header( "LOCATION: http://Stats.Coach/login/recover/" );
                // throw new /Exception ('');

            }
            if (isset($email) === true) {   // and only email

                if (!$this->relay->email_exists( $email ))
                    throw new \Exception ( 'Sorry, that email doesn\'t exist.' );

                if (!$this->relay->confirm_recover( $email ))     // Sends Email  // if didn't work
                    throw new \Exception ( 'Sorry, we are having an internal error. Please contact us for more support.' );

                return header( "LOCATION: http://Stats.Coach/login/sent/" ); // This Re-directs to new url/ No $_Post

            }
        } catch (\Exception $e) {
            $this->alert = $e->getMessage();
        }
        $view = 'recover';
    }

    public function profile($id = null)
    {
        $pageTitle = "Profile";

        // TODO - Delete the old user image, Complete the full forum submit
        /*
        if (!empty($_POST)) {
            if ('false' == $filePath = new StoreFiles( 'FileToUpload', 'Data/Uploads/Pictures/' )) {
                echo "File Upload Fail";
                die();
            } else if (!empty($user_id)) {
                $this->relay->updateRow( "UPDATE users SET user_profile_pic = ? WHERE user_id = ?", array($filePath, $user_id) );
                $user_profile_pic = $filePath;
            }
        }
        */


    }

}


