<?php
define("NAME_LONG","NAME_LONG");
define("QUERY","QUERY");
define("DESCRIPTION","DESCRIPTION");
define("REMARKS","REMARKS");

function db_func_get_table_count()
{
  global $table_names;
  return count($table_names);
}

function db_func_get_table_name($ndx)
{
  global $table_names;
  
  $tablename = "INVALID_INDEX";
  if(is_numeric($ndx))
    $tablename = $table_names[$ndx];

  return $tablename;
}
function db_func_get_table_long_name($ndx)
{
  global $table_names, $table_data;
  
  $tablename = "INVALID_INDEX";
  if(is_numeric($ndx))
    $tablename = $table_names[$ndx];
  
  return $table_data[$tablename][NAME_LONG];
}
function db_func_get_table_description($ndx)
{
  global $table_names, $table_data;
  
  $tablename = $ndx;
  if(is_numeric($ndx))
    $tablename = $table_names[$ndx];

  return $table_data[$tablename][DESCRIPTION];
}
function db_func_get_table_query($ndx)
{
  global $table_names, $table_data;
  
  $tablename = $ndx;
  if(is_numeric($ndx))
    $tablename = $table_names[$ndx];

  return $table_data[$tablename][QUERY];
}
function db_func_get_table_remarks($ndx)
{
  global $table_names, $table_data;
  
  $tablename = $ndx;
  if(is_numeric($ndx))
    $tablename = $table_names[$ndx];

  return $table_data[$tablename][REMARKS];
}
function db_func_set_table_remarks($ndx, $remarks)
{
  global $table_names, $table_data;
  
  $tablename = $ndx;
  if(is_numeric($ndx))
    $tablename = $table_names[$ndx];

  $table_data[$tablename][REMARKS] = $remarks;
}

