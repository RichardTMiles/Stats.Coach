<?php

/*
The user relay runs through out Database connection code, no PDO is actually run here
Do not use try catch, as it is not needed

Email needs to be edited in function "register"
*/

namespace Model\Helpers;


use Modules\Helpers\QuickFetch;

abstract class UserRelay extends QuickFetch
{
    public $user_id;
    public $user_type;
    public $user_sport;
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
}












