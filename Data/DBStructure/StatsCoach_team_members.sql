-- Failed to load definition
-- SHOW command denied to user 'tmiles199'@'c-73-155-97-197.hsd1.tx.comcast.net' for table 'team_members'
-- auto-generated definition
CREATE TABLE team_members
(
  user_id INT(11) NOT NULL,
  team_id INT(11) NOT NULL,
  CONSTRAINT `PRIMARY` PRIMARY KEY (user_id, team_id)
)