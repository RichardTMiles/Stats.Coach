<?php

namespace Controller;

use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Helpers\Bcrypt;
use CarbonPHP\Request;
use CarbonPHP\Session;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Google_Client;

//use Table\Users;

class User extends Request
{
    public static function logout(): bool
    {
        Session::clear();
        startApplication(true);
        return false;
    }


    public function listFollowers($id = null) {
        if ($id === null) {
            return $_SESSION['id'];
        }
        if (!ctype_xdigit($id)) {
            PublicAlert::danger('Could not look up user followers!');
            return startApplication('home/');
        }
        return $id;
    }

    public function listFollowing($id = null) {
        if ($id === null) {
            return $_SESSION['id'];
        }
        if (!ctype_xdigit($id)) {
            PublicAlert::danger('Could not look up the users following!');
            return startApplication('home/');
        }
        return $id;
    }




    /**
     * @return array|bool
     * @throws PublicAlert
     * @throws \Google_Exception
     */
    public function login()
    {
        global $json, $UserName, $FullName, $UserImage;    // validate cookies

        [$UserName, $FullName] = $this->cookie('UserName', 'FullName')->alnum();

        $UserImage = $this->cookie('UserImage')->value();

        $UserImage = file_exists(SERVER_ROOT . $UserImage) ? SITE . $UserImage : false;

        $rememberMe = $this->post('RememberMe')->int();

        if (!$rememberMe) {
            $this->cookie('username', 'password', 'RememberMe')->clearCookies();
        }

        $json['google_url'] = self::urlGoogle('SignIn');

        $json['facebook_url'] = self::urlFacebook('SignIn');

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
     * @param $service
     * @param null $request
     * @return array|bool|null|string
     * @throws PublicAlert
     * @throws \Google_Exception
     */
    public function oAuth($service, &$request = null)
    {
        global $UserInfo;

        [$service, $request] = $this->set($service, $request)->word();

        $service = strtolower($service);

        if (array_key_exists('UserInfo', $_SESSION) && \is_array($_SESSION['UserInfo'])) {
            $UserInfo = $_SESSION['UserInfo'];  // Pull this from the session
        }

        if (!\is_array($UserInfo)) {
            if ($service === 'google'){
                $UserInfo = self::urlGoogle($request);
            } elseif ($service === 'facebook') {
                $UserInfo = self::urlFacebook($request);
            }
            if (!\is_array($UserInfo)) {
                return startApplication('login/');  // return null, bc were in a
            }

            return [$service, &$request];    // return the view
        }

        if ($request === 'SignUp') {
            [$username, $first_name, $last_name, $gender]
                = $this->post('username', 'firstname', 'lastname', 'gender')->alnum();

            [$password, $verifyPass]
                = $this->post('password', 'password2')->value();

            $email = $this->post('email')->email();

            $terms = 'true' === ($_POST['Terms']  ?? false);

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

            $UserInfo['first_name'] = $first_name;
            $UserInfo['last_name'] = $last_name;
            $UserInfo['gender'] = $gender;
            $UserInfo['email'] = $email;
            $UserInfo['username'] = $username;
            $UserInfo['password'] = $password;
            $_SESSION['UserInfo'] = $UserInfo;

            return [$service, &$request];
        }


        return [$service, &$request];
    }

    public function follow($user_id)
    {
        global $json;

        $json = [];

        if (!ctype_xdigit($user_id)) {
            return null;
        }

        return $user_id;
    }

    public function unfollow($user_id)
    {
        return $this->follow($user_id); // its the same check.
    }

    public function messages()
    {

    }

    /**
     * @return bool|null
     * @throws PublicAlert
     */
    public function register() : ?bool
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

        $terms = $_POST['Terms'] === 'true';

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

        APP_LOCAL OR $password = Bcrypt::genHash($password);

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
            return startApplication(true);
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
     * @param  string|array|mixed $user_id - validating for string
     * @return array|bool|mixed
     * @throws PublicAlert
     */
    public function profile($user_id = false)
    {
        global $json;


        $user_id = $this->set($user_id)->alnum();

        if (!empty($user_id)) { // we can assume no post data then
            return $user_id;
        }

        $json['myAccountBool'] = true; // no id set so its our profile


        if (empty($_POST)) {
            return null;        // don't go onto the model, but run the view
        }

        if (!$this->post('Terms')->value() === 'true') {
            throw new PublicAlert('Sorry, you must accept the terms and conditions.', 'warning');
        }

        // Our forum variables get put in the public scope TODO - make this better
        global $first, $last, $email, $gender, $dob, $password, $profile_pic, $about_me;

        [$first, $last, $gender] = $this->post('first_name', 'last_name', 'gender')->word();

        $dob = $this->post('datepicker')->date();

        $email = $this->post('email')->email();

        $password = $this->post('password')->value();

        $about_me = $this->post('about_me')->text();

        //sortDump($_POST['FileToUpload']);

        if ($_POST['FileToUpload'])
            $profile_pic = $this->files('FileToUpload')->storeFiles('Data/Uploads/Pictures/Profile/');

        return true; // to the model
    }

    public function settings()
    {
        return false;
    }


    static function urlFacebook($request = null)
    {
        try {
            $fb = new Facebook([
                'app_id' => FACEBOOK_APP_ID,            // Replace {app-id} with your app id
                'app_secret' => FACEBOOK_APP_SECRET,
                'default_graph_version' => 'v2.12',
                'http_client_handler' => 'stream',      // better compatibility
            ]);


            if (isset($_GET['state'])) {
                $_SESSION['FBRLH_state'] = $_GET['state'];
            }

            $helper = $fb->getRedirectLoginHelper();


            #if (isset($_SESSION['facebook_access_token'])) {
            #    $accessToken = $_SESSION['facebook_access_token'];
            #} else {
            $accessToken = $helper->getAccessToken();
            #}
        } catch (FacebookSDKException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        }

        if (null !== $accessToken) {
            if (isset($_SESSION['facebook_access_token'])) {
                $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
            } else {
                // getting short-lived access token
                $_SESSION['facebook_access_token'] = (string)$accessToken;
                // OAuth 2.0 client handler
                $oAuth2Client = $fb->getOAuth2Client();
                // Exchanges a short-lived access token for a long-lived one
                $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
                $_SESSION['facebook_access_token'] = (string)$longLivedAccessToken;
                // setting default access token to be used in script
                $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
            }
            // redirect the user back to the same page if it has "code" GET variable
            #if (isset($_GET['code'])) {
            ##   header('Location: ./');
            #}
            // getting basic info about user

            try {
                $profile_request = $fb->get('/me?fields=name,first_name,last_name,email,gender,cover,picture', $_SESSION['facebook_access_token']);
                $profile = $profile_request->getGraphNode()->asArray();
            } catch (FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                // redirecting user back to app login page
                exit;
            } catch (FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
            // Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
        } else {
            try {
                // replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
                return $helper->getLoginUrl(SITE . 'oAuth/Facebook/' . $request . DS, [
                    'public_profile', 'user_friends', 'email',
                    'user_birthday',
                    'user_hometown',
                    'user_location', 'user_photos', 'user_friends']);
            } catch (FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
        }

        self::changeURI(SITE . 'oAuth/Facebook/?much=love');  // clear GET data.

        return array(
            'id' => $profile['id'],
            'first_name' => $profile['first_name'] ?? '',
            'last_name' => $profile['last_name'] ?? '',
            'email' => $profile['email'] ?? '',
            'gender' => $profile['gender'] ?? '',
            'picture' => $profile['picture']['url'] ?? '',
            'cover' => $profile['cover']['source'] ?? '',
        );

    }

    /**
     * @param null $request
     * @return array|string
     * @throws Google_Exception
     * @throws \CarbonPHP\Error\PublicAlert
     * @throws \Google_Exception
     */
    static function urlGoogle($request = null)
    {
        //Call Google API
        $client = new Google_Client();
        $client->setApplicationName('Stats.Coach');
        $client->setAuthConfig(APP_ROOT . 'config/gAuth.json');

        if ($request !== null) {
            $request .= DS;
            $client->setRedirectUri('https://stats.coach/oAuth/Google/' . $request);
        }

        $client->setIncludeGrantedScopes(true);   // incremental auth
        $client->addScope('profile');
        $client->addScope('email');

        $google = new \Google_Service_Oauth2($client);


        if (!isset($_GET['code'])) {
            return $client->createAuthUrl();
        }

        $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if ($accessToken) {

            $client->setAccessToken($accessToken);

            //Get user profile data from google
            $gpUserProfile = $google->userinfo->get();

            //Insert or update user data to the database
            return array(
                'id' => $gpUserProfile['id'],
                'first_name' => $gpUserProfile['given_name'],
                'last_name' => $gpUserProfile['family_name'],
                'email' => $gpUserProfile['email'],
                'gender' => $gpUserProfile['gender'],
                'locale' => $gpUserProfile['locale'],
                'picture' => $gpUserProfile['picture'],
                'link' => $gpUserProfile['link']
            );
        }
        throw new PublicAlert('failed to get access token');
    }


}

