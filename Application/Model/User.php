<?php

namespace Model;

use Model\Helpers\UserRelay;
use Modules\Database;
use Modules\Helpers\Bcrypt;
use Modules\StoreFiles;
use Modules\Request;
use Psr\Singleton;


class User extends UserRelay
{
    use Singleton;


    protected $db;

    /**
     * @return object|User
     *
     */
    private function ajaxLogin_Support()
    {   // the constructor or wake will be called first
        if(empty($this->user_id)):
            $this->user_id = false;
        elseif (!isset($this->user_username)):
            return $this->getUser();   // returns this object populated with data
        endif; // $this will be returned through singleton
    }

    public function __construct()
    {
        $this->user_id = (array_key_exists( 'id', $_SESSION ) ? $_SESSION['id'] : false);
        $this->db = Database::getConnection();  // establish a connection
        // Reconfig variables for dynamic path
        if (!$this->user_id) return;
        $this->user_profile_pic = SITE_PATH . $this->user_profile_pic;
        $this->user_cover_photo = SITE_PATH . $this->user_cover_photo;
        $this->user_full_name = $this->user_first_name . ' ' . $this->user_last_name;
    }

    public function __wakeup()
    {
        $this->db = Database::getConnection();  // establish a connection
    }

    public final function __sleep()
    {
        return (!empty($this->user_username) ? array_keys( get_object_vars( $this ) ) : 0 ); //? array('user_id','user_username', 'user_first_name', 'user_last_name', 'user_full_name', 'user_profile_pic', 'user_cover_photo', 'user_birth_date', 'user_gender', 'user_bio', 'user_rank', 'user_email', 'user_email_code', 'user_email_confirmed', 'user_generated_string', 'user_membership', 'user_deactivated', 'user_creation_date', 'user_ip') : 0);
    }

    protected function getUser()
    {
        if (!array_key_exists( 'id', $_SESSION )) throw new \Exception('nope bad id');
        // In theory this request is only called once per session.
        try {
            $stmt = $this->db->prepare( 'SELECT * FROM StatsCoach.user WHERE user_id = ?' );
            $stmt->setFetchMode( \PDO::FETCH_CLASS, User::class );
            $stmt->execute( [$_SESSION['id']] );
            $stmt = $stmt->fetch();                 // user obj
            $stmt->user_id = $_SESSION['id'];
            return $stmt;
        } catch (\Exception $e) {
            alert($e->getMessage());
        }
    }

    protected function fetchSQL($what, $field, $value)
    {
        $allowed = array('user_id', 'user_profile_pic', 'user_username', 'user_full_name', 'user_first_name', 'user_last_name', 'user_gender', 'user_bio', 'user_email');
        if (!in_array( $what, $allowed, true ) || !in_array( $field, $allowed, true ))
            throw new \InvalidArgumentException;

        $sql = "SELECT $what FROM StatsCoach.user WHERE $field = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($value) );
        return $stmt->fetch();

    } // Returns only one value from the db

