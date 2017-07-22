<?php

namespace Model;

use Model\Helpers\iSport;
use Model\Helpers\Team;
use Modules\Helpers\Reporting\PublicAlert;
use Modules\Helpers\Bcrypt;
use Modules\StoreFiles;
use Modules\Singleton;
use Modules\Request;


class User extends Team
{
    use Singleton { __sleep as private catNap; }
    const Singleton = true;

    public function __sleep()
    {   // define the globals & unset the pointers
        unset($this->user, $this->team, $this->course, $this->tournament);
        global $user, $team, $course, $tournament;
        $this->user = $user;
        $this->team = $team;
        $this->course = $course;
        $this->tournament = $tournament;
        return $this->catNap();
    }

    public function __construct()   // What do we need from the logged in user for the template?
    {
        parent::__construct();
        $id = isset($_SESSION['id']) ? $_SESSION['id'] : false;
        if ($_SESSION['id']) {
            $_SESSION['X_PJAX_Version'] = 'v' . SITE_VERSION . 'u' . $_SESSION['id']; // Bcrypt::genRandomHex(30);
            Request::setHeader( 'X-PJAX-Version: ' . $_SESSION['X_PJAX_Version'] );
            // if (!empty($this->user[$_SESSION['id']])) return null;
            $this->profile( $_SESSION['id'] );
            if (!empty($teams = $this->user[$_SESSION['id']]->teams))
                foreach ($teams as $team_id)
                    $this->teamMembers($team_id);
        } else $_SESSION['X_PJAX_Version'] = SITE_VERSION;
    }

    private function user($id)
    {   // In theory this method is only called once per session.
        $this->user[$id] = $this->fetch_object( 'SELECT * FROM StatsCoach.user LEFT JOIN StatsCoach.entity_tag ON entity_id = StatsCoach.user.user_id WHERE StatsCoach.user.user_id = ?', $id );
        if (empty($this->user[$id])) throw new \Exception( 'Could not find user  ' . $id );
        $this->user[$id]->user_profile_pic = SITE . $this->user[$id]->user_profile_pic;
        $this->user[$id]->user_cover_photo = SITE . $this->user[$id]->user_cover_photo;
    }