$sql_bus_riders = <<<SQL
CREATE TABLE `bus_riders` (
  `rider_id` int(11) NOT NULL auto_increment,
  `phone_number` varchar(15) NOT NULL,
  `name_last` varchar(30) NOT NULL,
  `name_first` varchar(30) default NULL,
  `address1` varchar(50) default NULL,
  `address2` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `zip_code` varchar(15) default NULL,
  `special_handling` varchar(100) default NULL,
  `update_pwd` varchar(30) default NULL,
  `update_pwd_exp` varchar(15) default NULL,
  `last_update` varchar(15) default NULL,
  PRIMARY KEY  (`rider_id`),
  UNIQUE KEY `phone_number_ndx` (`phone_number`),
  KEY `name_ndx` (`name_last`,`name_first`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 
SQL;

$sql_bus_riders_reg = <<<SQL
CREATE TABLE `bus_riders_reg` (
  `rider_id` int(11) NOT NULL auto_increment,
  `phone_number` varchar(15) NOT NULL,
  `name_last` varchar(30) NOT NULL,
  `name_first` varchar(30) default NULL,
  `address1` varchar(50) default NULL,
  `address2` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `zip_code` varchar(15) default NULL,
  `special_handling` varchar(100) default NULL,
  `update_pwd` varchar(30) default NULL,
  `update_pwd_exp` varchar(15) default NULL,
  `last_update` varchar(15) default NULL,
  PRIMARY KEY  (`rider_id`),
  UNIQUE KEY `phone_number_ndx` (`phone_number`),
  KEY `name_ndx` (`name_last`,`name_first`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 
SQL;

$sql_bus_riders_reg_insert = <<<SQL
INSERT INTO `bus_riders_reg` (`rider_id`, `phone_number`, `name_last`, `name_first`, `address1`, `address2`, `city`, `zip_code`, `special_handling`, `update_pwd`, `update_pwd_exp`, `last_update`) VALUES
(1, '7026124216', 'Bolden', 'Nate', '1234 Five St', NULL, 'Las Vegas', '89130', 'Key under mat', NULL, NULL, '20160325132115'),
(2, '7022793038', 'Bolden', 'Zee', '12682 Memorial Way', 'Apt 1022', 'Moreno Valley', '92553', 'Gate Code 4321', NULL, NULL, '20160326144411'),
(3, '7027556684', 'Pope', 'Maddy', '3665 E. 110 St', NULL, 'Cleveland', '44105', NULL, NULL, NULL, '20160327175522'),
(4, '7027553701', 'Pope', 'Ethan', '23113 Shurmer Dr', NULL, 'Warrensville Hts', '44128', NULL, NULL, NULL, '20160327175755'),
(5, '4404760389', 'Bolden', 'Norris', '4480 Granada blvd', 'Apt 1', 'Warrensville Hts', '44128', NULL, NULL, NULL, '20130327181911'),
(6, '7027559737', 'Pope', 'Elijah', '4775 Apartment Dr', '#G-10', 'Charleston', '29418', NULL, NULL, NULL, '20160327181311'),
(7, '3108017973', 'Nalbandian', 'Jenifer', '12991 Moreno Beach Drive', 'Apt 15207', 'Moreno Valley', '92553', NULL, NULL, NULL, '20160407051822'),
(8, '8188134626', 'Nalbandian', 'Michael', '12991 Moreno Beach Drive', 'Apt 15207', 'Moreno Valley', '92553', NULL, NULL, NULL, '20160327181744'),
(9, '9098004619', 'Espinosa', 'Michael', '2345 S.Waterman Ave', 'Room 102', 'San Bernadino', '92408', 'Back near office', NULL, NULL, '20160407051422')
SQL;

$sql_bus_rides = <<<SQL
CREATE TABLE `bus_rides` (
  `ride_id` int(11) NOT NULL auto_increment,
  `rider_id` int(11) NOT NULL,
  `svs_id` int(11) NOT NULL,
  `pax_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ride_id`),
  UNIQUE KEY `ride_ndx` (`rider_id`,`svs_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
SQL;

$sql_default_services = <<<SQL
CREATE TABLE `default_services` (
  `default_svs_id` int(11) NOT NULL auto_increment,
  `day_of_week` int(11) NOT NULL,
  `time_of_day` varchar(4) NOT NULL,
  PRIMARY KEY  (`default_svs_id`),
  UNIQUE KEY `day_of_week_ndx` (`day_of_week`,`time_of_day`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 
SQL;

$sql_default_services_insert = <<<SQL
INSERT INTO `default_services` (`default_svs_id`, `day_of_week`, `time_of_day`) VALUES
(3, 0, '0900'),
(4, 0, '1115'),
(5, 0, '1800'),
(1, 3, '1900'),
(6, 4, '0930'),
(2, 6, '1000')
SQL;

$sql_services = <<<SQL
CREATE TABLE `services` (
  `svs_id` int(11) NOT NULL auto_increment,
  `svs_datetime` varchar(15) NOT NULL,
  `svs_expiration` varchar(15) NOT NULL,
  `head_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`svs_id`),
  KEY `svs_datetime_ndx` (`svs_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 
SQL;

$sql_sessions = <<<SQL
CREATE TABLE `sessions` (
  `ses_id` int(11) NOT NULL auto_increment,
  `rider_id` int(11) NOT NULL,
  `ses_datetime` varchar(15) NOT NULL,
  `ses_expiration` varchar(15) NOT NULL,
  `ses_status` varchar(25) NOT NULL,
  PRIMARY KEY  (`ses_id`),
  KEY `rider_id_ndx` (`rider_id`),
  KEY `ses_datetime_ndx` (`ses_datetime`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;
SQL;

$sql_session_choices = <<<SQL
CREATE TABLE `session_choices` (
  `ses_choice_id` int(11) NOT NULL auto_increment,
  `ses_id` int(11) NOT NULL,
  `svs_id` int(11) NOT NULL,
  `identifier` varchar(1) NOT NULL,
  `pax_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ses_choice_id`),
  UNIQUE KEY `unique_ndx` (`ses_id`,`svs_id`,`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
SQL;

$sql_session_log = <<<SQL
CREATE TABLE `session_log` (
  `ses_log_id` int(11) NOT NULL auto_increment,
  `ses_id` int(11) NOT NULL,
  `ses_datetime` varchar(15) NOT NULL,
  `ses_sender` varchar(1) NOT NULL,
  `ses_text` varchar(500) NOT NULL,
  PRIMARY KEY  (`ses_log_id`),
  KEY `ses_id_ndx` (`ses_id`),
  KEY `ses_datetime_ndx` (`ses_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 
SQL;

$sql_unregistered_log = <<<SQL
CREATE TABLE `unregistered_log` (
  `unr_log_id` int(11) NOT NULL auto_increment,
  `unr_phone_number` varchar(15) NOT NULL,
  `unr_datetime` varchar(15) NOT NULL,
  `unr_expiration` varchar(15) NOT NULL,
  `unr_sender` varchar(1) NOT NULL,
  `unr_text` varchar(500) NOT NULL,
  PRIMARY KEY  (`unr_log_id`),
  KEY `unr_datetime_ndx` (`unr_datetime`),
  KEY `unr_phone_number_ndx` (`unr_phone_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 
SQL;

$sql_session_choices_v = <<<SQL
CREATE VIEW `bus-riders`.`session_choices_v` AS 
select 
  `b`.`rider_id` AS `rider_id`,
  `v`.`svs_id` AS `svs_id`,
  `s`.`ses_datetime` AS `ses_datetime`,
  `s`.`ses_expiration` AS `ses_expiration`,
  `c`.`ses_id` AS `ses_id`,
  `c`.`pax_count` AS `pax_count`,
  `v`.`svs_datetime` AS `svs_datetime`,
  `c`.`identifier` AS `identifier` 
from 
  (((`bus-riders`.`session_choices` `c` 
    join `bus-riders`.`services` `v` 
      on((`v`.`svs_id` = `c`.`svs_id`))) 
    join `bus-riders`.`sessions` `s` 
      on((`c`.`ses_id` = `s`.`ses_id`))) 
    join `bus-riders`.`bus_riders` `b` 
      on((`b`.`rider_id` = `s`.`rider_id`)))
SQL;

$sql_session_log_v = <<<SQL
CREATE VIEW `bus-riders`.`session_log_v` AS 
SELECT 
  `b`.`phone_number` AS `phone_number`,
  `l`.`ses_log_id` AS `ses_log_id`,
  `l`.`ses_id` AS `ses_id`,
  `l`.`ses_datetime` AS `ses_datetime`,
  `l`.`ses_sender` AS `ses_sender`,
  if((`l`.`ses_sender` = _latin1'S'),1,2) AS `ses_sender_ord`,
  `s`.`ses_expiration` AS `ses_expiration`,
  `s`.`rider_id` AS `rider_id`,
  `l`.`ses_text` AS `ses_text` 
FROM
  ((`bus-riders`.`session_log` `l` 
    join `bus-riders`.`sessions` `s` 
	  on((`l`.`ses_id` = `s`.`ses_id`))) 
	join `bus-riders`.`bus_riders` `b` 
	  on((`s`.`rider_id` = `b`.`rider_id`)))
UNION 
SELECT 
  `bus-riders`.`unregistered_log`.`unr_phone_number` AS `phone_number`,
  -(1) AS `ses_log_id`,
  -(2) AS `ses_id`,
  `bus-riders`.`unregistered_log`.`unr_datetime` AS `ses_datetime`,
  `bus-riders`.`unregistered_log`.`unr_sender` AS `ses_sender`,
  if((`bus-riders`.`unregistered_log`.`unr_sender` = _latin1'S'),1,2) AS `ses_sender_ord`,
  `bus-riders`.`unregistered_log`.`unr_expiration` AS `ses_expiration`,
  -(3) AS `rider_id`,`bus-riders`.`unregistered_log`.
  `unr_text` AS `ses_text` from `bus-riders`.`unregistered_log` 
ORDER BY 1,3,6;
SQL;

$sql_blocked_numbers = <<<SQL
CREATE TABLE `blocked_numbers` (
  `blocked_id` int(11) NOT NULL auto_increment,
  `blocked_number` varchar(15) NOT NULL,
  `reason` varchar(250) NOT NULL,
  PRIMARY KEY  (`blocked_id`),
  KEY `blocked_number` (`blocked_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
SQL;

$table_names = array(
  "bus_riders",
  "bus_riders_reg",
  "bus_riders_reg_insert",
  "bus_rides",
  "default_services",
  "default_services_insert",
  "services",
  "sessions",
  "session_choices",
  "session_log",
  "unregistered_log",
  "session_choices_v",
  "session_log_v",
  "blocked_numbers"  
);

$table_data = array(
  "bus_riders"              =>  array(
                                        NAME_LONG => "Bus Riders",
                                        QUERY => $sql_bus_riders,
                                        DESCRIPTION => "Table of registered bus riders",
                                        REMARKS => "N/A" ),
  "bus_riders_reg"          =>  array(
                                        NAME_LONG => "Bus Riders Beta",
                                        QUERY => $sql_bus_riders_reg,
                                        DESCRIPTION => "Table of beta testers",
                                        REMARKS => "N/A" ),
  "bus_riders_reg_insert"   =>  array(
                                        NAME_LONG => "Bus Riders Beta Data", 
                                        QUERY => $sql_bus_riders_reg_insert,
                                        DESCRIPTION => "Insert command to load bus_riders_reg",
                                        REMARKS => "N/A" ),
  "bus_rides"               =>  array(
                                        NAME_LONG => "Bus Rides", 
                                        QUERY => $sql_bus_rides,
                                        DESCRIPTION => "Table of rides for each rider",
                                        REMARKS => "N/A" ),
  "default_services"        =>  array(
                                        NAME_LONG => "Default Services", 
                                        QUERY => $sql_default_services,
                                        DESCRIPTION => "Table of default services for each week",
                                        REMARKS => "N/A" ),
  "default_services_insert" =>  array(
                                        NAME_LONG => "Default Services Data",
                                        QUERY => $sql_default_services_insert,
                                        DESCRIPTION => "Insert command of default services",
                                        REMARKS => "N/A" ),
  "services"                =>  array(
                                        NAME_LONG => "Services",
                                        QUERY => $sql_services,
                                        DESCRIPTION => "Calendar of services",
                                        REMARKS => "N/A" ),
  "sessions"                =>  array(
                                        NAME_LONG => "Sessions", 
                                        QUERY => $sql_sessions,
                                        DESCRIPTION => "Table of sessions",
                                        REMARKS => "N/A" ),
  "session_choices"         =>  array(
                                        NAME_LONG => "Session Choices",   
                                        QUERY => $sql_session_choices,
                                        DESCRIPTION => "Table of choice per session",
                                        REMARKS => "N/A" ),
  "session_log"             =>  array(
                                        NAME_LONG => "Session Log",    
                                        QUERY => $sql_session_log,
                                        DESCRIPTION => "Log of messages for each session",
                                        REMARKS => "N/A" ),
  "unregistered_log"             =>  array(
                                        NAME_LONG => "Unregistered Log",     
                                        QUERY => $sql_unregistered_log,
                                        DESCRIPTION => "Log of messages from unregistered users",
                                        REMARKS => "N/A" ),
  "session_choices_v"             =>  array(
                                        NAME_LONG => "Session Choices View",      
                                        QUERY => $sql_session_choices_v,
                                        DESCRIPTION => "View of choices per session",
                                        REMARKS => "N/A" ),
  "session_log_v"             =>  array(
                                        NAME_LONG => "Session Log View",      
                                        QUERY => $sql_session_log_v,
                                        DESCRIPTION => "View of log per session",
                                        REMARKS => "N/A" ),      
  "blocked_numbers"           =>  array(
                                        NAME_LONG => "Blocked Numbers",      
                                        QUERY => $sql_blocked_numbers,
                                        DESCRIPTION => "Table of blocked numbers",
                                        REMARKS => "N/A" )      
);

?>