    protected function updateUser()
    {
        return $this->db->prepare( "UPDATE StatsCoach.user SET 
                                    user_facebook_id = ?, 
                                    user_username = ?, 
                                    user_first_name = ?, 
                                    user_last_name = ?, 
                                    user_profile_pic = ?,
                                    user_cover_photo = ?,
                                    user_birth_date = ?,
                                    user_gender = ?, 
                                    user_bio = ?,
                                    user_rank = ?,
                                    user_email = ?
                                    WHERE user_id = ?" )
            ->execute( [$this->user_facebook_id,
                $this->user_username,
                $this->user_first_name,
                $this->user_last_name,
                $this->user_profile_pic,
                $this->user_cover_photo,
                $this->user_birth_date,
                $this->user_gender,
                $this->user_bio,
                $this->user_rank,
                $this->user_email,
                $this->user_id] );
    }

    protected function change_password($user_id, $password)
    {   /* Two create a Hash you do */
        $password_hash = Bcrypt::genHash( $password );
        $stmt = $this->db->prepare( "UPDATE StatsCoach.user SET user_password = ? WHERE user_id = ?" );
        return $stmt->execute( array($password_hash, $user_id) );
    }

    protected function recoverSQL($email, $generated_string)
    {
        if ($generated_string == 0) {
            return false;
        } else {
            $stmt = $this->db->prepare( "SELECT COUNT(`user_id`) FROM StatsCoach.user WHERE `user_email` = ? AND `user_generated_string` = ?" );
            $stmt->execute( array($email, $generated_string) );

            if ($stmt->fetch()) {   // a row exists

                $username = self::fetchSQL( 'user_username', 'user_email', $email ); // getting username for the use in the email.
                $user_id = self::fetchSQL( 'user_id', 'user_email', $email ); // getting username for the use in the email.

                // We want to keep things standard and use the user's id for most of the operations. Therefore, we use id instead of email.

                $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $generated_password = substr( str_shuffle( $charset ), 0, 10 );

                $this->change_password( $user_id, $generated_password );

                $stmt = $this->db->prepare( "UPDATE `users` SET `user_generated_string` = 0 WHERE `user_id` = ?" );
                $stmt->execute( array($user_id) );

                mail( $email, 'Your password', "Hello " . $username . ",\n\nYour your new password is: " . $generated_password . "\n\n
                               Please change your password once you have logged in using this password.\n\n-Lil Richard" );

            } else {
                return false;
            }
        }
    }

    protected function confirm_recover($email)
    {
        $first_name = $this->fetchSQL( 'first_name', 'email', $email );   // returns 1 value

        $unique = uniqid( '', true );
        $random = substr( str_shuffle( 'AdfsBCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 10 );

        $generated_string = $unique . $random;          // a random and unique string

        $stmt = $this->db->prepare( "UPDATE StatsCoach.user SET `user_generated_string` = ? WHERE `user_email` = ?" );
        $stmt->execute( array($generated_string, $email) );

        mail( $email, 'Recover Password', "Hello " . $first_name . ",\r\nPlease click the link below:\r\n\r\n
            " . SITE_PATH . "recover/" . $email . "/" . $generated_string . "/\r\n\r\n 
            We will generate a new password for you and send it back to your email.\r\n\r\n
            --" . SITE_PATH );
        return true;
    }

    protected function user_exists($username)
    {
        $sql = 'SELECT COUNT(user_id) FROM StatsCoach.user WHERE user_username = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$username] );
        $sql = $stmt->fetchColumn();
        return $sql;
    }

    protected function team_exists($teamCode)
    {
        $sql = 'SELECT COUNT(team_id) FROM StatsCoach.teams WHERE team_code = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$teamCode] );
        $sql = $stmt->fetchColumn();
        return $sql;
    }
    
    protected function email_exists($email)
    {
        $sql = "SELECT COUNT(user_id) FROM StatsCoach.user WHERE `user_email`= ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($email) );
        $sql = $stmt->fetchColumn();
        return $sql;
    }

    protected function email_confirmed($username)
    {
        $sql = "SELECT COUNT(user_id) FROM StatsCoach.user WHERE user_username= ? AND user_email_confirmed = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($username, 1) );
        if ($stmt->fetch()) return true;
        throw new \Exception( 'Sorry, you need to activate your account. Please check your email!' );
    }

    public function login()
    {
        try {

            if (!$this->user_exists( $this->username ))
                throw new \Exception( 'Sorry, this Username and Password combination doesn\'t match out records.' );


            // if (!$this->email_confirmed( $this->username ))
               // throw new \Exception( 'Sorry, you need to activate your account. Please check your email!' );

            $sql = "SELECT `user_password`, `user_id` FROM StatsCoach.user WHERE `user_username` = ?";
            $stmt = $this->db->prepare( $sql );
            $stmt->execute( array($this->username) );
            $data = $stmt->fetch();


            // using the verify method to compare the password with the stored hashed password.
            if (Bcrypt::verify( $this->password, $data['user_password'] ) === true)
                $_SESSION['id'] = $data['user_id'];    // returning the user's id.
            else throw new \Exception ( 'Sorry, the username and password combination you have entered is invalid.' );


            if ($this->rememberMe) {
                Request::setCookie( "UserName", $this->user_username );
                Request::setCookie( "FullName", $this->user_full_name );
                Request::setCookie( "UserImage", $this->user_profile_pic );
            } // we clear the cookies in the controller

            session_regenerate_id( true );
            User::clearInstance();
            startApplication( true );     // restart

        } catch (\Exception $e) {
            $this->alert['danger'] = $e->getMessage();
        }

    }

    public function facebook()
    {
        try {
            if (!$this->email_exists( $this->facebook['email'] )) {

            } else {
                $_SESSION['id'] = $this->fetchSQL( 'user_id', 'user_email', $this->facebook['email'] )['user_id'];

                $this->getUser();

                if ($this->user_facebook_id == null) ;
                #self::update_user();
            }
            startApplication( true );
        } catch (\Exception $e) {
            throw new \Exception( 'Sorry, there appears to be an error in Facebook SDK.' );
        }
    }

    public function register()
    {
        try {
            if ($this->user_exists( $this->username ))
                throw new \Exception ( 'That username already exists' );

            if ($this->email_exists( $this->email ))
                throw new \Exception ( 'That email already exists.' );

            $time = time();
            $ip = $_SERVER['REMOTE_ADDR']; // getting the users IP address
            $email_code = $email_code = uniqid( 'code_', true ); // Creating a unique string.
            $this->password = Bcrypt::genHash( $this->password );

            try {
                $sql = "INSERT INTO StatsCoach.user (user_username, user_password, user_type, user_email, user_ip, user_creation_date, user_email_code, user_first_name, user_last_name, user_gender) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $this->db->prepare( $sql )->execute(
                    array($this->username, $this->password, $this->userType, $this->email, $ip, $time, $email_code, $this->firstName, $this->lastName, $this->gender) );

                $sql = "SELECT user_id FROM StatsCoach.user WHERE user_username = ?";
                $stmt = $this->db->prepare( $sql );
                $stmt->execute([$this->username]);
                $_SESSION['id'] = $stmt->fetchColumn();

                $sql = "INSERT INTO StatsCoach.golf_stats (user_id) VALUES (?)";
                $this->db->prepare( $sql )->execute([$_SESSION['id']]);

                if ($this->userType == 'Coach') {
                    do $teamCode = \Modules\Helpers\Bcrypt::genRandomHex();
                    while ($this->team_exists($teamCode));
                    $sql = "INSERT INTO StatsCoach.teams (team_name, team_school, team_coach, team_code) VALUES (?,?,?,?)";
                    $this->db->prepare( $sql )->execute( [$this->teamName, $this->schoolName, $_SESSION['id'], $teamCode] );
                } elseif ($this->teamCode) {
                    if ($teamId = $this->team_exists($this->teamCode)) {
                        $sql = "INSERT INTO StatsCoach.team_members (user_id, team_id) VALUES (?,?)";
                        $this->db->prepare( $sql )->execute( [$_SESSION['id'], $teamId] );
                    } else {
                        $this->alert['danger'] = "The team code you provided appears to be invalid. Select `Join Team` from the menu to try again.";
                    }
                }

                mail( $this->email, 'Please activate your account', "Hello $this->firstName ,
            \r\nThank you for registering with us. 
            \r\n Username :  $this->username 
            \r\n Password :  $this->password 
            \r\n Please visit the link below so we can activate your account:\r\n\r\n
             https://www.Stats.Coach/Activate/$this->email/$email_code/
             \r\n\r\n--" . SITE_PATH );

            } catch (\Exception $e) {

                throw new \Exception( $e->getMessage() ); //"Sorry, we were unable to create this account. Please try again." );
            }



            $this->alert['success'] = "Welcome to Stats Coach. Please check your email to finish your registration.";
            
            startApplication( true );

        } catch (\Exception $e) {
            $this->alert['danger'] = $e->getMessage();
        }
    }

    public function activate()
    {
        // Need to validate the success with database
        try {
            if (!$this->email_exists( $this->email ))
                throw new \Exception( 'Please make sure the Url you have entered is correct.' );

            $sql = "SELECT COUNT(user_id) FROM StatsCoach.user WHERE user_email = ? AND user_email_code = ? AND user_email_confirmed = ?";
            $stmt = $this->db->prepare( $sql );
            $stmt->execute( array($this->email, $this->email_code, '0') );

            if ($stmt->fetch() == 0)
                startApplication( true );

            $sql = "UPDATE StatsCoach.user SET `user_email_confirmed` = 1 WHERE `user_email` = ?";
            $this->db->prepare( $sql )->execute( array($this->email) );
            $login = $this->fetchSQL( 'id', 'email', $this->email );
            session_destroy();
            session_regenerate_id( true );
            $_SESSION['id'] = $login;

        } catch (\Exception $e) {
            $this->alert['danger'] = 'Sorry, we have failed to activate your account. Please contact us for further assistance.';
        }
        startApplication( true ); // there is not activate template file
    }

    public function recover()
    {
        try {
            if (isset($parameter) & isset($unique)) {
                if (!$this->email_exists( $email ))
                    throw new \Exception ( "Sorry, we have detected an invalid url. Please contact us for further support." );

                if (!$this->recoverSQL( $email, $unique ))
                    throw new \Exception ( "Sorry, something went wrong and we could not recover your password." );


                // throw new /Exception ('');

            }
            if (isset($email) === true) {   // and only email

                if (!$this->email_exists( $email ))
                    throw new \Exception ( 'Sorry, that email doesn\'t exist.' );

                if (!$this->confirm_recover( $email ))     // Sends Email  // if didn't work
                    throw new \Exception ( 'Sorry, we are having an internal error. Please contact us for more support.' );

                return header( "LOCATION: http://Stats.Coach/login/sent/" ); // This Re-directs to new url/ No $_Post
                // TODO - recover
            }
        } catch (\Exception $e) {
            $this->alert['danger'] = $e->getMessage();
        }
    }

    public function profile($id = null)
    {

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


