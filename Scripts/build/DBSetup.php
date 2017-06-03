<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 3/4/17
 * Time: 10:20 PM
 */


try {
    $db = \Modules\Database::getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS `users` (
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
    echo "Created `users` Table \n";

    $sql = "CREATE TABLE IF NOT EXISTS teams (
  team_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  team_code VARCHAR(225) NOT NULL,
  team_name VARCHAR(225) NOT NULL,
  team_rank INT(11),
  team_coach INT(11) NOT NULL COMMENT 'user_id',
  team_sport VARCHAR(225) NOT NULL,
  team_division VARCHAR(225) NOT NULL,
  team_school VARCHAR(225) NOT NULL,
  team_district VARCHAR(225) NOT NULL,
  team_membership VARCHAR(225) NOT NULL
);
CREATE UNIQUE INDEX team_id ON teams (team_id, team_code)";

    $db->exec( $sql );
    echo "Created `teams` Table \n";



    $sql = "CREATE TABLE IF NOT EXISTS team_members (
    user_id INT(11) NOT NULL,
    team_id INT(11) NOT NULL,
    CONSTRAINT `PRIMARY` PRIMARY KEY (user_id, team_id))";


    $db->exec( $sql );
    echo "Created `Team_members` Table \n";

    $sql ="CREATE TABLE IF NOT EXISTS golf_tournaments
(
  tournament_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  tournament_name INT(11) NOT NULL,
  host_name INT(11) NOT NULL,
  tournament_style INT(11),
  tournament_team_price INT(11),
  tournament_paid INT(1) DEFAULT '1' NOT NULL COMMENT 'True False',
  host_id INT(11) NOT NULL COMMENT 'users(user_id)',
  course_id_day_1 INT(11) NOT NULL COMMENT 'course_id, incase double',
  course_id_day_2 INT(11) NOT NULL,
  tournament_date DATE NOT NULL
);
CREATE UNIQUE INDEX tournament_id ON golf_tournaments (tournament_id)";


    $db->exec( $sql );
    echo "Created `Golf_tournaments` Table \n";


    $sql = "CREATE TABLE IF NOT EXISTS golf_tournament_teams
(
  tournament_id INT(11) NOT NULL COMMENT 'tournaments(tournament_id)',
  tournament_team_id INT(11) NOT NULL,
  tournament_team_name VARCHAR(225) NOT NULL,
  tournament_accepted INT(11) NOT NULL,
  tournament_paid INT(1) DEFAULT '1' NOT NULL,
  coach_id INT(11) NOT NULL,
  team_id INT(11) NOT NULL COMMENT 'teams(team_id)',
  athlete_id_1 INT(11) NOT NULL COMMENT 'user_id',
  athlete_id_2 INT(11) NOT NULL COMMENT 'user_id',
  athlete_id_3 INT(11) NOT NULL COMMENT 'user_id',
  athlete_id_4 INT(11) NOT NULL COMMENT 'user_id',
  athlete_id_5 INT(11) NOT NULL COMMENT 'user_id'
)";

    $db->exec( $sql );
    echo "Created `golf_tournament_teams` Table \n";

    $sql = "CREATE TABLE IF NOT EXISTS golf_rounds
(
  round_id INT(11) NOT NULL,
  round_public INT(1) DEFAULT '1' NOT NULL COMMENT 'true \"1\" or false \"2\"',
  user_id INT(11) NOT NULL COMMENT 'reference from users',
  course_id INT(11) NOT NULL COMMENT 'golf_courses(course_id)',
  score_1 INT(2) NOT NULL COMMENT 'strokes / score',
  score_2 INT(2) NOT NULL,
  score_3 INT(2) NOT NULL,
  score_4 INT(2) NOT NULL,
  score_5 INT(2) NOT NULL,
  score_6 INT(2) NOT NULL,
  score_7 INT(2) NOT NULL,
  score_8 INT(2) NOT NULL,
  score_9 INT(2) NOT NULL,
  score_out INT(2) NOT NULL,
  score_10 INT(2) NOT NULL,
  score_11 INT(2) NOT NULL,
  score_12 INT(2) NOT NULL,
  score_13 INT(2) NOT NULL,
  score_14 INT(2) NOT NULL,
  score_15 INT(2) NOT NULL,
  score_16 INT(2) NOT NULL,
  score_17 INT(2) NOT NULL,
  score_18 INT(2) NOT NULL,
  score_in INT(3) NOT NULL,
  score_tot INT(3) NOT NULL
);
CREATE UNIQUE INDEX round_id ON golf_rounds (round_id)";

    $db->exec( $sql );
    echo "Created `golf_rounds` Table \n";

    $sql = "CREATE TABLE IF NOT EXISTS golf_handicap
