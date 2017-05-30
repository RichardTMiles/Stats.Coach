-- Failed to load definition
-- SHOW command denied to user 'tmiles199'@'c-73-155-97-197.hsd1.tx.comcast.net' for table 'teams'
-- auto-generated definition
CREATE TABLE teams
(
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
CREATE UNIQUE INDEX team_id ON teams (team_id, team_code)