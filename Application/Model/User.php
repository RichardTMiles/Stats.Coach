<?php

namespace Model;

use Model\Helpers\GlobalMap;
use Psr\Log\InvalidArgumentException;
use Tables\Users;
use Model\Helpers\iSport;
use Tables\Followers;
use Tables\Messages;
use Tables\Teams;
use Carbon\Error\PublicAlert;
use Carbon\Helpers\Bcrypt;
use Carbon\Request;

class User extends GlobalMap
{
    public function __construct(string $id = null)
    {
        // Used to get team member
        parent::__construct();

        if (!is_array( $this->user ))
            throw new \Exception( 'Users is no longer an array' );

        if ($_SESSION['id'] == $id)
            return; // We've already gotten current user data

        if ($_SESSION['id'] && $id !== null) {
            Users::get( $this->user[$id], $id );
            Followers::get( $this->user[$id], $id );
            Messages::get( $this->user[$id], $id );
        }
    }

    /**
     * @param $username
     * @param $password
     * @param $rememberMe
     * @throws PublicAlert
     */
    public function login($username, $password, $rememberMe)
    {
        if (!Users::user_exists( $username ))
            throw new PublicAlert( 'Sorry, this Username and Password combination doesn\'t match out records.', 'warning' );

        if (!Users::email_confirmed( $username ))
            throw new PublicAlert( 'Sorry, you need to activate your account. Please check your email!' );

        // We get this for the cookies
        $sql = "SELECT user_password, user_first_name, user_last_name, user_profile_pic, user_id FROM StatsCoach.user WHERE user_username = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$username] );
        $data = $stmt->fetch();

        // using the verify method to compare the password with the stored hashed password.
        if (Bcrypt::verify( $password, $data['user_password'] ) === true) {
            $_SESSION['id'] = $data['user_id'];    // returning the user's id.
        } else throw new PublicAlert ( 'Sorry, the username and password combination you have entered is invalid.', 'warning' );


        if ($rememberMe) {
            Request::setCookie( "UserName", $username );
            Request::setCookie( "FullName", $data['user_first_name'] . ' ' . $data['user_last_name'] );
            Request::setCookie( "UserImage", $data['user_profile_pic'] );
        } else (new Request)->clearCookies();

        startApplication( true );
    }

    public function facebook($request)
    {
        global $facebook;

        if (empty($facebook)) startApplication(true);

        sortDump($facebook);

        $sql = "SELECT user_id, user_facebook_id FROM StatsCoach.user WHERE user_email = ? OR user_facebook_id =?";
        $sql = (self::fetch($sql, $facebook['email'], $facebook['id']));

        $C6_id = $sql['user_id'] ?? false;
        $fb_id = $sql['user_facebook_id'] ?? false;

        if (!$C6_id && !$fb_id): // create new account
            if ($request === 'SignUp'):                         // This will set the session id
                Users::add($null, $null, [
                    'username' => $facebook['id'],
                    'password' => null,
                    'facebook_id' => $facebook['id'],
                    'profile_pic' => $facebook["picture"]["url"],
                    'cover_photo' => $facebook["cover"]["source"],
                    'email' => $facebook["email"],
                    'type' => "Athlete",
                    'first_name' => $facebook["first_name"],
                    'last_name' => $facebook["last_name"],
                    'gender' => $facebook["gender"]
                ]);
            else:                                           // were trying to signin when signup
                $_SESSION['facebook'] = $facebook;
                return $facebook = "SignUp";        // Sign into a non-existing account
            endif;
        elseif ($C6_id && !$fb_id):
            if ($request === 'SignIn'):
                $sql = "UPDATE StatsCoach.user SET user_facebook_id = ? WHERE user_id = ?";
                $this->db->prepare($sql)->execute([$facebook['id'], $_SESSION['id']]);
                $_SESSION['id'] = $C6_id;
            else:
                $_SESSION['facebook'] = $facebook;  // were trying to signup when we need to signin
                return $facebook = "SignIn";
            endif;
        else:
            $_SESSION['id'] = $C6_id;
        endif;
        $_SESSION['facebook'] = $facebook = null; 
        startApplication(true);
    }

