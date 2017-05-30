-- Failed to load definition
-- SHOW command denied to user 'tmiles199'@'c-73-155-97-197.hsd1.tx.comcast.net' for table 'golf_tournaments'
-- auto-generated definition
CREATE TABLE golf_tournaments
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
CREATE UNIQUE INDEX tournament_id ON golf_tournaments (tournament_id)