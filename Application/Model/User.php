<?php

namespace Model;

use Model\Helpers\UserRelay;
use Modules\StoreFiles;
use Modules\Request;
use Psr\Singleton;


class User extends UserRelay
{
    use Singleton;

    private function ajaxLogin_Support($id = false)
    {
        if (!$id) return false;
        
        if (!isset($this->user_username))
            $this->userSQL( $this->user_id );   // populates the global and


        if (isset($this->user_username) && !array_key_exists( 'user_username', $GLOBALS )) {
            if (empty($this->user_full_name))
                $this->user_full_name = $this->user_first_name . ' ' . $this->user_last_name;

            foreach (get_object_vars( $this ) as $key => $var)
                $GLOBALS[$key] = $this->$key = $var;

            // Ajax makes life a little hard when pressing the back button
            // Backing into a previous post state is a thing is a problem..
            if (!array_key_exists( "username", $_POST )) return true;
            // We came from the login page?
            $_POST["username"] = null;
            $_POST["password"] = null;
            unset($_POST);
        }
        return true;
    }
    
    public function login()
    {
        if (isset($this->facebook)) return $this->facebook();

        try {
            if (!parent::user_exists( $this->username ))
                throw new \Exception( 'Sorry, this Username and Password combination doesn\'t match out records.' );

            if (!parent::email_confirmed( $this->username ))
                throw new \Exception( 'Sorry, you need to activate your account. Please check your email!' );

            // If ->login() fails exception is thrown
            parent::loginSQL( $this->username, $this->password );   // This will call userProfile()

            session_regenerate_id(true);

            if ($this->rememberMe) {
                Request::setCookie( "UserName",  $this->user_username);
                Request::setCookie( "FullName",  $this->user_full_name);
                Request::setCookie( "UserImage",  $this->user_profile_pic);
            } else {
                Request::setCookie( "UserName",  "", -1);
                Request::setCookie( "FullName",  "", -1);
                Request::setCookie( "UserImage",  "", -1);
            }
            startApplication(true);     // restart
            
        } catch (\Exception $e) {
            $this->alert = $e->getMessage();
        }

    }

    public function facebook()
    {
        try {
            if ($this->email_exists( $this->facebook['email'] )) {
                $_SESSION['id'] = $this->fetchSQL( 'user_id', 'user_email', $this->facebook['email'] )['user_id'];
                    $this->userSQL($_SESSION['id']);
                if ($this->user_facebook_id == null)
                    //$this->update_user();
                sortDump($this->facebook);
            } else {
                
            }
        } catch (\Exception $e) {
            throw new \Exception( 'Sorry, there appears to be an error in Facebook SDK.' );
        }
    }

    public function register()
    {

        try {
            if (!parent::user_exists( $this->username ))
                throw new \Exception ( 'That username already exists' );

            if (!parent::email_exists( $this->email ))
                throw new \Exception ( 'That email already exists.' );


            parent::registerSQL( $this->username, $this->password, $this->email, $this->firstName, $this->lastName );

            parent::loginSQL( $this->username, $this->password );
            
            startApplication(true);

        } catch (\Exception $e) {
            $this->alert = $e->getMessage();
        }
    }

    public function activate()
    {
        // Need to validate the success with database
        try {
            if (!parent::email_exists( $this->email ))
                throw new \Exception( 'Please make sure the Url you have entered is correct.' );

            if (!$this->relay->activate( $this->email, $this->email_code ))      //Push to server - run activate
                throw new \Exception( 'Sorry, we have failed to activate your account' );


            $login = parent::fetchSQL( 'id', 'email', $this->email );

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
                if (!parent::email_exists( $email ))
                    throw new \Exception ( "Sorry, we have detected an invalid url. Please contact us for further support." );

                if (!parent::recoverSQL( $email, $unique ))
                    throw new \Exception ( "Sorry, something went wrong and we could not recover your password." );

                return header( "LOCATION: http://Stats.Coach/login/recover/" ); // TODO - start app compat.
                // throw new /Exception ('');

            }
            if (isset($email) === true) {   // and only email

                if (!parent::email_exists( $email ))
                    throw new \Exception ( 'Sorry, that email doesn\'t exist.' );

                if (!parent::confirm_recover( $email ))     // Sends Email  // if didn't work
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


