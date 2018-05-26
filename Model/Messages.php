<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 12/2/17
 * Time: 3:56 PM
 */

namespace Model;

use Model\Helpers\GlobalMap;

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

    public function chat()
    {
        global $user_id, $user, $json;

        if (!(is_array($account = $user[$user_id] ?? false)))
            throw new \Exception('User not loaded');

        $json = [
            'widget' => '.direct-chat',
            'scroll' => '#messages',
            'to_User' => $user_id,
            'scrollTo' => 'bottom'
        ];

        foreach ($account['messages'] as $key => $message) {
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