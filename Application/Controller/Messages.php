<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 12/2/17
 * Time: 3:56 PM
 */

namespace Controller;

use Carbon\Request;
use Tables\Messages as Table;
use Tables\Users as U;

class Messages extends Request
{
    public function messages() {
        // list($us_id, $messages) = $this->post('user_id','message')->alnum();
        return true;
    }

    public function navigation() {
        return true;
    }

    public function chat($user_uri = false){
        global $user_id;

        $user_id = U::user_id_from_uri($user_uri) or die(1);        // if post isset we can assume an add

        if (!empty($_POST) && !empty(($string = (new \Carbon\Request)->post('message')->noHTML()->value())))
            Table::Post($this->user[$user_id], $user_id, $string);     // else were grabbing content (json, html, etc)

        Table::Get($this->user[$user_id], $user_id);

        return true;
    }
}