-- Failed to load definition
-- SHOW command denied to user 'tmiles199'@'c-73-155-97-197.hsd1.tx.comcast.net' for table 'golf_rounds'
-- auto-generated definition
CREATE TABLE golf_rounds
(
  round_id INT(11) NOT NULL,
  round_public INT(1) DEFAULT '1' NOT NULL COMMENT 'true "1" or false "2"',
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
CREATE UNIQUE INDEX round_id ON golf_rounds (round_id)