<?php
// I guess at this point we should assume we've collected all the data
// The user id and the user array
return (function () {
    global $user;
    $unread = 0;

    foreach ($user as $id => $their) {
        if ($id == $_SESSION['id'])
            continue;

        if ($their->messages ?? false) {
            $message = $their->messages[count( $their->messages ) - 1];
            $json['users'][] = [
                'first_name' => $their->user_first_name,
                'last_name' => $their->user_last_name,
                'user_uri' => SITE . 'Profile' . DS . $their->user_profile_uri . DS,
                'user_profile_picture' => $their->user_profile_picture,
                'creation_date' => date( "F j, g:i a", $message->creation_date ),
                'message' => $message->message
            ];
            if (!$message->message_read) $unread++;
        }
    }

    if ($unread) $json['newMessages'] = $unread;

    return $json ?? [];

})();
