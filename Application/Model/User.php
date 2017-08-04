<?php

namespace Model;

use Model\Helpers\iSport;
use Model\Helpers\Messages;
use Modules\Helpers\Reporting\PublicAlert;
use Modules\Helpers\Bcrypt;
use Modules\Request;


class User extends Team
{
    public function __construct()
    {
        global $user;
        parent::__construct();
        $_SESSION['id'] = $id = isset($_SESSION['id']) ? $_SESSION['id'] : false;

        if ($id) {
            $_SESSION['X_PJAX_Version'] = 'v' . SITE_VERSION . 'u' . $id; // force reload occurs when X_PJAX_Version changes between requests
            Request::setHeader( 'X-PJAX-Version: ' . $_SESSION['X_PJAX_Version'] );
            if (is_array( $user ) && !empty($user[$id])) return null;
            $this->fullUser( $id );
            if (!empty($teams = $user[$id]->teams))
                foreach ($teams as $team_id)
                    $this->teamMembers( $team_id );
        } else $_SESSION['X_PJAX_Version'] = SITE_VERSION;
    }

    private function onlineStatus($id)
    {
        $sql = 'SELECT user_online_status FROM StatsCoach.user_session WHERE user_id = ? LIMIT 1';
        $this->fetch_into_class( $this->user[$id], $sql, $id );
    }

    public function changeStatus($id, $status = false)
    {
        $sql = 'UPDATE StatsCoach.user_session SET user_online_status = ? WHERE user_id = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$status, $id] );
        $this->user[$id]->online = (bool)$stmt->fetchColumn();
    }

    public function user_id_from_uri(string $user_uri)
    {
        $stmt = $this->db->prepare( 'SELECT user_id FROM StatsCoach.user WHERE user_profile_uri = ?' );
        $stmt->execute( [$user_uri] );
        return $stmt->fetch( \PDO::FETCH_COLUMN );
    }

    private function fullUser($id)
    {

        $this->user[$id] = $this->fetch_object( 'SELECT * FROM StatsCoach.user LEFT JOIN StatsCoach.entity_tag ON entity_id = StatsCoach.user.user_id WHERE StatsCoach.user.user_id = ?', $id );
        if (empty($this->user[$id])) throw new \Exception( 'Could not find user  ' . $id );
        $this->user[$id]->user_profile_picture = SITE . (!empty($this->user[$id]->user_profile_pic) ? $this->user[$id]->user_profile_pic : 'Data/Uploads/Pictures/Defaults/default_avatar.png');
        $this->user[$id]->user_cover_photo = SITE . $this->user[$id]->user_cover_photo;
        $this->user[$id]->user_full_name = $this->user[$id]->user_first_name . ' ' . $this->user[$id]->user_last_name;

        $this->onlineStatus( $id );
        $this->userTeams( $id );                        // get teams
        if (!empty($this->user[$id]->teams))
            foreach ($this->user[$id]->teams as $team_id)
                $this->team( $team_id );

        $model = $this->user[$id]->user_sport;
        $model = "Model\\$model";
        $model = new $model;
        if ($model instanceof iSport)                   // load stats
            $model->stats( $id );


        $stmt = $this->db->prepare( 'SELECT COUNT(*) FROM StatsCoach.user_followers WHERE follows_user_id = ?' );
        $stmt->execute( [$id] );
        $this->user[$id]->stats->followers = (int)$stmt->fetchColumn();
        $stmt = $this->db->prepare( 'SELECT COUNT(*) FROM StatsCoach.user_followers WHERE user_id = ?' );
        $stmt->execute( [$id] );
        $this->user[$id]->stats->following = (int)$stmt->fetchColumn();

        // load messages

        Messages::get( $this->user[$id], $id );

        //sortDump($this->user[$id]);
    }

    protected function change_password($user_id, $password)
    {   /* Two create a Hash you do */
        $password = Bcrypt::genHash( $password );
        return $this->db->prepare( "UPDATE StatsCoach.user SET user_password = ? WHERE user_id = ?" )->execute( [$password, $user_id] );
    }

