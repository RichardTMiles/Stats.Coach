<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/16/17
 * Time: 11:26 PM
 */

$route->signedIn()->match( 'Messages/{user_uri?}/',
    function ($user_uri = false) use ($view, $mustache) {
        if (!$user_uri) return $mustache( 'messages/nav-messages' );

        global $user_id;
        $user_id = $user->user_id_from_uri( $user_uri ) or die(1); // if post isset we can assume an add

        if (!empty($_POST) && !empty(($string = ( new class extends \Modules\Request{} )->post( 'message' )->noHTML()->value())))
            Tables\Messages::add( $this->user[$user_id], $user_id, $string );// else were grabbing content (json, html, etc)

        Tables\Messages::get( $this->user[$user_id], $user_id );

        return $mustache('messages/messages');
    } );

$route->signedIn()->match( 'Notifications/*' , 'notifications/notifications', ['widget' => '#NavNotifications']);

$route->signedIn()->match('tasks/*', 'tasks/tasks', ['widget' => '#NavTasks'] );


