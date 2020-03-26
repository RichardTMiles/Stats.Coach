<?php

namespace Model;

use CarbonPHP\Session;
use Exception;
use Model\Helpers\GlobalMap;


use Tables\carbon_user_followers;
use Tables\carbon_user_golf_stats as Stats;
use Tables\carbon_users as Users;
use Tables\carbon_user_followers as Followers;
use Tables\carbon_user_messages as Messages;


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
     * @throws Exception
     * @deprecated TODO - ??
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


    public static function followers($id) {
        return self::fetchColumn('SELECT HEX(user_id) FROM carbon_user_followers WHERE follows_user_id = UNHEX(?)', $id);
    }

    /**
     * @param $id
     * @throws PublicAlert
     */
    public function listFollowers($id) {
        global $json;
        $users = self::followers($id);
        foreach ($users as &$value) {
            $value = getUser($value, 'Basic');
        }
        $json['followers'] = $users;
    }


    public static function following($id) {
        return self::fetchColumn('SELECT HEX('.carbon_user_followers::FOLLOWS_USER_ID.') FROM carbon_user_followers WHERE user_id = UNHEX(?)', $id);
    }

    /**
     * @param $id
     * @throws PublicAlert
     */
    public function listFollowing($id) {
        global $json;
        $users = self::followers($id);
        foreach ($users as &$value) {
            $value = getUser($value, 'Basic');
        }
        unset($value);
        $json['following'] = $users;
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
                Users::USER_USERNAME => $username
            ],
            'select' => [
                Users::USER_FIRST_NAME,
                Users::USER_LAST_NAME,
                Users::USER_PROFILE_PIC,
                Users::USER_ID,
                Users::USER_PASSWORD
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

        } else if ($password === $data['user_password']) {
            $_SESSION['id'] = $data['user_id'];    // returning the user's id.
            PublicAlert::warning('Password encryption not detected, be sure to update before live release.');
        } else {
            throw new PublicAlert('Sorry, this Username and Password combination doesn\'t match out records.', 'warning');
        }

        if ($rememberMe) {
            Request::setCookie('UserName', $username);
            Request::setCookie('FullName', $data['user_first_name'] . ' ' . $data['user_last_name']);
            Request::setCookie('UserImage', $data['user_profile_pic']);
        } else {
            (new Request)->clearCookies();
        }

        return startApplication(true);
    }

    /**
     * @param string $service
     * @param string|bool $request will map the the global scope
     * @return bool|mixed
     */
    public function oAuth($service, &$request)
    {
        global $UserInfo, $json;

        $UserInfo['service'] = ucfirst($service);

        $json['UserInfo'] = &$UserInfo;

        $sql = [];

        if (false === Users::Get($sql, null, [
            'select' => [
                Users::USER_ID,
                $service === 'google'
                    ? $service = Users::USER_GOOGLE_ID
                    : $service = Users::USER_FACEBOOK_ID
            ],
            'where' => [
                [
                    Users::USER_EMAIL => $UserInfo['email'],
                    $service => $UserInfo['id']
                ]
            ],
            'pagination' => [
                'limit' => 1
            ]
        ])) {
            $json['oauth'] = ('Failed to lookup user OAuth.');
            return null;
        }

        $user_id = $sql['user_id'] ?? false;

        $service_id = $sql[$service] ?? false;

        if (!$user_id && !$service_id) { // create new account
            if ($request === 'SignUp') {                         // This will set the session id
                $id = Users::Post([
                    Users::USER_USERNAME  => $UserInfo['username'],
                    Users::USER_PASSWORD => $UserInfo['password'],
                    $service => $UserInfo['id'],
                    Users::USER_PROFILE_PIC => $UserInfo['picture'] ?? '',
                    Users::USER_COVER_PHOTO => $UserInfo['cover'] ?? '',
                    Users::USER_EMAIL => $UserInfo['email'],
                    Users::USER_TYPE => 'Athlete',
                    Users::USER_FIRST_NAME => $UserInfo['first_name'],
                    Users::USER_LAST_NAME => $UserInfo['last_name'],
                    Users::USER_GENDER => $UserInfo['gender'],
                    Users::USER_IP => IP,
                ]);

                Stats::Post([
                    'stats_id' => $id
                ]);
                $_SESSION['id'] = $id;

                if (!self::commit()) {
                    PublicAlert::danger('Failed to add oAuth user.');
                    return startApplication('login');
                }

            } else {

                $_SESSION['UserInfo'] = $UserInfo;

                $UserInfo['alert'] = 'It appears you do not already have an account with us.'; // Sign into a non-existing account

                $json = array_merge($json, $UserInfo);

                return true;
            }
        } elseif ($user_id && !$service_id) {
            if ($request === 'SignIn') {
                $sql = "UPDATE carbon_users SET $service = ? WHERE user_id = HEX(?)";     // UPDATE user
                self::execute($sql, $UserInfo['id'], $_SESSION['id']);
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

        return startApplication(true);
    }

    /**
     * @param $user_id
     * @return bool
     * @throws PublicAlert
     */
    public function follow($user_id): bool
    {
        global $json;

        if (!$out = getUser($user_id)) {
            throw new PublicAlert("That user does not exist $user_id >> $out");
        }
        if (false === Followers::Post([
                Followers::USER_ID => $_SESSION['id'],
                Followers::FOLLOWS_USER_ID => $user_id
            ])) {
            PublicAlert::warning('Could not follow user!');
        } elseif (self::commit(
            function () use ($user_id) {
                self::sendUpdate($user_id, 'NavigationMessages');
                return true;
            }
        )) {
            $json['success'] = true;
        }
        return true;
    }

    /**
     * @param $user_id
     * @return bool
     * @throws PublicAlert
     */
    public function unfollow($user_id): bool
    {
        global $json;

        if (!getUser($user_id)) {
            PublicAlert::warning("That user does not exist $user_id");
        }

        if (false === Followers::Delete($this->user[$_SESSION['id']], null, [
                'follows_user_id' => $user_id,
                'user_id' => $_SESSION['id']
            ])) {
            PublicAlert::warning('Could not unfollow user.');
        } else {
            $json['success'] = true;
        }

        return true;

    }

    /**
     * @return bool
     * @throws PublicAlert
     */
    public function register(): bool
    {
        global $username, $password, $email, $firstName, $lastName, $gender;

        if (self::fetch('SELECT COUNT(*) FROM StatsCoach.carbon_users WHERE user_username = ? LIMIT 1', $username)['COUNT(*)']) {
            throw new PublicAlert ('That username already exists', 'warning');
        }

        if (self::fetch('SELECT COUNT(*) FROM StatsCoach.carbon_users WHERE user_email = ? LIMIT 1', $email)['COUNT(*)']) {
            throw new PublicAlert ('That email already exists.', 'warning');
        }

        // Tables self validate and throw public errors
        if (!$id = Users::Post([
            Users::USER_TYPE => 'Athlete',
            Users::USER_IP => IP,
            Users::USER_SPORT => 'GOLF',
            Users::USER_EMAIL_CONFIRMED => 1,
            Users::USER_EDUCATION_HISTORY => '',
            Users::USER_LOCATION => '',
            Users::USER_USERNAME => $username,
            Users::USER_PASSWORD => $password,       // TODO - encrypt password
            Users::USER_EMAIL => $email,
            Users::USER_FIRST_NAME => $firstName,
            Users::USER_LAST_NAME => $lastName,
            Users::USER_GENDER => $gender
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

            startApplication('home/'); // TODO - Im not going to return this but I feel like I should... idk
            return false;
        } else {
            throw new PublicAlert('Failed to create your account!');
        }
    }

    /**
     * @param $email
     * @param $email_code
     * @return bool
     */
    public function activate($email, $email_code): bool
    {
        $user = [];

        if (false === Users::Get($user, null, [
                'select' => [
                    Users::USER_ID
                ],
                'where' => [
                    Users::USER_EMAIL => $email,
                    Users::USER_EMAIL_CODE => $email_code
                ],
                'pagination' => [
                    'limit' => 1
                ]
            ])) {
            PublicAlert::danger('The Rest API Failed.');
            return startApplication(true);
        }

        if (empty($user)) {
            PublicAlert::warning('Sorry, you may be using an old activation code.');
            return startApplication(true);
        }

        if (false === Users::Put($user, $user['user_id'], [
                Users::USER_EMAIL_CONFIRMED => 1
            ])) {
            PublicAlert::danger('The Rest API Failed.');
            return startApplication(true);
        }

        $_SESSION['id'] = $user['user_id'];
        PublicAlert::success('We successfully activated your account.');
        return startApplication(true); // there is not an activate template file
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

        startApplication('login/'); // TODO - returning start app requires the mvc pattern be used here

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
            if (false === Users::Delete($this->user[$_SESSION['id']], $_SESSION['id'], [])) {
                throw new PublicAlert('Failed to delete user.');
            }
            Serialized::clear();
            $_SESSION['id'] = false;
            self::commit();
            return startApplication(true);
        }

        if (true !== $user_uri) {   // !! an actual user id -- NOT US
            global $json, $user;

            getUser($user_uri);

            $user[$user_uri]['following'] = [];

            if (!carbon_user_followers::Get($user[$user_uri]['following'], null, [
                    'where' => [
                        carbon_user_followers::USER_ID => $_SESSION['id'],
                        carbon_user_followers::FOLLOWS_USER_ID => $user_uri
                    ],
                    'pagination' => [
                        'limit' => 1
                    ]
                ]
            )) {
                PublicAlert::warning('Failed to look up following status!');
            }

            $json['my'] = $user[$user_uri];
            return true;
        }

        if (empty($_POST)) {
            return null;
        }

        // we can assume post is active then TODO - lets remove this global ref
        global $first, $last, $email, $gender, $dob, $password, $profile_pic, $about_me, $user_education_history;

        // $this->user === global $user
        $my = $this->user[$_SESSION['id']];

        //throw new PublicAlert($first);

        // todo - shrink this?
        if (false === Users::Put($my, $_SESSION['id'], [
                Users::USER_PROFILE_PIC => $profile_pic ?: $my['user_profile_pic'],
                Users::USER_FIRST_NAME => $first ?: $my['user_first_name'],
                Users::USER_BIRTHDAY => $dob ?: $my['user_birthday'],
                Users::USER_LAST_NAME => $last ?: $my['user_last_name'],
                Users::USER_GENDER => $gender ?: $my['user_gender'],
                Users::USER_EMAIL => $email ?: $my['user_email'],
                Users::USER_PASSWORD => $password ? Bcrypt::genHash($password) : $my['user_password'],
                Users::USER_EMAIL_CONFIRMED => $email ? 0 : $my['user_email_confirmed'],
                Users::USER_EDUCATION_HISTORY => $user_education_history ?: $my['user_education_history'],
                Users::USER_ABOUT_ME => $about_me ?: $my['user_about_me']])) {
            throw new PublicAlert('Sorry, we could not process your information at this time.', 'warning');
        }

        // Remove old picture
        if (!empty($profile_pic) && !empty($my['user_profile_pic']) && $profile_pic !== $my['user_profile_pic']) {
            unlink(APP_ROOT . $my['user_profile_pic']);
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
            PublicAlert::info('You may need to refresh the page to see all changes made.');
        }

        Session::update();

        return null;
    }

}


