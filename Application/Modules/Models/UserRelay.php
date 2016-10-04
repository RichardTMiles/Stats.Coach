<?php

/*
The user relay runs through out Database connection code, no PDO is actually run here
Do not use try catch, as it is not needed

Email needs to be edited in function "register"
*/

namespace App\Modules\Models;

use App\Models\Database\db;

class UserRelay extends db
{
    private $bcrypt; // Password Hashing

    public function __construct()
    {
        $this->bcrypt = new Bcrypt( 12 );
        return parent::__construct();       // The parent:: __construct(); must be called last
    }

    // This returns only the basic user data
    public function profileData($id)
    {
        return parent::getRow( "SELECT `user_username`, `user_first_name`, `user_last_name`, 
                      `user_gender`, `user_bio`, `user_profile_pic`, `user_cover_photo`, `user_email`,
                       `user_creation_date`
                      FROM `users` WHERE `user_id`= ?", array($id) );

    }

    // Returns all profile table data
    public function userData($id)
    {
        return parent::getRow( "SELECT * FROM `users` WHERE `id`= ?", array($id) );
    }

    public function fetch_info($what, $field, $value)
    {
        $allowed = array('user_id', 'user_username', 'user_first_name', 'user_last_name', 'user_gender', 'user_bio', 'user_email');
        // I have only added few, but you can add more. However do not add 'password' even
        // though the parameters will only be given by you and not the user, in our system.
        if (!in_array( $what, $allowed, true ) || !in_array( $field, $allowed, true )) {
            throw new \InvalidArgumentException;
        } else {
            $query = parent::getRow( "SELECT $what FROM `users` WHERE $field = ?", array($value) );
            return $query[$what];
        }
    } // Returns only one value from the db

    public function register($username, $password, $email, $firstName, $lastName)
    {

        $time = time();
        $ip = $_SERVER['REMOTE_ADDR']; // getting the users IP address
        $email_code = $email_code = uniqid( 'code_', true ); // Creating a unique string.
        $password = $this->bcrypt->genHash( $password );

        try {
            parent::insertRow( "INSERT INTO users (`user_username`, `user_password`, `user_email`, `user_ip`, `user_time`, `user_email_code`, `user_first_name`, `user_last_name`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                array($username, $password, $email, $ip, $time, $email_code, $firstName, $lastName) );

            mail( $email, 'Please activate your account', "Hello " . $firstName . ",
            \r\nThank you for registering with us. 
            \r\n Username : " . $username . "
            \r\n Password : " . $password . "
            \r\n Please visit the link below so we can activate your account:\r\n\r\n
             http://www.Stats.Coach/users/activate/" . $email . "/" . $email_code .
                "\r\n\r\n-- Richard Miles" );

        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            return false;
        }
        return true;
    }

    public function activate($email, $email_code)
    {
        try {
            parent::checkExist( "SELECT COUNT('user_id') FROM `users` WHERE `user_email` = ? AND `user_email_code` = ? AND `user_email_confirmed` = ?", array($email, $email_code, '0') );
            parent::updateRow( "UPDATE `users` SET `user_email_confirmed` = 1 WHERE `user_email` = ?", array($email) );
        } catch (\Exception $e) {
            // The next line would be for error testing
            // echo 'Caught exception: ',  $e->getMessage(), "\n";
            throw new \Exception( 'Sorry, we have failed to activate your account. Please contact us for further assistance.' );
        }
    }

    public function login($username, $password)
    {
        $query = parent::getRow( "SELECT `user_password`, `user_id` FROM `users` WHERE `user_username` = ?", array($username) );
        $data = $query;
        $stored_password = $data['user_password']; // stored hashed password
        $id = $data['user_id']; // id of the user to be returned if the password is verified, below.

        // using the verify method to compare the password with the stored hashed password.
        if ($this->bcrypt->verify( $password, $stored_password ) === true) {
            return $id;    // returning the user's id.
        } else {
            throw new \Exception ( 'Sorry, the username and password combination you have entered is invalid.' );
        }
    }

    public function update_user($first_name, $last_name, $gender, $bio, $image_location, $id)
    {
        return parent::updateRow( "UPDATE users SET user_first_name = ?, user_last_name = ?, user_gender = ?, 
            user_bio = ?, user_profile_pic = ? WHERE user_id = ?",
            array($first_name, $last_name, $gender, $bio, $image_location, $id) );
    }

    public function change_password($user_id, $password)
    {
        /* Two create a Hash you do */
        $password_hash = $this->bcrypt->genHash( $password );
        return parent::updateRow( "UPDATE `users` SET `user_password` = ? WHERE `user_id` = ?", array($password_hash, $user_id) );
    }

    public function recover($email, $generated_string)
    {
        if ($generated_string == 0) {
            return false;
        } else {
            if (parent::checkExist( "SELECT COUNT(`user_id`) FROM `users` WHERE `user_email` = ? AND `user_generated_string` = ?", array($email, $generated_string) ) == true) {
                $username = self::fetch_info( 'user_username', 'user_email', $email ); // getting username for the use in the email.
                $user_id = self::fetch_info( 'user_id', 'user_email', $email ); // getting username for the use in the email.

                // We want to keep things standard and use the user's id for most of the operations. Therefore, we use id instead of email.

                $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $generated_password = substr( str_shuffle( $charset ), 0, 10 );

                $this->change_password( $user_id, $generated_password );

                parent::updateRow( "UPDATE `users` SET `user_generated_string` = 0 WHERE `user_id` = ?", array($user_id) );

                mail( $email, 'Your password', "Hello " . $username . ",\n\nYour your new password is: " . $generated_password . "\n\nPlease change your password once you have logged in using this password.\n\n-Lil Richard" );
            } else {
                return false;
            }
        }
    }

    public function confirm_recover($email)
    {

        $first_name = $this->fetch_info( 'first_name', 'email', $email ); // returns 1 value

        $unique = uniqid( '', true );
        $random = substr( str_shuffle( 'AdfsBCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 10 );

        $generated_string = $unique . $random; // a random and unique string

        parent::updateRow( "UPDATE `users` SET `user_generated_string` = ? WHERE `user_email` = ?",
            array($generated_string, $email) );

        mail( $email, 'Recover Password', "Hello " . $first_name . ",\r\nPlease click the link below:\r\n\r\n
        
        http://www.stats.coach/users/recover/" . $email . "/" . $generated_string . "/\r\n\r\n 
        
        We will generate a new password for you and send it back to your email.\r\n\r\n
        -- Stats.Coach" );
        return true;
    }

    public function user_exists($username)
    {
        return parent::checkExist( "SELECT COUNT(`user_id`) FROM `users` WHERE `user_username`= ?", array($username) );
    }

    public function email_exists($email)
    {
        return parent::checkExist( "SELECT COUNT(`user_id`) FROM `users` WHERE `user_email`= ?", array($email) );
    }

    public function email_confirmed($username)
    {
        if (parent::checkExist( "SELECT COUNT(`user_id`) FROM `users` WHERE `user_username`= ? AND `user_email_confirmed` = ?",
            array($username, 1) )
        ) {
            return true;
        } else {
            throw new \Exception( 'Sorry, you need to activate your account. Please check your email!' );
        }
    }

    public function get_users()
    {
        return parent::getAllRows( "SELECT * FROM `users` ORDER BY `user_time` DESC" );
    }

    public function __destruct()
    {   // Close the DB Connection
        parent::Disconnect();
    }

}













