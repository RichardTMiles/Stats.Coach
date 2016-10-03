<?php

namespace App\Controllers;
use App\ApplicationController as Controller;
use \App\Modules\Models\StoreFiles;

class Users extends Controller
{

    protected function login()
    {
        extract($this->data);

        if (empty($_POST) === false) {      // If forum already submitted
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            if (empty($username) === true || empty($password) === true) {
                $errors[] = 'Sorry, but we need your username and password.';
            } else {
                $parameter = 'verify';      // We can now check server side
            }
        }
        parent::pushModel(compact(array_keys(get_defined_vars())));
    }

    protected function logout()
    {
        // Because were just destroying the session, there is no need for a model push
        session_destroy();
        session_start();
        # header("Location: http://www.lilrichard.com/frame/index.php"); This shit don't work
        echo '<script type="text/javascript"> window.location = "http://www.stats.coach/" </script>';
        die();
    }

    protected function register()
    {
        extract($this->data);
        if (isset($_POST['submit'])) {
            if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
                $errors[] = 'All fields are required.';
            } else {
                if (!ctype_alnum($_POST['firstname'])) {
                    $errors[] = 'Your first name appears to contain numbers. Please enter a valid first name.';
                }
                if (!ctype_alnum($_POST['lastname'])) {
                    $errors[] = 'Your last name appears to contain numbers. Please enter YOUR last name.';
                }
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
                    $errors[] = 'Please enter a valid email address';
                }
                if (!ctype_alnum($_POST['username'])) {
                    $errors[] = 'Please enter a username with only alphabets and numbers';
                }
                if (strlen($_POST['password']) < 6) {
                    $errors[] = 'Your password must be at least 6 characters';
                } else if (strlen($_POST['password']) > 18) {
                    $errors[] = 'Your password cannot be more than 18 characters long';
                }

            }

            if (empty($errors) === true) {  //Everything seems valid, push to model for second verification

                $username = htmlentities($_POST['username']);
                $password = $_POST['password'];
                $email = htmlentities($_POST['email']);
                $firstName = htmlentities($_POST['firstname']);
                $lastName = htmlentities($_POST['lastname']);

                $parameter = 'verify';
            }
        }

        parent::pushModel(compact(array_keys(get_defined_vars())));

    }

    protected function activate()
    {
        extract($this->data);
        parent::pushModel(compact(array_keys(get_defined_vars())));
    }

    protected function recover()
    {
        extract($this->data);
        if (isset($_POST['email'])) {
            if (filter_var(($_POST['email']), FILTER_VALIDATE_EMAIL) == TRUE) {
                $email = $_POST['email'];
                unset($_POST['email']);
            } else if (isset($_POST['email']) & filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == FALSE) {
                $errors[] = 'You have entered an invalid email address.';
                unset($_POST['email']);
            }
        }
        parent::pushModel(compact(array_keys(get_defined_vars())));

    }

    protected function profile() {
        extract($this->data);

        parent::pushModel(compact(array_keys(get_defined_vars())));
    }

}



