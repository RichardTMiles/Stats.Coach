<?php

namespace Model;

use Model\Helpers\GlobalMap;


use Tables\Carbon_User_Golf_Stats as Stats;
use Tables\Carbon_Users as Users;
use Tables\Carbon_User_Followers as Followers;
use Tables\Carbon_User_Messages as Messages;


use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Helpers\Bcrypt;
use CarbonPHP\Request;
use CarbonPHP\Helpers\Serialized;

/**
 * Class User
 * @package Model
 */
class User extends GlobalMap
{
    /**
     * User constructor.
     * @param string|null $id
     * @throws \CarbonPHP\Error\PublicAlert
     */
    public function __construct(string $id = null)
    {
        // Used to get team member
        parent::__construct();

        if (!\is_array($this->user)) {
            $this->user = [];
        }

        if ($_SESSION['id'] === $id) {
            return; // We've already gotten current user data
        }
        if ($_SESSION['id'] && $id !== null) {
            Users::get($this->user[$id], $id, []);
            Followers::get($this->user[$id], $id, []);
            Messages::get($this->user[$id], $id, []);
        }
    }

    /**
     * @param $username
     * @param $password
     * @param $rememberMe
     * @return bool
     * @throws PublicAlert
     */
    public function login($username, $password, $rememberMe): bool
    {
        $data = [];

        Users::Get($data, null, [
            'where' => [
                'user_username' => $username
            ],
            'select' => [
                'user_first_name',
                'user_last_name',
                'user_profile_pic',
                'user_id',
                'user_password'
            ]
        ]);


        if (empty($data)) {
            throw new PublicAlert('Sorry, this Username and Password combination doesn\'t match out records.', 'warning');
        }

        $data = $data[0];

        // using the verify method to compare the password with the stored hashed password.
        if (APP_LOCAL && $password === $data['user_password']) {
            $_SESSION['id'] = $data['user_id'];    // returning the user's id.
        } else if (Bcrypt::verify($password, $data['user_password']) === true) {
            /* TODO - make sure email is sending
            if (!Users::email_confirmed($username)) {
                throw new PublicAlert('Sorry, you need to activate your account. Please check your email!');
            }
            */
            $_SESSION['id'] = $data['user_id'];    // returning the user's id.
        } else {
            throw new PublicAlert('Sorry, the username and password combination you have entered is invalid.', 'warning');
        }

        if ($rememberMe) {
            Request::setCookie('UserName', $username);
            Request::setCookie('FullName', $data['user_first_name'] . ' ' . $data['user_last_name']);
            Request::setCookie('UserImage', $data['user_profile_pic']);
        } else {
            (new Request)->clearCookies();
        }

        startApplication(true);

        return false;
    }

    /**
     * @param string $service
     * @param string|bool $request will map the the global scope
     * @return bool|mixed
     * @throws \CarbonPHP\Error\PublicAlert
     */
    public function oAuth($service, &$request)
    {
        global $UserInfo, $json;

        $UserInfo['service'] = ucfirst($service);

        $service = "user_{$service}_id";

        $sql = "SELECT user_id, $service FROM carbon_users WHERE user_email = ? OR $service = ?";

        $sql = self::fetch($sql, $UserInfo['email'], $UserInfo['id']);

        $user_id = $sql['user_id'] ?? false;

        $service_id = $sql[$service] ?? false;

        if (!$user_id && !$service_id) { // create new account
            if ($request === 'SignUp') {                         // This will set the session id
                Users::Post([
                    'username' => $UserInfo['username'],
                    'password' => $UserInfo['password'],
                    $service => $UserInfo['id'],
                    'profile_pic' => $UserInfo['picture'] ?? '',
                    'cover_photo' => $UserInfo['cover'] ?? '',
                    'email' => $UserInfo['email'],
                    'type' => 'Athlete',
                    'first_name' => $UserInfo['first_name'],
                    'last_name' => $UserInfo['last_name'],
                    'gender' => $UserInfo['gender']
                ]);

                Stats::Post([]);

            } else {

                $_SESSION['UserInfo'] = $UserInfo;

                $UserInfo['alert'] = 'It appears you do not already have an account with us.'; // Sign into a non-existing account

                $json = array_merge($json, $UserInfo);

                return true;
            }
        } elseif ($user_id && !$service_id) {
            if ($request === 'SignIn') {
                //Users::Put()

                $sql = "UPDATE carbon_users SET $service = ? WHERE user_id = ?";     // UPDATE user
                $this->db->prepare($sql)->execute([$UserInfo['id'], $_SESSION['id']]);
                $_SESSION['id'] = $user_id;
            } else {
                $_SESSION['UserInfo'] = $UserInfo;  // were trying to sign up when we need to sign in

                $UserInfo['alert'] = "You're {$UserInfo['service']} email address matches an account that has not previously been linked to this service.";

                $UserInfo['member'] = true;

                $json = $UserInfo;

                return true;
            }
        } else {
            $_SESSION['id'] = $user_id;
        }
        $_SESSION['UserInfo'] = $UserInfo = null;
        startApplication(true);
        return false;
    }

