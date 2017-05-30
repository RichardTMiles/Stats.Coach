CREATE TABLE users
(
  user_id INT(25) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  user_username VARCHAR(25) NOT NULL,
  user_first_name VARCHAR(25) NOT NULL,
  user_last_name VARCHAR(25) NOT NULL,
  user_profile_pic VARCHAR(225) DEFAULT 'Data/Uploads/Pictures/default_avatar.png' NOT NULL,
  user_cover_photo TEXT,
  user_birth_date DATE,
  user_gender VARCHAR(25) NOT NULL,
  user_bio TEXT NOT NULL,
  user_rank INT(8) DEFAULT '0' NOT NULL,
  user_password VARCHAR(225) NOT NULL,
  user_email VARCHAR(50) NOT NULL,
  user_email_code VARCHAR(225) NOT NULL,
  user_email_confirmed VARCHAR(20) DEFAULT '0' NOT NULL,
  user_generated_string VARCHAR(200) NOT NULL,
  user_membership INT(10) DEFAULT '0' NOT NULL,
  user_deactivated INT(4) NOT NULL,
  user_creation_date VARCHAR(14) NOT NULL,
  user_ip VARCHAR(20) NOT NULL
);
CREATE INDEX first_name ON users (user_first_name, user_last_name, user_email);
CREATE UNIQUE INDEX username ON users (user_username);
CREATE INDEX username_2 ON users (user_username)