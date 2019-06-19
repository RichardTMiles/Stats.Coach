<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 12/2/17
 * Time: 3:56 PM
 */

namespace Controller;

use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Request;
use Tables\carbon_user_messages as message;


class Messages extends Request
{
    public function messages($user_uri = false)
    {
        // list($us_id, $messages) = $this->post('user_id','message')->alnum();

        if (ctype_xdigit($user_uri)) {
            return $user_uri;
        }
        return true;
    }

    public function navigation()
    {
        return true;
    }

    public function chat($user_id = false)
    {
        if (!ctype_xdigit($user_id)) {
            PublicAlert::danger('Invalid user id given for chat.');
            return null;
        }

        $message = $this->post('message')->noHTML()->value();

        return [$user_id, $message];
    }
}