    /**
     * @param $user_id
     * @return bool
     * @throws PublicAlert
     */
    public function follow($user_id): bool
    {
        if (!$out = Users::user_exists($user_id)) {
            throw new PublicAlert("That user does not exist $user_id >> $out");
        }
        return Followers::Post([$user_id]);
    }

    /**
     * @param $user_id
     * @return bool
     * @throws PublicAlert
     */
    public function unfollow($user_id): bool
    {
        if (!Users::user_exists($user_id)) {
            throw new PublicAlert('That user does not exist?!');
        }
        Followers::Delete($this->user[$_SESSION['id']], $user_id);

        return true;

    }

    /**
     * @return bool
     * @throws PublicAlert
     */
    public function register(): bool
    {
        global $username, $password, $email, $firstName, $lastName, $gender;

        if (self::fetch('SELECT COUNT(*) FROM statscoach.carbon_users WHERE user_username = ? LIMIT 1', $username)['COUNT(*)']) {
            throw new PublicAlert ('That username already exists', 'warning');
        }

        if (self::fetch('SELECT COUNT(*) FROM statscoach.carbon_users WHERE user_email = ? LIMIT 1', $email)['COUNT(*)']) {
            throw new PublicAlert ('That email already exists.', 'warning');
        }

        // Tables self validate and throw public errors
        if (!$id = Users::Post([
            'user_type' => 'Athlete',
            'user_ip' => IP,
            'user_sport' => 'GOLF',
            'user_email_confirmed' => 1,
            'user_education_history' => '',
            'user_location' => '',
            'user_username' => $username,
            'user_password' => $password,       // TODO - encrypt password
            'user_email' => $email,
            'user_first_name' => $firstName,
            'user_last_name' => $lastName,
            'user_gender' => $gender
        ])) {
            throw new PublicAlert('Failed to create your account!');
        }

        if (!Stats::Post([
            'stats_id' => $id
        ])) {
            throw new PublicAlert('Failed to create your account!');
        }

        if (self::commit()) {

            $_SESSION['id'] = $id;

            PublicAlert::success('Welcome to Stats Coach. Please check your email to finish your registration.');

            startApplication('home/');
        } else {
            throw new PublicAlert('Failed to create your account!');
        }

        return false;
    }

    /**
     * @param $email
     * @param $email_code
     * @return bool
     * @throws PublicAlert
     */
    public function activate($email, $email_code): bool
    {
        if (!Users::email_exists($email)) {
            throw new PublicAlert('Please make sure the Url you have entered is correct.', 'danger');
        }

        $stmt = $this->db->prepare('SELECT COUNT(user_id) FROM carbon_users WHERE user_email = ? AND user_email_code = ?');
        $stmt->execute([$email, $email_code]);

        if ($stmt->fetch() === 0) {
            PublicAlert::warning('Sorry, you may be using an old activation code.');
            return startApplication(true);
        }

        if (!$this->db->prepare('UPDATE carbon_users SET user_email_confirmed = 1 WHERE user_email = ?')->execute(array($email)))
            throw new PublicAlert('The code provided appears to be invalid.', 'danger');


        $stmt = $this->db->prepare('SELECT user_id FROM carbon_users WHERE user_email = ?');
        $stmt->execute([$email]);
        $_SESSION['id'] = $stmt->fetchColumn();
        PublicAlert::success('We successfully activated your account.');
        startApplication(true); // there is not an activate template file
        exit(1);
    }

