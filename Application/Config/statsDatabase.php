<?php

/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 10/12/17
 * Time: 5:38 PM
 */

$db = \Carbon\Database::database();

try {

    print '<h1>Creating Stats.Coach</h1>';


    try {
        $stmt = $db->prepare("SELECT 1 FROM golf_course LIMIT 1;");
        $stmt->execute();
        print "<br>Table `golf_course` already exists";
    } catch (PDOException $e) {
        $sql = <<<END
    CREATE TABLE golf_course
(
	course_id VARCHAR(225) NOT NULL
		PRIMARY KEY,
	course_name VARCHAR(225) NOT NULL,
	course_holes INT(2) DEFAULT '18' NOT NULL,
	course_phone TEXT NOT NULL,
	course_difficulty INT(10) NULL,
	course_rank INT(5) NULL,
	box_color_1 VARCHAR(10) NULL,
	box_color_2 VARCHAR(10) NULL,
	box_color_3 VARCHAR(10) NULL,
	box_color_4 VARCHAR(10) NULL,
	box_color_5 VARCHAR(10) NULL,
	course_par BLOB NOT NULL,
	course_par_out INT(2) NOT NULL,
	course_par_in INT(2) NOT NULL,
	par_tot INT(2) NOT NULL,
	course_par_hcp INT(4) NULL,
	course_type CHAR(30) NULL,
	course_access VARCHAR(120) NULL,
	course_handicap BLOB NULL,
	pga_professional TEXT NULL,
	website TEXT NULL,
	CONSTRAINT golf_course_course_id_uindex
		UNIQUE (course_id),
	CONSTRAINT golf_courses_course_id_uindex
		UNIQUE (course_id),
	CONSTRAINT golf_course_entity_entity_pk_fk
		FOREIGN KEY (course_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;

END;

        $db->exec($sql);
        print '<br>Build Course';
    }


    try {
        $stmt = $db->prepare("SELECT 1 FROM golf_rounds LIMIT 1;");
        $stmt->execute();
        print "<br>Table `golf_rounds` already exists";
    } catch (PDOException $e) {
        $sql = <<<END
CREATE TABLE golf_rounds
(
	user_id VARCHAR(225) NOT NULL,
	round_id VARCHAR(225) NOT NULL,
	course_id VARCHAR(225) NOT NULL COMMENT 'golf_courses(course_id)',
	round_public INT(1) DEFAULT '1' NOT NULL COMMENT 'true "1" or false "2"',
	score TEXT NOT NULL,
	score_gnr TEXT NOT NULL,
	score_ffs TEXT NOT NULL,
	score_putts TEXT NOT NULL,
	score_out INT(2) NOT NULL,
	score_in INT(3) NOT NULL,
	score_total INT(3) NOT NULL,
	score_total_gnr INT DEFAULT '0' NULL,
	score_total_ffs INT(3) DEFAULT '0' NULL,
	score_total_putts INT NULL,
	score_date TEXT NULL,
	CONSTRAINT golf_rounds_entity_entity_pk_fk
		UNIQUE (round_id),
	CONSTRAINT golf_rounds_entity_user_pk_fk
		FOREIGN KEY (user_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT golf_rounds_entity_entity_pk_fk
		FOREIGN KEY (round_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT golf_rounds_entity_course_pk_fk
		FOREIGN KEY (course_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX golf_rounds_entity_course_pk_fk
	ON golf_rounds (course_id)
;

CREATE INDEX golf_rounds_entity_user_pk_fk
	ON golf_rounds (user_id)
;


END;

        $db->exec($sql);
        print '<br>Build Golf Rounds ';
    }


    try {
        $stmt = $db->prepare("SELECT 1 FROM golf_stats LIMIT 1;");
        $stmt->execute();
        print "<br>Table `golf_stats` already exists";
    } catch (PDOException $e) {
        $sql = <<<END
CREATE TABLE golf_stats
(
	stats_id VARCHAR(225) NOT NULL
		PRIMARY KEY,
	stats_tournaments INT DEFAULT '0' NULL,
	stats_rounds INT DEFAULT '0' NULL,
	stats_handicap INT DEFAULT '0' NULL,
	stats_strokes INT DEFAULT '0' NULL,
	stats_ffs INT DEFAULT '0' NULL,
	stats_gnr INT DEFAULT '0' NULL,
	stats_putts INT DEFAULT '0' NULL,
	CONSTRAINT golf_stats_entity_entity_pk_fk
		FOREIGN KEY (stats_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;



END;

        $db->exec($sql);
        print '<br>Built golf stats';
    }


    try {
        $stmt = $db->prepare("SELECT 1 FROM golf_tee_box LIMIT 1;");
        $stmt->execute();
        print "<br>Table `golf_tee_box` already exists";
    } catch (PDOException $e) {
        $sql = <<<END
CREATE TABLE golf_tee_box
(
	course_id VARCHAR(225) NOT NULL COMMENT 'Reference from golf_courses',
	tee_box INT(1) NOT NULL COMMENT 'options ( 1 - 5 )',
	distance BLOB NOT NULL,
	distance_color VARCHAR(10) NOT NULL,
	distance_general_slope INT(4) NULL,
	distance_general_difficulty FLOAT NULL,
	distance_womens_slope INT(4) NULL,
	distance_womens_difficulty FLOAT NULL,
	distance_out INT(7) NULL,
	distance_in INT(7) NULL,
	distance_tot INT(10) NULL,
	CONSTRAINT golf_distance_entity_entity_pk_fk
		FOREIGN KEY (course_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX golf_distance_entity_entity_pk_fk
	ON golf_tee_box (course_id)
;



END;

        $db->exec($sql);
        print "<br>Built Tournament teams";
    }


    try {
        $stmt = $db->prepare("SELECT 1 FROM golf_tournament_teams LIMIT 1;");
        $stmt->execute();
        print "<br>Table `golf_tournament_teams` already exists";
    } catch (PDOException $e) {
        $sql = <<<END
CREATE TABLE golf_tournament_teams
(
	team_id VARCHAR(225) NOT NULL COMMENT 'teams(team_id)',
	tournament_id VARCHAR(255) NOT NULL COMMENT 'tournaments(tournament_id)',
	tournament_paid INT(1) DEFAULT '0' NULL,
	tournament_accepted INT(1) DEFAULT '0' NULL,
	CONSTRAINT golf_tournament_teams_entity_team_pk_fk
		FOREIGN KEY (team_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT golf_tournament_teams_entity_tournament_pk_fk
		FOREIGN KEY (tournament_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX golf_tournament_teams_entity_team_pk_fk
	ON golf_tournament_teams (team_id)
;

CREATE INDEX golf_tournament_teams_entity_tournament_pk_fk
	ON golf_tournament_teams (tournament_id)
;



END;

        $db->exec($sql);
    }

    try {
        $stmt = $db->prepare("SELECT 1 FROM golf_tournaments LIMIT 1;");
        $stmt->execute();
        print "<br>Table `golf_tournaments` already exists";
    } catch (PDOException $e) {
        print '<br>Build Golf Tournaments';
        $sql = <<<END
CREATE TABLE golf_tournaments
(
	tournament_id VARCHAR(225) NOT NULL,
	tournament_name VARCHAR(225) NOT NULL,
	host_name VARCHAR(225) NOT NULL COMMENT 'This could be a school or org
	',
	tournament_style INT NOT NULL,
	tournament_team_price INT NULL,
	tournament_paid INT(1) DEFAULT '1' NULL COMMENT 'True False',
	course_id VARCHAR(225) NULL COMMENT 'course_id, incase double',
	tournament_date DATE NULL,
	CONSTRAINT golf_tournaments_entity_entity_pk_fk
		FOREIGN KEY (tournament_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT golf_tournaments_entity_course_pk_fk
		FOREIGN KEY (course_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX golf_tournaments_entity_course_pk_fk
	ON golf_tournaments (course_id)
;

CREATE INDEX golf_tournaments_entity_entity_pk_fk
	ON golf_tournaments (tournament_id)
;


END;

        $db->exec($sql);

    }
    try {
        $stmt = $db->prepare("SELECT 1 FROM team_members LIMIT 1;");
        $stmt->execute();
        print "<br>Table `carbon` already exists";
    } catch (PDOException $e) {
        print '<br>Build Team Member';
        $sql = <<<END
CREATE TABLE team_members
(
	member_id VARCHAR(225) NULL,
	team_id VARCHAR(225) NOT NULL,
	user_id VARCHAR(225) NOT NULL,
	accepted TINYINT(1) DEFAULT '0' NULL,
	CONSTRAINT team_members_entity_entity_pk_fk
		FOREIGN KEY (member_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT team_member_entity_team_pk_fk
		FOREIGN KEY (team_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT team_member_entity_entity_pk_fk
		FOREIGN KEY (user_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX team_members_entity_entity_pk_fk
	ON team_members (member_id)
;

CREATE INDEX team_member_entity_entity_pk_fk
	ON team_members (user_id)
;

CREATE INDEX team_member_entity_team_pk_fk
	ON team_members (team_id)
;


END;

        $db->exec($sql);

        print '<br>Build teams';
    }


    try {
        $stmt = $db->prepare("SELECT 1 FROM teams LIMIT 1;");
        $stmt->execute();
        print "<br>Table `teams` already exists";
    } catch (PDOException $e) {
        $sql = <<<END
CREATE TABLE teams
(
	team_id VARCHAR(225) NOT NULL
		PRIMARY KEY,
	team_coach VARCHAR(225) NOT NULL COMMENT 'user_id',
	parent_team VARCHAR(225) NULL,
	team_code VARCHAR(225) NOT NULL,
	team_name VARCHAR(225) NOT NULL,
	team_rank INT DEFAULT '0' NULL,
	team_sport VARCHAR(225) DEFAULT 'Golf' NOT NULL,
	team_division VARCHAR(225) NULL,
	team_school VARCHAR(225) NULL,
	team_district VARCHAR(225) NULL,
	team_membership VARCHAR(225) NULL,
	team_photo VARCHAR(225) NULL,
	CONSTRAINT teams_team_id_uindex
		UNIQUE (team_id),
	CONSTRAINT teams_entity_entity_pk_fk
		FOREIGN KEY (team_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT teams_entity_coach_pk_fk
		FOREIGN KEY (team_coach) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT teams_teams_team_id_fk
		FOREIGN KEY (parent_team) REFERENCES teams (team_id)
			ON UPDATE CASCADE ON DELETE SET NULL,
	CONSTRAINT teams_entity_photos_photo_id_fk
		FOREIGN KEY (team_photo) REFERENCES carbon_photos (photo_id)
			ON UPDATE CASCADE ON DELETE SET NULL
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX teams_entity_coach_pk_fk
	ON teams (team_coach)
;

CREATE INDEX teams_entity_photos_photo_id_fk
	ON teams (team_photo)
;

CREATE INDEX teams_teams_team_id_fk
	ON teams (parent_team)
;


END;
        $db->exec($sql);

    }


    print '<h4>Creating Tags</h4>';


    Try {
        $sql = <<<END
REPLACE INTO carbon_tags (tag_id, tag_description, tag_name) VALUES (?,?,?);
END;

        $tag = [
            [TEAMS, '', 'TEAMS'],
            [TEAM_MEMBERS, '', 'TEAM_MEMBERS'],
            [GOLF_TOURNAMENTS, '', 'GOLF_TOURNAMENTS'],
            [GOLF_ROUNDS, '', 'GOLF_ROUNDS'],
            [GOLF_COURSE, '', 'GOLF_COURSE'],
        ];

        foreach ($tag as $key => $value)
            $db->prepare($sql)->execute($value);

        print '<br>Tags inserted';

    } catch (PDOException $e) {
        print '<br>' . $e->getMessage();
    }

    print '<br><h4>Done!</h4>';

} catch (PDOException $e) {
    print "Oh no!! Goto CarbonPHP.com for support! ( screenshot this page )<br>";
    print $e->getMessage() . PHP_EOL;
}