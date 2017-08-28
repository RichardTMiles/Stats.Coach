<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/16/17
 * Time: 11:26 PM
 */

function mustache($path) {
    $json = [
        'MY_ID' => $_SESSION['id'],
        'Mustache' => SITE . $path.'.mst' ];
    $json[] = (include SERVER_ROOT . $path.'.php');
    print json_encode($json);

    exit(1);
}

// AJAX REQUEST
$route->signedIn()->match( 'Messages/{user_uri?}/',
    function ($user_uri = false) use ($user, $view) {

        if (!$user_uri) {
            $path = 'Public/StatsCoach/Mustache/messages/nav-messages';
            echo json_encode([
                'MY_ID' => $_SESSION['id'],
                'Mustache' => SITE . $path.'.mst' ]);
            exit(1);
            //mustache('Public/StatsCoach/Mustache/messages/nav-messages');
        }

        global $user_id;
        $user_id = $user->user_id_from_uri( $user_uri ) or die(1); // if post isset we can assume an add

        if (!empty($_POST) && !empty(($string = ( new class extends \Modules\Request{} )->post( 'message' )->noHTML()->value())))
            \Model\Helpers\Tables\Messages::add( $this->user[$user_id], $user_id, $string );// else were grabbing content (json, html, etc)

        \Model\Helpers\Tables\Messages::get( $this->user[$user_id], $user_id );

        mustache('Public/StatsCoach/Mustache/messages/messages' );
    } );


$route->signedIn()->match( 'Notifications/' ,
    function ($path = "Public/StatsCoach/Mustache/notifications/notifications") {
        echo json_encode([
        'MY_ID' => $_SESSION['id'],
        'Mustache' => SITE . $path.'.mst' ]);
        exit(1);
    });


$route->signedIn()->match('Tasks/',
    function ($path = "Public/StatsCoach/Mustache/tasks/tasks") {
        echo json_encode([
            'MY_ID' => $_SESSION['id'],
            'Mustache' => SITE . $path.'.mst' ]);
        exit(1);
    });