    /**
     * @param $email
     * @param $generated_string
     * @return bool
     * @throws PublicAlert
     */
    public function recover($email, $generated_string)
    {
        $alert = function () {
            throw new PublicAlert('An account could not be found with the email provided.', 'warning');
        };

        if (!Users::email_exists($email)) {
            $alert();
        }

        $generated = Bcrypt::genRandomHex(20);

        if (empty($generated_string)) {
            $sql = 'SELECT user_first_name FROM carbon_users WHERE user_email = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            $user_first_name = $stmt->fetchColumn();

            $stmt = $this->db->prepare('UPDATE carbon_users SET user_generated_string = ? WHERE user_email = ?');
            if (!$stmt->execute([$generated, $email])) {
                throw new PublicAlert('Sorry, we failed to recover your account.', 'danger');
            }
            $subject = 'Your' . SITE_TITLE . ' password';
            $headers = 'From: Support@Stats.Coach' . "\r\n" .
                'Reply-To: Support@Stats.Coach' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $message = "Hello " . $user_first_name . ",
            \r\nPlease click the link below:\r\n\r\n" . SITE . "Recover/" . base64_encode($email) . "/" . base64_encode($generated) . "/\r\n\r\n 
            We will generate a new password for you and send it back to your email.\r\n\r\n--" . SITE_TITLE;

            mail($email, $subject, $message, $headers);

            PublicAlert::info("If an account is found, an email will be sent to the account provided.");

        } else {
            $sql = 'SELECT user_id, user_first_name FROM carbon_users WHERE user_email = ? AND user_generated_string = ?';
            $stmt = $this->db->prepare($sql);
            if (!$stmt->execute([$email, $generated_string])) {
                $alert();
            }
            if (empty($user = $stmt->fetch())) {
                $alert();
            }

            $this->change_password($user['user_id'], $generated);
            $stmt = $this->db->prepare('UPDATE carbon_users SET user_generated_string = 0 AND user_email_code = 0 AND user_email_confirmed = 1 WHERE user_id = ?');
            $stmt->execute([$user['user_id']]);

            $subject = 'Your' . SITE_TITLE . ' password';
            $headers = 'From: Support@Stats.Coach' . "\r\n" .
                'Reply-To: Support@Stats.Coach' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $message = "Hello {$user['user_first_name']} ,\n\nYour your new password is: " . $generated .
                "\n\nPlease change your password once you have logged in using this password.\n\n-- " . SITE_TITLE;

            mail($email, $subject, $message, $headers);
            PublicAlert::success("Your password has been successfully reset.");
        }

        startApplication('login/');

        return false;
    }

    /**
     * @param bool $user_uri
     * @return User|null|bool
     * @throws PublicAlert
     */
    public function profile($user_uri)
    {
        if ($user_uri === 'DeleteAccount') {
            Users::Delete($this->user[$_SESSION['id']], $_SESSION['id'], []);
            Serialized::clear();
            startApplication(true);
            return false;
        }

        if (true !== $user_uri) {   // an actual user id
            global $user_id;
            $user_id = Users::user_id_from_uri($user_uri);
            if (!empty($user_id) && $user_id !== $_SESSION['id']) {
                new User($user_id);
                return true;
            }
        }

        carbon_users::Get($this->user[$_SESSION['id']], $_SESSION['id'], []);

        if (empty($_POST)) {
            return null;
        }

        // we can assume post is active then
        global $first, $last, $email, $gender, $dob, $password, $profile_pic, $about_me, $user_education_history;

        // $this->user === global $user
        $my = $this->user[$_SESSION['id']];

        //throw new PublicAlert($first);

        if (false === carbon_users::Put($my, $_SESSION['id'], [
            'user_profile_pic' => $profile_pic ?: $my['user_profile_pic'],
            'user_first_name' => $first ?: $my['user_first_name'],
            'user_birthday' => $dob ?: $my['user_birthday'],
            'user_last_name' => $last ?: $my['user_last_name'],
            'user_gender' => $gender ?: $my['user_gender'],
            'user_email' => $email ?: $my['user_email'],
            'user_password' =>  $password ? Bcrypt::genHash($password) : $my['user_password'],
            'user_email_confirmed' => $email ? 0 : $my['user_email_confirmed'],
            'user_education_history' => $user_education_history ?: $my['user_education_history'],
            'user_about_me' => $about_me ?: $my['user_about_me']])) {
            throw new PublicAlert('Sorry, we could not process your information at this time.', 'warning');
        }

        // Remove old picture
        if (!empty($profile_pic) && !empty($my['user_profile_pic']) && $profile_pic !== $my['user_profile_pic']) {
            unlink(SERVER_ROOT . $my['user_profile_pic']);
        }

        // Send new activation code
        if (!empty($email) && $email !== $my['user_email']) {
            $subject = 'Please confirm your email';
            $headers = 'From: ' . SYSTEM_EMAIL . "\r\n" .
                'Reply-To: ' . REPLY_EMAIL . "\r\n" .
                'X-Mailer: PHP/' . PHP_VERSION;

            $message = 'Hello ' . ($first ?: $my['user_first_name']) . ",
            \r\n Please visit the link below so we can activate your account:\r\n\r\n"
                . SITE . 'Activate/' . base64_encode($email) . '/' . base64_encode($my['user_email_code']) . "/ \r\n\r\n Happy Golfing \r\n--" . SITE;

            if (!mail($email ?: $my['user_email'], $subject, $message, $headers)) {
                throw new PublicAlert('Our email system failed.');
            }
            PublicAlert::success('Please check your email to activate your account!');
        } else {
            PublicAlert::success('Your account has been updated!');
        }
        startApplication('home/');

        return true;
    }

}


