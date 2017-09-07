<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/4/17
 * Time: 4:59 PM
 */

return (function () {

    global $user_id, $user;

    if (!(is_object( $account = $user[$user_id] ?? false )))
        throw new \Exception( 'User not loaded' );

    $json = [
        'widget' => '#hierarchical',
        'scroll' => '#messages',
        'scrollTo' => 'bottom'
    ];

    foreach ($account->messages as $key => $message) {
        $json['Messages'][] = [
            'me' => $message->user_id == $_SESSION['id'],
            'first_name' => $user[$message->user_id]->user_first_name,
            'user_profile_picture' => $user[$message->user_id]->user_profile_picture,
            'creation_date' => date( "F j, Y, g:i a", $message->creation_date ),
            'message' => $message->message
        ];
    }

    return $json;

})();

