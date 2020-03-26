<?php


$opts = array('http' =>
    array(
        'method'  => 'GET',
        /*'header'  => "Content-Type: text/xml\r\n".
            "Authorization: Basic ".base64_encode("$https_user:$https_password")."\r\n",
        'content' => $body,*/
        'timeout' => 60
    )
);

$context  = stream_context_create($opts);


// $url = 'https://greenskeeper.org/texas/Dallas_Fort_Worth/lake_park_golf_club_texas/scorecard.cfm';
$url = 'https://course.bluegolf.com/bluegolf/course/course/lakeparkgolfcourse/overview.json?format=json&src=course';
$result = file_get_contents($url, false, $context);
file_put_contents('strip.json', $result);


// $url = 'https://course.bluegolf.com/bluegolf/course/course/lakeparkgolfcourse/overview.json?format=json&src=course';
$url = 'https://course.bluegolf.com/bluegolf/course/clist/featured/clist.json';
$result = file_get_contents($url, false, $context);
file_put_contents('course_list.json', $result);


