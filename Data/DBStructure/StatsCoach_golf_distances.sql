-- Failed to load definition
-- SHOW command denied to user 'tmiles199'@'c-73-155-97-197.hsd1.tx.comcast.net' for table 'golf_distances'
-- auto-generated definition
CREATE TABLE golf_distances
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
)