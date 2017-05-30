-- Failed to load definition
-- SHOW command denied to user 'tmiles199'@'c-73-155-97-197.hsd1.tx.comcast.net' for table 'golf_tournament_teams'
-- auto-generated definition
CREATE TABLE golf_tournament_teams
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
)