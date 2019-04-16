<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 12/2/17
 * Time: 3:56 PM
 */

namespace Model;

use CarbonPHP\Error\PublicAlert;
use Model\Helpers\GlobalMap;
use Tables\carbon_user_messages as Message;
use Tables\carbon_users as Users;

class Messages extends GlobalMap
{
    public function messages()
    {
        global $json, $user;

        $json['widget'] = '#NavMessages';

        foreach ($user as $id => $info) {
            if ($id == $_SESSION['id'])
                continue;
            $json['users'][] = array(
                'user_id' => $info['user_id'],
                'user_profile_pic' => $info['user_profile_pic'],
                'user_profile_url' => $info['user_profile_uri'],
                'user_full_name' => $info['user_full_name'],
                'user_last_login' => date('D, d M Y', $info['user_last_login'])
            );
        }
    }

    public function navigation()
    {
        global $user, $json;
        $unread = 0;

        $json['widget'] = '#NavMessages';

        foreach ($user as $id => $their) {
            if ($id == $_SESSION['id'])
                continue;

            if ($their['messages'] ?? false) {
                $message = $their['messages'][count($their['messages']) - 1];
                $json['users'][] = [
                    'first_name' => $their['user_first_name'],
                    'last_name' => $their['user_last_name'],
                    'user_uri' => SITE . 'Profile' . DS . $their['user_profile_uri'] . DS,
                    'user_profile_picture' => $their['user_profile_pic'],
                    'creation_date' => date("F j, g:i a", $message['creation_date']),
                    'message' => $message['message']
                ];
                if (!$message['message_read']) $unread++;
            }
        }

        if ($unread) $json['newMessages'] = $unread;

        return $json;
    }

    public function chat($user_id)
    {
        global $json;

        $this->user[$user_id] = [];

        if (false === Users::get($this->user[$user_id], $user_id, [])) {
            throw new PublicAlert('Failed to get restful user in chat.');
        }

        if (empty($this->user[$user_id])) {
            throw new \Exception('Could not find user.');
        }

        $this->user[$user_id]['messages'] = [];

        if (!empty($_POST) && !empty($string = $this->post('message')->noHTML()->value())) {
            Message::Post($this->user[$user_id]['messages'], $user_id, $string);
        }     // else were grabbing content (json, html, etc)

        Message::get($this->user[$user_id]['messages'], $user_id, [
            'where' => [
                [
                    'to_user_id' => $_SESSION['id'],
                    'from_user_id' => $user_id
                ],
                [
                    'to_user_id' => $user_id,
                    'from_user_id' => $_SESSION['id'],
                ],
            ]
        ]);


        $json = array_merge($json, [
            'Widget' => '.direct-chat',
            'scroll' => '#messages',
            'to_User' => $user_id,
            'scrollTo' => 'bottom'
        ]);

        foreach ($this->user[$user_id]['messages'] as $key => $message) {
            $json['Messages'][] = [
                'me' => $message['user_id'] == $_SESSION['id'],
                'first_name' => $user[$message['user_id']]['user_first_name'],
                'user_profile_picture' => $user[$message['user_id']]['user_profile_pic'],
                'creation_date' => date("F j, Y, g:i a", $message['creation_date']),
                'message' => $message['message']
            ];
        }

        return $json;
    }
}