    public function follow($user_id){
        global $user;
        if (!$out = Users::user_exists($user_id))
            throw new PublicAlert("That user does not exist $user_id >> $out");
        return Followers::add($user[$_SESSION['id']], $_SESSION['id'], $user_id);
    }

    public function unfollow($user_id){
        global $user;
        if (!Users::user_exists($user_id))
            throw new PublicAlert("That user does not exist");
        return Followers::remove($user[$_SESSION['id']], $user_id);

    }

    public function google($request){

    }

    public function register()
    {
        global $username, $password, $userType, $email, $firstName, $lastName, $gender, $teamCode;

        if (Users::user_exists( $username ))
            throw new PublicAlert ( 'That username already exists', 'warning' );

        if (Users::email_exists( $email ))
            throw new PublicAlert ( 'That email already exists.', 'warning' );

        $null = null;

        // Tables self validate and throw public errors
        Users::add( $null, $null, [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'type' => $userType,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'gender' => $gender
        ] );

        if ($userType == 'Coach') {
            global $teamName, $schoolName;
            Teams::add( $null, $null, [
                'teamName' => $teamName,
                'schoolName' => $schoolName
            ] );
        } elseif ($teamCode ?? false)
            Teams::newTeamMember( $teamCode );

        PublicAlert::success( "Welcome to Stats Coach. Please check your email to finish your registration." );
        startApplication( 'home/' );
    }

    public function activate($email, $email_code)
    {
        if (!Users::email_exists( $email ))
            throw new PublicAlert( 'Please make sure the Url you have entered is correct.', 'danger' );

        $stmt = $this->db->prepare( "SELECT COUNT(user_id) FROM StatsCoach.user WHERE user_email = ? AND user_email_code = ?" );
        $stmt->execute( [$email, $email_code] );

        if ($stmt->fetch() == 0) {
            PublicAlert::warning( 'Sorry, you may be using an old activation code.' );
            return startApplication( 'Home/' );
        }

        if (!$this->db->prepare( "UPDATE StatsCoach.user SET user_email_confirmed = 1 WHERE user_email = ?" )->execute( array($email) ))
            throw new PublicAlert( 'The code provided appears to be invalid.', 'danger' );


        $stmt = $this->db->prepare( "SELECT user_id FROM StatsCoach.user WHERE user_email = ?" );
        $stmt->execute( [$email] );
        $_SESSION['id'] = $stmt->fetchColumn();
        PublicAlert::success( 'We successfully activated your account.' );
        startApplication( true ); // there is not an activate template file
    }

    public function recover($email, $generated_string)
    {
        $alert = function () {
            throw new PublicAlert( "An account could not be found with the email provided.", 'warning' );
        };

        if (!Users::email_exists( $email )) $alert();   // todo

        $generated = Bcrypt::genRandomHex( 20 );

        if (empty( $generated_string )) {
            $sql = 'SELECT user_first_name  FROM StatsCoach.user WHERE user_email = ?';
            $stmt = $this->db->prepare( $sql );
            $stmt->execute( [$email] );
            $user_first_name = $stmt->fetchColumn();

            $stmt = $this->db->prepare( 'UPDATE StatsCoach.user SET user_generated_string = ? WHERE user_email = ?' );
            if (!$stmt->execute( [$generated, $email] ))
                throw new PublicAlert( 'Sorry, we failed to recover your account.', 'danger' );

            $subject = 'Your' . SITE_TITLE . ' password';
            $headers = 'From: Support@Stats.Coach' . "\r\n" .
                'Reply-To: Support@Stats.Coach' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $message = "Hello " . $user_first_name . ",
            \r\nPlease click the link below:\r\n\r\n" . SITE . "Recover/" . base64_encode( $email ) . "/" . base64_encode( $generated ) . "/\r\n\r\n 
            We will generate a new password for you and send it back to your email.\r\n\r\n--" . SITE_TITLE;

            mail( $email, $subject, $message, $headers );

            PublicAlert::info( "If an account is found, an email will be sent to the account provided." );

        } else {

            $sql = 'SELECT user_id, user_first_name FROM StatsCoach.user WHERE user_email = ? AND user_generated_string = ?';
            $stmt = $this->db->prepare( $sql );
            if (!$stmt->execute( [$email, $generated_string] )) $alert();
            if (empty( $user = $stmt->fetch() )) $alert();

            $this->change_password( $user['user_id'], $generated );
            $stmt = $this->db->prepare( 'UPDATE StatsCoach.user SET user_generated_string = 0 AND user_email_code = 0 AND user_email_confirmed = 1 WHERE user_id = ?' );
            $stmt->execute( [$user['user_id']] );

            $subject = 'Your' . SITE_TITLE . ' password';
            $headers = 'From: Support@Stats.Coach' . "\r\n" .
                'Reply-To: Support@Stats.Coach' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $message = "Hello " . $user['user_first_name'] . ",\n\nYour your new password is: " . $generated .
                "\n\nPlease change your password once you have logged in using this password.\n\n-- " . SITE_TITLE;

            mail( $email, $subject, $message, $headers );
            PublicAlert::success( "Your password has been successfully reset." );
        }
        startApplication( 'login/' );

    }

