<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 12/2/17
 * Time: 3:56 PM
 */

namespace Controller;


use Carbon\Request;

class Messages
{
    public function messages() {
        list($us_id, $messages)=(new Request())->post('user_id','message')->alnum();


        return true;


    }
}