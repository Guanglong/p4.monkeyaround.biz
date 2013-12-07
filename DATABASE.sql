
create database monkeyar_p4_monkeyaround_biz;


CREATE TABLE `monkeyar_p4_monkeyaround_biz`.`users` ( `user_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`created` int( 11 ) NOT NULL ,
`modified` int( 11 ) NOT NULL ,
`token` varchar( 255 ) NOT NULL ,
`password` varchar( 255 ) NOT NULL ,
`last_login` int( 11 ) NOT NULL ,
`timezone` int( 11 ) NOT NULL ,
`first_name` varchar( 255 ) CHARACTER SET utf8 NOT NULL ,
`last_name` varchar( 255 ) CHARACTER SET utf8 NOT NULL ,
`email` varchar( 255 ) CHARACTER SET utf8 NOT NULL ,
`login_count` int( 11 ) NOT NULL ,
`deleted_ind` varchar( 1 ) NOT NULL DEFAULT 'N',
`temp_password` varchar( 255 ) DEFAULT NULL ,
`signup_ip_address` varchar( 50 ) NOT NULL COMMENT 'ip address when user signup',
`signup_machine_name` varchar( 250 ) DEFAULT NULL ,
`last_login_ip_address` varchar( 50 ) DEFAULT NULL ,
`last_login_machine_name` varchar( 250 ) DEFAULT NULL ,
PRIMARY KEY ( `user_id` ) ) ENGINE = InnoDB DEFAULT CHARSET = latin1;



CREATE TABLE `monkeyar_p4_monkeyaround_biz`.`goals` ( `goal_id` int( 11 ) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
`user_id` int( 11 ) NOT NULL COMMENT 'foreign key to users.user_id',
`start_date` date NOT NULL COMMENT 'when to start the goal',
`goal_days` int( 11 ) NOT NULL COMMENT 'how many days for the goal',
`start_value` int( 11 ) NOT NULL COMMENT 'starting point',
`target_value` int( 11 ) NOT NULL COMMENT 'the goal ',
`end_value` int( 11 ) NOT NULL COMMENT 'the actual point at the end of the goal days',
`active_flag` varchar( 1 ) NOT NULL DEFAULT 'Y',
`created` int( 11 ) NOT NULL COMMENT 'the time when the row was created',
`modified` int( 11 ) NOT NULL COMMENT 'the time when the row was modified',
PRIMARY KEY ( `goal_id` ) ,
KEY `user_id` ( `user_id` ) ) ENGINE = InnoDB DEFAULT CHARSET = latin1;

CREATE TABLE `monkeyar_p4_monkeyaround_biz`.`progress` (
`progress_id` int( 11 ) NOT NULL AUTO_INCREMENT COMMENT 'priary key for progress',
`goal_id` int( 11 ) NOT NULL COMMENT 'fk to goal',
`progress_value` int( 11 ) NOT NULL COMMENT 'what value reached',
`progress_day` int( 11 ) NOT NULL COMMENT 'when',
`created` int( 11 ) NOT NULL COMMENT 'created timie',
`modified` int( 11 ) NOT NULL COMMENT 'modified time',
PRIMARY KEY ( `progress_id` ) ,
KEY `goal_id` ( `goal_id` )
) ENGINE = InnoDB DEFAULT CHARSET = latin1; 