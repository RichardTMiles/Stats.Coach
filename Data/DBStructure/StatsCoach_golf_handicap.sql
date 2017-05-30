-- Failed to load definition
-- SHOW command denied to user 'tmiles199'@'c-73-155-97-197.hsd1.tx.comcast.net' for table 'golf_handicap'
-- auto-generated definition
CREATE TABLE golf_handicap
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
)