    protected function updateUser()
    {
        return $this->db->prepare( "UPDATE StatsCoach.user SET 
                                    user_facebook_id = ?, 
                                    user_username = ?, 
                                    user_first_name = ?, 
                                    user_last_name = ?, 
                                    user_profile_pic = ?,
                                    user_cover_photo = ?,
                                    user_birthday = ?,
                                    user_gender = ?, 
                                    user_about_me = ?,
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

    protected function user_exists($username)
    {
        $sql = 'SELECT COUNT(user_id) FROM StatsCoach.user WHERE user_username = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$username] );
        $sql = $stmt->fetchColumn();
        return $sql;
    }

    protected function team_exists($team_code)
    {
        $sql = 'SELECT team_id FROM StatsCoach.teams WHERE team_code = ? AND team_sport = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$team_code, $this->user_sport] );
        return $stmt->fetchColumn();
    }

    protected function email_exists($email)
    {
        $sql = "SELECT COUNT(user_id) FROM StatsCoach.user WHERE `user_email`= ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($email) );
        return $stmt->fetchColumn();
    }

    protected function email_confirmed($username)
    {
        $sql = "SELECT COUNT(user_id) FROM StatsCoach.user WHERE user_username = ? AND user_email_confirmed = ? LIMIT 1";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($username, 1) );
        return $stmt->fetch();
    }

    public function login($username, $password, $rememberMe)
    {
        if (!$this->user_exists( $username ))
            throw new PublicAlert( 'Sorry, this Username and Password combination doesn\'t match out records.', 'warning' );

        if (!$this->email_confirmed( $username ))
            throw new PublicAlert( 'Sorry, you need to activate your account. Please check your email!' );

        $sql = "SELECT user_password, user_profile_pic, user_full_name, user_id FROM StatsCoach.user WHERE user_username = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$username] );
        $data = $stmt->fetch();

        // using the verify method to compare the password with the stored hashed password.
        if (Bcrypt::verify( $password, $data['user_password'] ) === true)
            $_SESSION['id'] = $data['user_id'];    // returning the user's id.
        else throw new PublicAlert ( 'Sorry, the username and password combination you have entered is invalid.', 'warning' );

        if ($rememberMe) {
            Request::setCookie( "UserName", $username );
            Request::setCookie( "FullName", $data['user_full_name'] );
            Request::setCookie( "UserImage", $data['user_profile_pic'] );
        }
        return startApplication( true );
    }

    public function joinTeam($teamCode)
    {
        if (!$teamId = $this->team_exists( $teamCode ))
            throw new PublicAlert( 'The team code you provided appears to be invalid.', 'warning' );

        $sql = 'SELECT COUNT(user_id) FROM StatsCoach.team_members WHERE team_id = ? AND user_id = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$teamId, $_SESSION['id']] );

        if ($stmt->fetchColumn() > 0) throw new PublicAlert( 'It appears you are already a member of this team.', 'warning' );

        $member = $this->beginTransaction( 6 );
        $sql = "INSERT INTO StatsCoach.team_members (member_id, user_id, team_id, sport) VALUES (?,?,?,?)";
        if (!$this->db->prepare( $sql )->execute( [$member, $_SESSION['id'], $teamId, $this->user_sport] ))
            throw new PublicAlert( 'Unable to join this team. ', '' );
        $this->commit();

        PublicAlert::success( 'We successfully add you!' );
        startApplication( true );

    }

    public function facebook()
    {
        try {
            if (!$this->email_exists( $this->facebook['email'] )) {

            } else {
                // $_SESSION['id'] = $this->fetchSQL( 'user_id', 'user_email', $this->facebook['email'] )['user_id'];

                $this->user();

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

        $sql = "INSERT INTO StatsCoach.user (user_id, user_username, user_password, user_type, user_email, user_ip, user_last_login, user_email_code, user_first_name, user_last_name, user_full_name, user_gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if (!$this->db->prepare( $sql )->execute( array($_SESSION['id'], $this->username, $password, $this->userType, $this->email, $_SERVER['REMOTE_ADDR'], time(), $email_code, $this->firstName, $this->lastName, $this->firstName . ' ' . $this->lastName, $this->gender) ))
            throw new PublicAlert ( 'Your account could not be created.', 'danger' );

        if (!$this->db->prepare( 'INSERT INTO StatsCoach.golf_stats (stats_id) VALUES (?)' )->execute( [$_SESSION['id']] ))
            throw new PublicAlert ( 'Your account could not be created.', 'danger' );;
        
        $this->commit();

        if ($this->userType == 'Coach')
            $this->createTeam( $this->teamName, $this->schoolName );

        elseif ($this->teamCode)
            $this->newTeamMember($this->teamCode);


        $subject = 'Your' . SITE_TITLE . ' password';
        $headers = 'From: Support@Stats.Coach' . "\r\n" .
            'Reply-To: Support@Stats.Coach' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $message = "Hello $this->firstName ,
            \r\nThank you for registering with us. 
            \r\n Username :  $this->username 
            \r\n Password :  $this->password 
            \r\n Please visit the link below so we can activate your account:\r\n\r\n
             https://www.Stats.Coach/Activate/" . base64_encode( $this->email ) . "/" . base64_encode( $email_code ) . "/ \r\n\r\n--" . SITE;


        mail( $this->email, $subject, $message, $headers );


        PublicAlert::success( "Welcome to Stats Coach. Please check your email to finish your registration." );
        startApplication( true );
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

        $this->db->prepare( "UPDATE StatsCoach.user SET user_email_confirmed = 1 WHERE user_email = ?" )->execute( array($email) );
        $sql = "SELECT user_id FROM StatsCoach.user WHERE user_email = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$email] );
        $login = $stmt->fetch();
        session_destroy();
        session_regenerate_id( true );
        $_SESSION['id'] = $login;
        return startApplication( 'Home/' ); // there is not an activate template file
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

    public function profile($id)
    {
        $this->user( $id );
        $this->userTeams( $id  );
        if (!empty($this->user[$id]->teams))
            foreach ($this->user[$id]->teams as $team_id)
                $this->team($team_id);
        $model = $this->user[$id]->user_sport;
        $model = "Model\\$model";
        $model = new $model;
        if ($model instanceof iSport)
            $model->stats( $id );
    }

    public function settings()
    {
        if (!empty($_POST)) {
            if ('false' == $filePath = new StoreFiles( 'FileToUpload', 'Data/Uploads/Pictures/' )) {
                echo "File Upload Fail";
            }
        }
    }

}


