<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 12/2/17
 * Time: 3:56 PM
 */

namespace Model;

use CarbonPHP\Error\PublicAlert;
use Exception;
use Model\Helpers\GlobalMap;
use Tables\carbon_user_messages as Message;
use Tables\carbon_user_notifications;
use Tables\carbon_users as Users;

class Messages extends GlobalMap
{

    /** A little misleading because I want all users who have sent a message to appear in the chat
     * @return array
     * @throws PublicAlert
     */
    public static function unreadMessages() {
         $users = self::fetchColumn('SELECT distinct(HEX(from_user_id)) FROM StatsCoach.carbon_user_messages WHERE to_user_id = UNHEX(?) AND message_read = 0', $_SESSION['id']);
         foreach ($users as &$value) {
             $value = self::fetch('SELECT HEX(user_id) as user_id, user_first_name, user_last_name, user_profile_pic, message, creation_date FROM carbon_users JOIN carbon_user_messages ON from_user_id = user_id WHERE message_read = 0 AND from_user_id = UNHEX(?) ORDER BY creation_date DESC LIMIT 1', $value);
         }
         return $users;
    }


    /**
     * @throws PublicAlert
     */
    public function messages()
    {
        global $json, $user;

        foreach ($user[$_SESSION['id']]['friends'] as $pos => $id) {
            if ($id === $_SESSION['id'])
                continue;
            getUser($id, 'Basic');

            $json['friends'] = $user[$id];
        }
    }

    /** GOOD for sockets
     * @return mixed
     */
    public function navigation()
    {
        global $user, $json;
        $unread = 0;

        // needed for socket
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

    /**
     * @param $user_id
     * @param bool $message
     * @return array
     * @throws PublicAlert
     */
    public function chat($user_id, $message = false)
    {
        global $json, $user;

        $this->user[$user_id] = [];

        getUser($user_id, 'Basic');

        if (empty($this->user[$user_id])) {
            throw new PublicAlert('Could not find user.');
        }

        $this->user[$user_id]['messages'] = [];

        $json = array_merge($json, [
            'scroll' => '#messages',
            'to_User' => $user_id,
            'scrollTo' => 'bottom'
        ]);


        if (!self::execute('UPDATE carbon_user_messages SET message_read = 1 WHERE to_user_id = UNHEX(?)', $_SESSION['id'])) {
            PublicAlert::warning('Failed to mark messages as read.');
        }

        if ($message && !$json['message_id'] = Message::Post([
                'from_user_id' => $_SESSION['id'],
                'to_user_id' => $user_id,
                'message' => $message,
            ])) {
            PublicAlert::warning('Failed to send message :( Please try again later');
        }     // else were grabbing content (json, html, etc)


        if (!self::commit(function() use ($user_id) {
            self::sendUpdate($user_id, 'NavigationMessages/');
            return true;
        })) {
            PublicAlert::warning('Failed to commit transaction :( Please try again later');
        }


//        self::fetch("SELECT HEX(message_id) as message_id, HEX(from_user_id) as from_user_id, HEX(to_user_id) as to_user_id, message, message_read, creation_date FROM StatsCoach.carbon_user_messages WHERE ((((to_user_id = UNHEX(:to_user_id)) AND (from_user_id = UNHEX(:from_user_id))) OR ((to_user_id = UNHEX(:to_user_id)) AND (from_user_id = UNHEX(:from_user_id))))) ORDER BY creation_date ASC  LIMIT 100",
//            $_SESSION['id'],$user_id, $user_id, $_SESSION['id']);

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
                'myUser' => $message['from_user_id'] === $_SESSION['id'],
                'first_name' => $user[$message['from_user_id']]['user_first_name'],
                'user_profile_picture' => $user[$message['from_user_id']]['user_profile_pic'],
                'creation_date' => $message['creation_date'], //date("F j, Y, g:i a", $message['creation_date']),
                'message' => $message['message']
            ];
        }

        return $json;
    }
}