    public function user_exists($username): bool
    {
        $sql = 'SELECT COUNT(user_id) FROM StatsCoach.user WHERE user_username = ? LIMIT 1';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$username] );
        return $stmt->fetchColumn();
    }

    protected function email_exists($email): bool
    {
        $sql = "SELECT COUNT(user_id) FROM StatsCoach.user WHERE `user_email`= ? LIMIT 1";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($email) );
        return $stmt->fetchColumn();
    }

    protected function email_confirmed($username)
    {
        $sql = "SELECT user_email_confirmed FROM StatsCoach.user WHERE user_username = ? LIMIT 1";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$username] );
        return $stmt->fetchColumn();
    }

    public function login($username, $password, $rememberMe)
    {
        if (!$this->user_exists( $username ))
            throw new PublicAlert( 'Sorry, this Username and Password combination doesn\'t match out records.', 'warning' );

        if (!$this->email_confirmed( $username ))
            throw new PublicAlert( 'Sorry, you need to activate your account. Please check your email!' );

        $sql = "SELECT user_password, user_first_name, user_last_name, user_profile_pic, user_id FROM StatsCoach.user WHERE user_username = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$username] );
        $data = $stmt->fetch();


        // using the verify method to compare the password with the stored hashed password.
        if (Bcrypt::verify( $password, $data['user_password'] ) === true)
            $_SESSION['id'] = $data['user_id'];    // returning the user's id.
        else throw new PublicAlert ( 'Sorry, the username and password combination you have entered is invalid.', 'warning' );

        if ($rememberMe) {
            Request::setCookie( "UserName", $username );
            Request::setCookie( "FullName", $data['user_first_name'] . ' ' . $data['user_last_name'] );
            Request::setCookie( "UserImage", $data['user_profile_pic'] );
        } #else Request::clearCookies();

        startApplication( true );
    }

    public function basicUser($id): \stdClass
    {
        $user = $this->user[$id] = $this->fetch_object( 'SELECT user_profile_uri, user_profile_pic, user_first_name, user_last_name FROM StatsCoach.user WHERE user_id = ?', $id );
        $user->user_profile_picture = SITE . $user->user_profile_pic ?? 'Data/Uploads/Pictures/Defaults/default_avatar.png';
        $user->user_full_name = $user->user_first_name . ' ' . $user->user_last_name;
        return $user;
    }

    public function facebook()
    {
        try {
            if (!$this->email_exists( $this->facebook['email'] )) {

            } else {
                // $_SESSION['id'] = $this->fetchSQL( 'user_id', 'user_email', $this->facebook['email'] )['user_id'];

                $this->fullUser( $_SESSION['id'] );

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
        if ($this->user_exists( $this->username ))
            throw new PublicAlert ( 'That username already exists', 'warning' );

        if ($this->email_exists( $this->email ))
            throw new PublicAlert ( 'That email already exists.', 'warning' );

        $email_code = uniqid( 'code_', true ); // Creating a unique string.
        $password = Bcrypt::genHash( $this->password );

        $_SESSION['id'] = $this->beginTransaction( 0 );

        $sql = "INSERT INTO StatsCoach.user (user_id, user_profile_uri, user_username, user_password, user_type, user_email, user_ip, user_last_login, user_email_code, user_first_name, user_last_name, user_gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if (!$this->db->prepare( $sql )->execute( array($_SESSION['id'], $_SESSION['id'], $this->username, $password, $this->userType, $this->email, $_SERVER['REMOTE_ADDR'], time(), $email_code, $this->firstName, $this->lastName, $this->gender) ))
            throw new PublicAlert ( 'Your account could not be created.', 'danger' );

        if (!$this->db->prepare( 'INSERT INTO StatsCoach.golf_stats (stats_id) VALUES (?)' )->execute( [$_SESSION['id']] ))
            throw new PublicAlert ( 'Your account could not be created.', 'danger' );;

        $this->commit();

        if ($this->userType == 'Coach')
            $this->createTeam( $this->teamName, $this->schoolName );

        elseif ($this->teamCode)
            $this->newTeamMember( $this->teamCode );


        $subject = 'Your ' . SITE_TITLE . ' Password';
        $headers = 'From: ' . SYSTEM_EMAIL . "\r\n" .
            'Reply-To: ' . REPLY_EMAIL . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $message = "Hello $this->firstName ,
            \r\nThank you for registering with us. 
            \r\n Username :  $this->username 
            \r\n Password :  $this->password 
            \r\n Please visit the link below so we can activate your account:\r\n\r\n
             https://www.Stats.Coach/Activate/" . base64_encode( $this->email ) . "/" . base64_encode( $email_code ) . "/ \r\n\r\n Have a good day! \r\n--" . SITE;


        mail( $this->email, $subject, $message, $headers );


        PublicAlert::success( "Welcome to Stats Coach. Please check your email to finish your registration." );
        startApplication( 'home/' );
    }

    public function activate($email, $email_code)
    {
        if (!$this->email_exists( $email ))
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

        if (!$this->email_exists( $email )) $alert();

        $generated = Bcrypt::genRandomHex( 20 );

        if (empty($generated_string)) {
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
            if (empty($user = $stmt->fetch())) $alert();

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

    public function profile($user_uri)
    {
        if ($user_uri !== true) {
            return $this->fullUser( $this->user_id_from_uri( $user_uri ) );
        }

        // we can assume post is active then
        global $first, $last, $email, $gender, $dob, $password, $profile_pic, $about_me;

        $user = $this->user[$_SESSION['id']];

        $sql = 'UPDATE StatsCoach.user SET user_profile_pic = :user_profile_pic, user_first_name = :user_first_name, user_last_name = :user_last_name, user_birthday = :user_birthday, user_email = :user_email, user_email_confirmed = :user_email_confirmed,  user_gender = :user_gender, user_about_me = :user_about_me WHERE user_id = :user_id';
        $stmt = $this->db->prepare( $sql );
        $stmt->bindValue( ':user_profile_pic', $profile_pic ?: $user->user_profile_pic );
        $stmt->bindValue( ':user_first_name', $first ?: $user->user_first_name );
        $stmt->bindValue( ':user_last_name', $last ?: $user->user_last_name );
        $stmt->bindValue( ':user_birthday', $dob ?: $user->user_birthday );
        $stmt->bindValue( ':user_gender', $gender ?: $user->user_gender );
        $stmt->bindValue( ':user_email', $email ?: $user->user_email );
        $stmt->bindValue( ':user_email_confirmed', $email ? 0 : $user->user_email_confirmed );
        $stmt->bindValue( ':user_about_me', $about_me ?: $user->user_about_me );
        $stmt->bindValue( ':user_id', $_SESSION['id'] );
        if (!$stmt->execute())
            throw new PublicAlert( 'Sorry, we could not process your information at this time.', 'warning' );

        if (!empty($profile_pic) && !empty($user->user_profile_pic) && $profile_pic != $user->user_profile_pic)
            unlink( SERVER_ROOT . $user->user_profile_pic );

        if (!empty($email) && $email != $user->user_email) {
            $subject = 'Please confirm your email';
            $headers = 'From: ' . SYSTEM_EMAIL . "\r\n" .
                'Reply-To: ' . REPLY_EMAIL . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $message = "Hello " . ($first ?: $user->user_first_name) . ",
            \r\n Please visit the link below so we can activate your account:\r\n\r\n
             https://www.Stats.Coach/Activate/" . base64_encode( $email ) . "/" . base64_encode( $user->user_email_code ) . "/ \r\n\r\n Happy Golfing \r\n--" . SITE;


            if (!mail( $email ?: $user->user_email, $subject, $message, $headers ))
                throw new PublicAlert( 'Our email system failed.' );

            PublicAlert::success( 'Please check your email to activate your account' );
        } else
            PublicAlert::success( 'Your account has been updated!' );
        startApplication( true );
    }


}


