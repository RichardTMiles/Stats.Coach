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

        $json['Widget'] = '#NavMessages';

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

    public function chat($user_id, $message = false)
    {
        global $json, $user;

        $this->user[$user_id] = [];

        if (false === Users::get($this->user[$user_id], $user_id, [
                'select' => [
                    'user_id',
                    'user_first_name',
                    'user_last_name',
                    'user_profile_pic',
                ]
            ])) {
            throw new PublicAlert('Failed to get restful user in chat.');
        }

        if (empty($this->user[$user_id])) {
            throw new \Exception('Could not find user.');
        }

        $this->user[$user_id]['messages'] = [];

        $json = array_merge($json, [
            'scroll' => '#messages',
            'to_User' => $user_id,
            'scrollTo' => 'bottom'
        ]);

        if ($message && !$json['message_id'] = Message::Post([
                'from_user_id' => $_SESSION['id'],
                'to_user_id' => $user_id,
                'message' => $message
            ])) {
            PublicAlert::warning('Failed to send message :( Please try again later');
        }     // else were grabbing content (json, html, etc)


        if (!self::commit(function(){
            // toDO - find the message notifications
            #self::sendUpdate($user_id, '');

            return true;
        })) {
            PublicAlert::warning('Failed to commit transaction :( Please try again later');
        }

        Message::get($this->user[$user_id]['messages'], null, [
            'where' => [
                [
                    [
                        'to_user_id' => $_SESSION['id'],
                        'from_user_id' => $user_id,
                    ],
                    [
                        'to_user_id' => $user_id,
                        'from_user_id' => $_SESSION['id'],
                    ]
                ],
            ],
            'pagination' => [
                'limit' => '100',
                'order' => 'creation_date ASC'

            ]
        ]);

        // this is rough, but it needs to get formatted for mustache templates
        foreach ($user[$user_id]['messages'] as $key => $message) {
            $json['Messages'][] = [
                'me' => $message['from_user_id'] === $_SESSION['id'],
                'first_name' => $user[$message['to_user_id']]['user_first_name'],
                'user_profile_picture' => $user[$message['to_user_id']]['user_profile_pic'],
                'creation_date' => $message['creation_date'], //date("F j, Y, g:i a", $message['creation_date']),
                'message' => $message['message']
            ];
        }

        return $json;
    }
}