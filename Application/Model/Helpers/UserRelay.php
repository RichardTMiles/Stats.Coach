<?php

/*
The user relay runs through out Database connection code, no PDO is actually run here
Do not use try catch, as it is not needed

Email needs to be edited in function "register"
*/

namespace Model\Helpers;

use PDO;

use Controller\User;
use Modules\Helpers\Skeleton;
use Modules\Helpers\Bcrypt;
use Modules\Database;
use Psr\Singleton;

abstract class UserRelay
{

    private $db;


    public $user_id;
    public $user_facebook_id;
    public $user_username;
    public $user_full_name;
    public $user_first_name;
    public $user_last_name;
    public $user_profile_pic;
    public $user_cover_photo;
    public $user_birth_date;
    public $user_gender;
    public $user_bio;
    public $user_rank;
    public $user_password;
    public $user_email;
    public $user_email_code;
    public $user_email_confirmed;
    public $user_generated_string;
    public $user_membership;
    public $user_deactivated;
    public $user_creation_date;
    public $user_ip;


    public function __construct($id = false)
    {
        $this->db = Database::getConnection();  // establish a connection
    }

    public function __wakeup($id = false)
    {
        $this->db = Database::getConnection();  // establish a connection
    }
    

    public function __sleep()
    {
        return (!empty($this->user_username) ? array('user_username', 'user_first_name', 'user_last_name', 'user_profile_pic', 'user_cover_photo', 'user_birth_date', 'user_gender', 'user_bio', 'user_rank', 'user_email', 'user_email_code', 'user_email_confirmed', 'user_generated_string', 'user_membership', 'user_deactivated', 'user_creation_date', 'user_ip') : 0);
    }


    protected function userSQL($id = false)
    {   // Private bc its commonly called singly, but still required the constructor

        $id = $id ?: User::getApp_id(); // throw new \Exception("Attempted load of profile while logged out.");
        $this->user_id = $id;

        if (isset($this->user_username)) {
            // this should have been done in the wake up routine
            // TODO - check if this actually matters

            if (!array_key_exists( 'user_username', $GLOBALS )) {
                foreach (get_object_vars( $this ) as $key => $var)
                    $GLOBALS[$key] = $var;
            }
            return true;
        }

        // In theory this request is only called once per session.
        $sql = 'SELECT * FROM users WHERE `user_id` = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$id] );
        $data = $stmt->fetch( PDO::FETCH_ASSOC );


        foreach ($data as $key => $val)
            $GLOBALS[$key] = $this->{$key} = $val;

        // Reconfig variables for dynamic path, and easy templating
        $GLOBALS['user_profile_pic'] = $this->user_profile_pic = SITE_ROOT . $this->user_profile_pic;
        $GLOBALS['user_cover_photo'] = $this->user_cover_photo = SITE_ROOT . $this->user_cover_photo;
        $GLOBALS['user_full_name'] = $this->user_full_name = $this->user_first_name . ' ' . $this->user_last_name;

    }

    protected function fetchSQL($what, $field, $value)
    {
        $allowed = array('user_id', 'user_profile_pic', 'user_username', 'user_full_name', 'user_first_name', 'user_last_name', 'user_gender', 'user_bio', 'user_email');

        if (!in_array( $what, $allowed, true ) || !in_array( $field, $allowed, true ))
            throw new \InvalidArgumentException;

        $sql = "SELECT $what FROM `users` WHERE $field = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($value) );
        return $stmt->fetch();

    } // Returns only one value from the db

