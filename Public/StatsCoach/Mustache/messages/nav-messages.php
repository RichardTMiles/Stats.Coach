<?php
// I guess at this point we should assume we've collected all the data
// The user id and the user array
return (function () {
    global $user;

    $json['widget'] = '#NavMessages';
    $json['newMessages'] = $user[$_SESSION['id']]->newMessages ?? null;

    foreach ($user as $id => $their) {
        if ($id == $_SESSION['id'])
            continue;

        if ($their->messages ?? false) {
            $message = $their->messages[count( $their->messages ) - 1];
            $json['users'][] = [
                'first_name' => $their->user_first_name,
                'last_name' => $their->user_last_name,
                'user_uri' => SITE . 'Profile' . DS . $id . DS,
                'user_profile_picture' => $their->user_profile_picture,
                'creation_date' => date( "F j, g:i a", $message->creation_date ),
                'message' => $message->message
            ];
        }
    }

    return $json;

})();
