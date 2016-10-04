<?php

namespace App\Models;

use App\ApplicationModel as Model;
use \App\Modules\Models\StoreFiles;
use App\Views\View;

// Stats.Coach / $controller / $action / $parameter / $unique / $id /

class Users extends Model
{
    // User Relay is a service layer,
    // bridge the gap between model and database

    public function login()
    {
        extract( $this->data );

        //Full view is set to home, because this view is considered apart of the main template

        switch ($parameter) {
            case 'sent':
                $alert[] = 'We have sent you a email containing steps to reset your password.';
                break;
            case 'recover':
                $alert[] = "Success, a new password has been sent to your email.";
                break;
            case 'verify':
                try {
                    if (!$this->UserRelay->user_exists( $username ))
                        throw new \Exception( 'Sorry, this Username and Password combination doesn\'t match out records.' );
                    if (!$this->UserRelay->email_confirmed( $username ))
                        throw new \Exception( 'Sorry, you need to activate your account. Please check your email!' );

                    // If ->login() fails exception is thrown from $this->UserRelay->login()
                    $login = $this->UserRelay->login( $username, $password ); //  } else { return $id;

                    session_regenerate_id( true ); // destroying the old session id and creating a new one

                    $_SESSION['id'] = $login;    // Set out login to true

                    return header( "LOCATION: http://Stats.Coach/" );

                } catch (\Exception $e) {
                    $alert[] = $e->getMessage();
                    $view = 'login';
                }
                break;
            default:
                $view = 'login';
        }
        return new View( compact( array_keys( get_defined_vars() ) ) );
    }


    protected function register($username, $email, $password, $firstName)
    {
        extract( $this->data );

        switch ($parameterr) {
            case 'verify':
                try {
                    if (!$this->UserRelay->user_exists( $username ))
                        throw new \Exception ( 'That username already exists' );

                    if (!$this->UserRelay->email_exists( $email ))
                        throw new \Exception ( 'That email already exists.' );

                    $this->UserRelay->register( $username, $password, $email, $firstName, $lastName );

                    $parameter = 'sent';
                    $this->data = compact( array_keys( get_defined_vars() ) );
                    return $this->login();

                } catch (\Exception $e) {
                    $alert[] = $e->getMessage();
                }
                break;
            default:
                $view = 'register';
        }

        return new View( compact( array_keys( get_defined_vars() ) ) );

    }

    protected function activate()
    {
        extract( $this->data );
        // Stats.Coach / $controller / $action / $parameter / $unique / $id /
        // Need to validate the success with database

        // This is the case of id not being set
        $email = trim( $parameter );
        $email_code = trim( $unique );

        try {
            if (!$this->UserRelay->email_exists( $email ))
                throw new \Exception( 'Please make sure the Url you have entered is correct.' );

            if (!$this->UserRelay->activate( $email, $email_code )) {       //Push to server - run activate
                throw new \Exception( 'Sorry, we have failed to activate your account' );
            }

            $login = $this->UserRelay->fetch_info( 'id', 'email', $email );

            session_destroy();

            session_regenerate_id( true );   // destroying the old session id and creating a new one
            $_SESSION['id'] = $login;
            header( 'Location:' . SITE_ROOT );

        } catch (\Exception $e) {
            $alert[] = $e->getMessage();
        }

        $view = 'recover';
        return new View( compact( array_keys( get_defined_vars() ) ) );
    }


    protected function recover()
    {
        extract( $this->data );
        try {
            if (isset($parameter) & isset($unique)) {
                if (!$this->UserRelay->email_exists( $email ))
                    throw new \Exception ( "Sorry, we have detected an invalid url. Please contact us for further support." );

                if (!$this->UserRelay->recover( $email, $unique ))
                    throw new \Exception ( "Sorry, something went wrong and we could not recover your password." );

                return header( "LOCATION: http://Stats.Coach/users/login/recover/" );
                // throw new /Exception ('');

            }
            if (isset($email) === true) {   // and only email

                if (!$this->UserRelay->email_exists( $email ))
                    throw new \Exception ( 'Sorry, that email doesn\'t exist.' );

                if (!$this->UserRelay->confirm_recover( $email ))     // Sends Email  // if didn't work
                    throw new \Exception ( 'Sorry, we are having an internal error. Please contact us for more support.' );

                return header( "LOCATION: http://Stats.Coach/users/login/sent/" ); // This Re-directs to new url/ No $_Post

            }
        } catch (\Exception $e) {
            $alert[] = $e->getMessage();
        }
        $view = 'recover';
        return new View( compact( array_keys( get_defined_vars() ) ) );
    }

    protected function profile()
    {
        extract( $this->data );
        $pageTitle = "Profile";


        // TODO - Delete the old user image, Complete the full forum submit
        /**
         *  I've decided to use the already gotten variables as bench marks for the full profile update
         *  UserRelay has already taken all the profile data, see is new $_Postdata[] is not empty...
         */
        if (!empty($_POST)) {
            if ('false' == $filePath = new StoreFiles( 'FileToUpload', 'Data/Uploads/Pictures/' )) {
                echo "File Upload Fail";
                die();
            } else if (!empty($user_id)) {
                $this->UserRelay->updateRow( "UPDATE users SET user_profile_pic = ? WHERE user_id = ?", array($filePath, $user_id) );
                $user_profile_pic = $filePath;
            }
        }


        return new View( compact( array_keys( get_defined_vars() ) ) );
    }
}


