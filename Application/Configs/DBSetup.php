<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 3/4/17
 * Time: 10:20 PM
 */


try {
    $db = \Modules\Database::getConnection();

    $drop = 'DROP TABLE users';

    $sql = "CREATE TABLE `users` (
      `user_id` INT(25) NOT NULL AUTO_INCREMENT,
      `user_username` VARCHAR(25) NOT NULL,
      `user_first_name` VARCHAR(25) NOT NULL,
      `user_last_name` VARCHAR(25) NOT NULL,
      `user_profile_pic` VARCHAR(225) NOT NULL DEFAULT 'Data/Uploads/Pictures/default_avatar.png',
      `user_cover_photo` TEXT,
      `user_birth_date` DATE DEFAULT NULL,
      `user_gender` VARCHAR(25) NOT NULL,
      `user_bio` TEXT NOT NULL,
      `user_rank` INT(8) NOT NULL DEFAULT '0',
      `user_password` VARCHAR(225) NOT NULL,
      `user_email` VARCHAR(50) NOT NULL,
      `user_email_code` VARCHAR(225) NOT NULL,
      `user_email_confirmed` VARCHAR(20) NOT NULL DEFAULT '0',
      `user_generated_string` VARCHAR(200) NOT NULL,
      `user_membership` INT(10) NOT NULL DEFAULT '0',
      `user_deactivated` INT(4) NOT NULL,
      `user_creation_date` VARCHAR(14) NOT NULL,
      `user_ip` VARCHAR(20) NOT NULL,
      PRIMARY KEY (`user_id`),
      UNIQUE KEY `username` (`user_username`),
      KEY `first_name` (`user_first_name`,`user_last_name`,`user_email`),
      KEY `username_2` (`user_username`)
    ) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 ";

    $db->exec( $sql );
    echo "Created `users` Table";

    echo '<meta http-equiv="refresh" content="5">';
    die();
} catch (PDOException $e) {
    // Delete this file
    unlink( SERVER_ROOT . "Application/Configs/DBSetup.php" );
}


