<?php

namespace Controller;

use Carbon\Error\PublicAlert;
use Carbon\Request;
use Carbon\Session;

class User extends Request
{
    public static function logout(): bool
    {
        Session::clear();
        startApplication(true);
        return false;
    }

    /**
     * @return array|bool
     * @throws PublicAlert
     */
    public function login()
    {
        global $UserName, $FullName, $UserImage;    // validate cookies

        [$UserName, $FullName] = $this->cookie('UserName', 'FullName')->alnum();

        $UserImage = $this->cookie('UserImage')->value();

        $UserImage = file_exists(SERVER_ROOT . $UserImage) ? SITE . $UserImage : false;

        $rememberMe = $this->post('RememberMe')->int();

        if (!$rememberMe) {
            $this->cookie('username', 'password', 'RememberMe')->clearCookies();
        }

        if (empty($_POST)) {
            return null;                    // returning null will show the view but not execute the model
        }  // If forum already submitted

        $username = $this->post('username')->alnum();

        $password = $this->post('password')->value();

        if (!$username || !$password) {
            throw new PublicAlert('Sorry, but we need your username and password.');
        }

        return [$username, $password, $rememberMe];
    }

    /**
     * @param null $request
     * @return bool
     * @throws PublicAlert
     */
    public function facebook($request = null): bool
    {
        global $facebook;

        if ($request === 'SignUp' || (array_key_exists('facebook', $_SESSION) && !empty($_SESSION['facebook']))) {

            $facebook = $_SESSION['facebook'] ?? $facebook;  // Pull this from the session

            [$username, $first_name, $last_name, $gender]
                = $this->post('username', 'firstname', 'lastname', 'gender')->alnum();

            [$password, $verifyPass]
                = $this->post('password', 'password2')->value();

            $email = $this->post('email')->email();

            $terms = $this->post('Terms')->int();

            if (!$username) {
                throw new PublicAlert('Please enter a username with only numbers & letters!');
            }

            if (!$gender) {
                throw new PublicAlert('Sorry, please enter your gender.');
            }

            if (!$password || \strlen($password) < 6) {
                throw new PublicAlert('Sorry, your password must be more than 6 characters!');
            }

            if ($password !== $verifyPass) {
                throw new PublicAlert('The passwords entered must match!');
            }

            if (!$email) {
                throw new PublicAlert('Please enter a valid email address!');
            }

            if (!$first_name) {
                throw new PublicAlert('Please enter your first name!');
            }

            if (!$last_name) {
                throw new PublicAlert('Please enter your last name!');
            }

            if (!$terms) {
                throw new PublicAlert('You must agree to the terms and conditions.');
            }

            $facebook['first_name'] = $first_name;
            $facebook['last_name'] = $last_name;
            $facebook['gender'] = $gender;
            $facebook['email'] = $email;
            $facebook['username'] = $username;
            $facebook['password'] = $password;
            return (new Request())->set($request)->alnum() ?: true;

        }
        if ((include SERVER_ROOT . 'Application/Model/Helpers/fb-callback.php') === false) {
            throw new PublicAlert('Sorry, we could not connect to Facebook. Please try again later.');
        }

        return (new Request())->set($request)->alnum() ?: true;
    }

    /**
     * @param $request
     */
    public function google($request)
    {

    }

    public function follow($user_id)
    {
        return $this->set($user_id)->alnum();
    }

    public function unfollow($user_id)
    {
        return $this->set($user_id)->alnum();
    }

    public function messages()
    {

    }

    /**
     * @return bool|null
     * @throws PublicAlert
     */
    public function register(): ?bool
    {
        if (empty($_POST)) {
            return null;
        }

        global $username, $password, $firstName, $lastName, $gender, $userType, $teamCode, $teamName, $schoolName, $email;

        [$username, $firstName, $lastName, $gender, $userType, $teamCode]
            = $this->post('username', 'firstname', 'lastname', 'gender', 'UserType', 'teamCode')->alnum();

        [$teamName, $schoolName]
            = $this->post('teamName', 'schoolName')->text();

        [$password, $verifyPass]
            = $this->post('password', 'password2')->value();

        $email = $this->post('email')->email();

        $terms = $this->post('Terms')->int();

        if (!$username) {
            throw new PublicAlert('Please enter a username with only numbers & letters!');
        }

        if (!$gender) {
            throw new PublicAlert('Sorry, please enter your gender.');
        }

        if (!$password || \strlen($password) < 6) {
            throw new PublicAlert('Sorry, your password must be more than 6 characters!');
        }

        if ($password !== $verifyPass) {
            throw new PublicAlert('The passwords entered must match!');
        }

        if (!$email) {
            throw new PublicAlert('Please enter a valid email address!');
        }

        if (!$firstName) {
            throw new PublicAlert('Please enter your first name!');
        }

        if (!$lastName) {
            throw new PublicAlert('Please enter your last name!');
        }
        if (!$terms) {
            throw new PublicAlert('You must agree to the terms and conditions.');
        }
        return true;
    }

    public function activate($email, $email_code = null)
    {
        $email = $this->set($email)->base64_decode()->email();
        $email_code = $this->set($email_code)->base64_decode()->value();

        if (!$email) {
            PublicAlert::warning('Sorry the url submitted is invalid.');
            return startApplication(true); // who knows what state we're in, best just restart.
        }
        return [$email, $email_code];
    }

    /**
     * @param null $user_email
     * @param null $user_generated_string
     * @return array|null
     * @throws PublicAlert
     */
    public function recover($user_email = null, $user_generated_string = null): ?array
    {
        if (!empty($user_email) && !empty($user_generated_string)) {

            [$user_email, $user_generated_string] = $this->set($user_email, $user_generated_string)->base64_decode()->value();

            if (!$this->set($user_email)->email()) {
                throw new PublicAlert('The code provided appears to be invalid.');
            }

            return [$user_email, $user_generated_string];
        }

        if (empty($_POST)) {
            return null;
        }

        global $user_email;

        if (!$user_email = $this->post('user_email')->email()) {
            throw new PublicAlert('You have entered an invalid email address.');
        }

        return [$user_email, false];
    }

    /**
     * @param  $user_id
     * @return array|bool|mixed
     * @throws PublicAlert
     */
    public function profile($user_id = false)
    {
        $user_id = $this->set($user_id)->alnum();

        if (false !== $user_id) {
            return $user_id;
        }

        if (empty($_POST)) {
            return null;        // don't go onto the model, but run the view
        }

        if (!$this->post('Terms')->int()) {
            throw new PublicAlert('Sorry, you must accept the terms and conditions.', 'warning');
        }

        global $first, $last, $email, $gender, $dob, $password, $profile_pic, $about_me;

        [$first, $last, $gender] = $this->post('first_name', 'last_name', 'gender')->word();

        $dob = $this->post('datepicker')->date();

        $email = $this->post('email')->email();

        $password = $this->post('password')->value();

        $about_me = $this->post('about_me')->text();

        $profile_pic = $this->files('FileToUpload')->storeFiles('Data/Uploads/Pictures/Profile/');

        return true;
    }

    public function settings()
    {
        return false;
    }

}