(
  course_id INT(11) NOT NULL COMMENT 'References golf_courses(course_id)',
  handicap_gender VARCHAR(5),
  handicap_1 INT(2) NOT NULL,
  handicap_2 INT(2) NOT NULL,
  handicap_3 INT(2) NOT NULL,
  handicap_4 INT(2) NOT NULL,
  handicap_5 INT(2) NOT NULL,
  handicap_6 INT(2) NOT NULL,
  handicap_7 INT(2) NOT NULL,
  handicap_8 INT(2) NOT NULL,
  handicap_9 INT(2) NOT NULL,
  handicap_10 INT(2) NOT NULL,
  handicap_11 INT(2) NOT NULL,
  handicap_12 INT(2) NOT NULL,
  handicap_13 INT(2) NOT NULL,
  handicap_14 INT(2) NOT NULL,
  handicap_15 INT(2) NOT NULL,
  handicap_16 INT(2) NOT NULL,
  handicap_17 INT(2) NOT NULL,
  handicap_18 INT(2) NOT NULL
)";


    $db->exec( $sql );
    echo "Created `golf_handicap` Table \n";



    $sql = "CREATE TABLE IF NOT EXISTS golf_distances
(
  course_id INT(11) NOT NULL COMMENT 'Reference from golf_courses',
  tee_box INT(1) NOT NULL COMMENT 'options ( 1 - 5 )',
  distance_1 INT(7),
  distance_2 INT(7),
  distance_3 INT(7),
  distance_4 INT(7),
  distance_5 INT(7),
  distance_6 INT(7),
  distance_7 INT(7),
  distance_8 INT(7),
  distance_9 INT(7),
  distance_out INT(7),
  distance_10 INT(7),
  distance_11 INT(7),
  distance_12 INT(7),
  distance_13 INT(7),
  distance_14 INT(7),
  distance_15 INT(7),
  distance_16 INT(7),
  distance_17 INT(7),
  distance_18 INT(7),
  distance_in INT(7),
  distance_tot INT(10),
  distance_color INT(11)
)";


    $db->exec( $sql );
    echo "Created `golf_distances` Table \n";


    $sql = "CREATE TABLE IF NOT EXISTS golf_courses
(
  course_id INT(11) NOT NULL,
  course_name VARCHAR(225) NOT NULL,
  course_holes INT(2) DEFAULT '18' NOT NULL,
  course_street TEXT NOT NULL,
  course_city VARCHAR(40),
  course_state VARCHAR(10) NOT NULL,
  course_phone TEXT NOT NULL,
  course_elevation INT(10) NOT NULL,
  course_difficulty INT(10) NOT NULL,
  course_rank INT(5) NOT NULL,
  box_color_1 VARCHAR(10),
  box_color_2 VARCHAR(10),
  box_color_3 VARCHAR(10),
  box_color_4 VARCHAR(10),
  box_color_5 VARCHAR(10),
  par_1 INT(1) NOT NULL,
  par_2 INT(1) NOT NULL,
  par_3 INT(1) NOT NULL,
  par_4 INT(1) NOT NULL,
  par_5 INT(1) NOT NULL,
  par_6 INT(1) NOT NULL,
  par_7 INT(1) NOT NULL,
  par_8 INT(1) NOT NULL,
  par_9 INT(1) NOT NULL,
  par_out INT(2) NOT NULL,
  par_10 INT(1) NOT NULL,
  par_11 INT(1) NOT NULL,
  par_12 INT(1) NOT NULL,
  par_13 INT(1) NOT NULL,
  par_14 INT(1) NOT NULL,
  par_15 INT(1) NOT NULL,
  par_16 INT(1) NOT NULL,
  par_17 INT(1) NOT NULL,
  par_18 INT(1) NOT NULL,
  par_in INT(2) NOT NULL,
  par_tot INT(2) NOT NULL,
  par_hcp INT(4) NOT NULL,
  course_type CHAR(30),
  course_access VARCHAR(120)
)";


    $db->exec( $sql );
    echo "Created `golf_courses` Table \n";

    echo '<meta http-equiv="refresh" content="5">';

} catch (PDOException $e) {
    // Delete this file
    echo "Build Failed";
}

unlink( SERVER_ROOT . "Scripts/Build/DBSetup.php" );


exit(1);