    protected function registerSQL($username, $password, $email, $firstName, $lastName)
    {

        $time = time();
        $ip = $_SERVER['REMOTE_ADDR']; // getting the users IP address
        $email_code = $email_code = uniqid( 'code_', true ); // Creating a unique string.
        $crypt = Bcrypt::genHash( $password );

        try {
            $sql = "INSERT INTO users (`user_username`, `user_password`, `user_email`, `user_ip`, `user_creation_date`, `user_email_code`, `user_first_name`, `user_last_name`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $this->db->prepare( $sql )->execute( array($username, $crypt, $email, $ip, $time, $email_code, $firstName, $lastName) );

            mail( $email, 'Please activate your account', "Hello $firstName ,
            \r\nThank you for registering with us. 
            \r\n Username :  $username 
            \r\n Password :  $password 
            \r\n Please visit the link below so we can activate your account:\r\n\r\n
             http://www.Stats.Coach/Activate/$email/$email_code/
             \r\n\r\n--" . SITE_ROOT );

        } catch (\Exception $e) {
            throw new \Exception( "Sorry, we were unable to create this account. Please try again." );
        }

        return true;
    }

    public function activateSQL($email, $email_code)
    {

        $sql = "SELECT COUNT(user_id) FROM users WHERE user_email = ? AND user_email_code = ? AND user_email_confirmed = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($email, $email_code, '0') );

        if ($stmt->fetch() > 0) {
            $sql = "UPDATE `users` SET `user_email_confirmed` = 1 WHERE `user_email` = ?";
            return $this->db->prepare( $sql )->execute( array($email) );
        }
        throw new \Exception( 'Sorry, we have failed to activate your account. Please contact us for further assistance.' );

    }

    protected function loginSQL($username, $password)
    {

        $sql = "SELECT `user_password`, `user_id` FROM `users` WHERE `user_username` = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($username) );
        $data = $stmt->fetch();

        // using the verify method to compare the password with the stored hashed password.
        if (Bcrypt::verify( $password, $data['user_password'] ) === true)
            $_SESSION['id'] = $data['user_id'];    // returning the user's id.
        else throw new \Exception ( 'Sorry, the username and password combination you have entered is invalid.' );

        $this->userSQL( $_SESSION['id'] );
    }

    protected function updateSQL()
    {
        return $this->db->prepare(  "UPDATE users SET 
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
        $this->db = Database::getConnection();

        $password_hash = Bcrypt::genHash( $password );

        $stmt = $this->db->prepare( "UPDATE users SET user_password = ? WHERE user_id = ?" );
        return $stmt->execute( array($password_hash, $user_id) );

    }

    public function recoverSQL($email, $generated_string)
    {
        $this->db = Database::getConnection();

        if ($generated_string == 0) {
            return false;
        } else {
            $stmt = $this->db->prepare( "SELECT COUNT(`user_id`) FROM `users` WHERE `user_email` = ? AND `user_generated_string` = ?" );
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
        $this->db = Database::getConnection();

        $first_name = $this->fetchSQL( 'first_name', 'email', $email );   // returns 1 value

        $unique = uniqid( '', true );
        $random = substr( str_shuffle( 'AdfsBCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 10 );

        $generated_string = $unique . $random;          // a random and unique string

        $stmt = $this->db->prepare( "UPDATE `users` SET `user_generated_string` = ? WHERE `user_email` = ?" );
        $stmt->execute( array($generated_string, $email) );

        mail( $email, 'Recover Password', "Hello " . $first_name . ",\r\nPlease click the link below:\r\n\r\n
            " . SITE_ROOT . "recover/" . $email . "/" . $generated_string . "/\r\n\r\n 
            We will generate a new password for you and send it back to your email.\r\n\r\n
            --" . SITE_ROOT );
        return true;
    }

    protected function user_exists($username)
    {
        $this->db = Database::getConnection();

        $sql = 'SELECT COUNT(user_id) FROM users WHERE user_username = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$username] );
        $sql = $stmt->fetchColumn();
        return $sql;
    }

    protected function email_exists($email)
    {
        $this->db = Database::getConnection();

        $sql = "SELECT COUNT(user_id) FROM `users` WHERE `user_email`= ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($email) );
        $sql = $stmt->fetchColumn();
        return $sql;
    }

    protected function email_confirmed($username)
    {
        $this->db = Database::getConnection();

        $sql = "SELECT COUNT(user_id) FROM users WHERE user_username= ? AND user_email_confirmed = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($username, 1) );
        if ($stmt->fetch()) return true;
        throw new \Exception( 'Sorry, you need to activate your account. Please check your email!' );
    }

    private function get_users()
    {
        $this->db = Database::getConnection();

        $sql = "SELECT * FROM users ORDER BY user_creation_date DESC";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute();
        return $stmt->fetchAll();
    }

}