    public function profile($user_uri = false)
    {
        global $user_id;

        if ($user_uri) {
            $user_id = Users::user_id_from_uri( $user_uri );
            if (!empty( $user_id ))
                return new User( $user_id );
        }

        Users::all( $this->user[$_SESSION['id']], $_SESSION['id'] );

        if (empty( $_POST )) return null;

        // we can assume post is active then
        global $first, $last, $email, $gender, $dob, $password, $profile_pic, $about_me;

        // $this->user === global $user
        $user = $this->user[$_SESSION['id']];

        $sql = 'UPDATE StatsCoach.user SET user_profile_pic = :user_profile_pic, user_first_name = :user_first_name, user_last_name = :user_last_name, user_birthday = :user_birthday, user_email = :user_email, user_email_confirmed = :user_email_confirmed,  user_gender = :user_gender, user_about_me = :user_about_me WHERE user_id = :user_id';
        $stmt = $this->db->prepare( $sql );
        $stmt->bindValue( ':user_profile_pic', $profile_pic ?: $user['user_profile_pic'] );
        $stmt->bindValue( ':user_first_name', $first ?: $user['user_first_name'] );
        $stmt->bindValue( ':user_last_name', $last ?: $user['user_last_name'] );
        $stmt->bindValue( ':user_birthday', $dob ?: $user['user_birthday'] );
        $stmt->bindValue( ':user_gender', $gender ?: $user['user_gender'] );
        $stmt->bindValue( ':user_email', $email ?: $user['user_email'] );
        $stmt->bindValue( ':user_email_confirmed', $email ? 0 : $user['user_email_confirmed'] );
        $stmt->bindValue( ':user_about_me', $about_me ?: $user['user_about_me'] );
        $stmt->bindValue( ':user_id', $_SESSION['id'] );
        if (!$stmt->execute())
            throw new PublicAlert( 'Sorry, we could not process your information at this time.', 'warning' );

        // Remove old picture
        if (!empty( $profile_pic ) && !empty( $user['user_profile_pic'] ) && $profile_pic != $user['user_profile_pic'])
            unlink( SERVER_ROOT . $user['user_profile_pic'] );

        // Send new activation code
        if (!empty( $email ) && $email != $user['user_email']) {
            $subject = 'Please confirm your email';
            $headers = 'From: ' . SYSTEM_EMAIL . "\r\n" .
                'Reply-To: ' . REPLY_EMAIL . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $message = "Hello " . ($first ?: $user['user_first_name']) . ",
            \r\n Please visit the link below so we can activate your account:\r\n\r\n
             https://www.Stats.Coach/Activate/" . base64_encode( $email ) . "/" . base64_encode( $user['user_email_code'] ) . "/ \r\n\r\n Happy Golfing \r\n--" . SITE;


            if (!mail( $email ?: $user['user_email'], $subject, $message, $headers ))
                throw new PublicAlert( 'Our email system failed.' );

            PublicAlert::success( 'Please check your email to activate your account' );
        } else
            PublicAlert::success( 'Your account has been updated!' );
        startApplication( true );
    }

}


