# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.1.73)
# Database: fall2017_workintent
# Generation Time: 2018-02-27 18:14:44 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table actions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `actions`;

CREATE TABLE `actions` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `localTimestamp` bigint(20) DEFAULT NULL,
  `localTime` time DEFAULT NULL,
  `localDate` date DEFAULT NULL,
  `action` text,
  `value` varchar(1000) DEFAULT NULL,
  `ip` text,
  `manuallyRecovered` tinyint(1) DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `group` text,
  `action_json` text,
  PRIMARY KEY (`actionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table click_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `click_data`;

CREATE TABLE `click_data` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `clientX` float DEFAULT NULL,
  `clientY` float DEFAULT NULL,
  `pageX` float DEFAULT NULL,
  `pageY` float DEFAULT NULL,
  `screenX` float DEFAULT NULL,
  `screenY` float DEFAULT NULL,
  `scrollX` float DEFAULT NULL,
  `scrollY` float DEFAULT NULL,
  `type` text,
  `url` text,
  `timestamp` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `localTimestamp` bigint(20) DEFAULT NULL,
  `localTime` time DEFAULT NULL,
  `localDate` date DEFAULT NULL,
  `value` varchar(1000) DEFAULT NULL,
  `ip` text,
  `manuallyRecovered` tinyint(1) DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `group` text,
  PRIMARY KEY (`actionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table copy_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `copy_data`;

CREATE TABLE `copy_data` (
  `snippetID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `stageID` varchar(45) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `url` text,
  `title` text,
  `snippet` text,
  `timestamp` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `localTimestamp` bigint(20) DEFAULT NULL,
  `localDate` date DEFAULT NULL,
  `localTime` time DEFAULT NULL,
  `note` text,
  `type` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `group` text,
  PRIMARY KEY (`snippetID`),
  KEY `userID` (`userID`),
  KEY `projectID` (`projectID`),
  FULLTEXT KEY `snippet` (`snippet`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table intent_assignments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `intent_assignments`;

CREATE TABLE `intent_assignments` (
  `assignmentID` int(11) NOT NULL AUTO_INCREMENT,
  `querySegmentID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `taskID` int(11) DEFAULT NULL,
  `sessionID` int(11) DEFAULT NULL,
  `time_start` text,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `id_start` tinyint(1) DEFAULT NULL,
  `id_more` tinyint(1) DEFAULT NULL,
  `learn_feature` tinyint(1) DEFAULT NULL,
  `learn_structure` tinyint(1) DEFAULT NULL,
  `learn_domain` tinyint(1) DEFAULT NULL,
  `learn_database` tinyint(1) DEFAULT NULL,
  `find_known` tinyint(1) DEFAULT NULL,
  `find_specific` tinyint(1) DEFAULT NULL,
  `find_common` tinyint(1) DEFAULT NULL,
  `find_without` tinyint(1) DEFAULT NULL,
  `locate_specific` tinyint(1) DEFAULT NULL,
  `locate_common` tinyint(1) DEFAULT NULL,
  `locate_area` tinyint(1) DEFAULT NULL,
  `keep_bibliographical` tinyint(1) DEFAULT NULL,
  `keep_link` tinyint(1) DEFAULT NULL,
  `keep_item` tinyint(1) DEFAULT NULL,
  `access_item` tinyint(1) DEFAULT NULL,
  `access_common` tinyint(1) DEFAULT NULL,
  `access_area` tinyint(1) DEFAULT NULL,
  `evaluate_correctness` tinyint(1) DEFAULT NULL,
  `evaluate_specificity` tinyint(1) DEFAULT NULL,
  `evaluate_usefulness` tinyint(1) DEFAULT NULL,
  `evaluate_best` tinyint(1) DEFAULT NULL,
  `evaluate_duplication` tinyint(1) DEFAULT NULL,
  `obtain_specific` tinyint(1) DEFAULT NULL,
  `obtain_part` tinyint(1) DEFAULT NULL,
  `obtain_whole` tinyint(1) DEFAULT NULL,
  `other` tinyint(1) DEFAULT NULL,
  `id_start_success` tinyint(1) DEFAULT NULL,
  `id_start_failure_reason` text,
  `id_more_success` tinyint(1) DEFAULT NULL,
  `id_more_failure_reason` text,
  `learn_feature_success` tinyint(1) DEFAULT NULL,
  `learn_feature_failure_reason` text,
  `learn_structure_success` tinyint(1) DEFAULT NULL,
  `learn_structure_failure_reason` text,
  `learn_domain_success` tinyint(1) DEFAULT NULL,
  `learn_domain_failure_reason` text,
  `learn_database_success` tinyint(1) DEFAULT NULL,
  `learn_database_failure_reason` text,
  `find_known_success` tinyint(1) DEFAULT NULL,
  `find_known_failure_reason` text,
  `find_specific_success` tinyint(1) DEFAULT NULL,
  `find_specific_failure_reason` text,
  `find_common_success` tinyint(1) DEFAULT NULL,
  `find_common_failure_reason` text,
  `find_without_success` tinyint(1) DEFAULT NULL,
  `find_without_failure_reason` text,
  `locate_specific_success` tinyint(1) DEFAULT NULL,
  `locate_specific_failure_reason` text,
  `locate_common_success` tinyint(1) DEFAULT NULL,
  `locate_common_failure_reason` text,
  `locate_area_success` tinyint(1) DEFAULT NULL,
  `locate_area_failure_reason` text,
  `keep_bibliographical_success` tinyint(1) DEFAULT NULL,
  `keep_bibliographical_failure_reason` text,
  `keep_link_success` tinyint(1) DEFAULT NULL,
  `keep_link_failure_reason` text,
  `keep_item_success` tinyint(1) DEFAULT NULL,
  `keep_item_failure_reason` text,
  `access_item_success` tinyint(1) DEFAULT NULL,
  `access_item_failure_reason` text,
  `access_common_success` tinyint(1) DEFAULT NULL,
  `access_common_failure_reason` text,
  `access_area_success` tinyint(1) DEFAULT NULL,
  `access_area_failure_reason` text,
  `evaluate_correctness_success` tinyint(1) DEFAULT NULL,
  `evaluate_correctness_failure_reason` text,
  `evaluate_specificity_success` tinyint(1) DEFAULT NULL,
  `evaluate_specificity_failure_reason` text,
  `evaluate_usefulness_success` tinyint(1) DEFAULT NULL,
  `evaluate_usefulness_failure_reason` text,
  `evaluate_best_success` tinyint(1) DEFAULT NULL,
  `evaluate_best_failure_reason` text,
  `evaluate_duplication_success` tinyint(1) DEFAULT NULL,
  `evaluate_duplication_failure_reason` text,
  `obtain_specific_success` tinyint(1) DEFAULT NULL,
  `obtain_specific_failure_reason` text,
  `obtain_part_success` tinyint(1) DEFAULT NULL,
  `obtain_part_failure_reason` text,
  `obtain_whole_success` tinyint(1) DEFAULT NULL,
  `obtain_whole_failure_reason` text,
  `other_success` tinyint(1) DEFAULT NULL,
  `other_failure_reason` text,
  `other_description` text,
  PRIMARY KEY (`assignmentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table intent_permutations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `intent_permutations`;

CREATE TABLE `intent_permutations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `intentGroupID` int(11) DEFAULT NULL,
  `intentID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table keystroke_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `keystroke_data`;

CREATE TABLE `keystroke_data` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `keyCode` int(11) DEFAULT NULL,
  `modifiers` text,
  `url` text,
  `timestamp` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `localTimestamp` bigint(20) DEFAULT NULL,
  `localTime` time DEFAULT NULL,
  `localDate` date DEFAULT NULL,
  `action` text,
  `value` varchar(1000) DEFAULT NULL,
  `ip` text,
  `manuallyRecovered` tinyint(1) DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `group` text,
  PRIMARY KEY (`actionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table mouse_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mouse_data`;

CREATE TABLE `mouse_data` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `clientX` float DEFAULT NULL,
  `clientY` float DEFAULT NULL,
  `pageX` float DEFAULT NULL,
  `pageY` float DEFAULT NULL,
  `screenX` float DEFAULT NULL,
  `screenY` float DEFAULT NULL,
  `scrollX` float DEFAULT NULL,
  `scrollY` float DEFAULT NULL,
  `type` text,
  `url` text,
  `timestamp` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `localTimestamp` bigint(20) DEFAULT NULL,
  `localTime` time DEFAULT NULL,
  `localDate` date DEFAULT NULL,
  `value` varchar(1000) DEFAULT NULL,
  `ip` text,
  `manuallyRecovered` tinyint(1) DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `group` text,
  PRIMARY KEY (`actionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table page_viewtimes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `page_viewtimes`;

CREATE TABLE `page_viewtimes` (
  `viewtimeID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `url` text,
  `title` text,
  `source` text,
  `host` text,
  `query` text,
  `timestamp` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `localTimestamp` bigint(20) DEFAULT NULL,
  `localDate` date DEFAULT NULL,
  `localTime` time DEFAULT NULL,
  `result` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `duplicated` int(11) DEFAULT NULL,
  `manually_recovered` tinyint(1) DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `group` text,
  PRIMARY KEY (`viewtimeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pages`;

CREATE TABLE `pages` (
  `pageID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `sessionID` int(11) DEFAULT NULL,
  `taskID` int(11) DEFAULT NULL,
  `querySegmentID` int(11) DEFAULT NULL,
  `querySegmentID_automatic` tinyint(1) NOT NULL DEFAULT '0',
  `tabID` int(11) DEFAULT NULL,
  `windowID` int(11) DEFAULT NULL,
  `active_tab` tinyint(1) DEFAULT NULL,
  `trash` tinyint(1) DEFAULT NULL,
  `permanently_delete` tinyint(1) DEFAULT NULL,
  `is_query` tinyint(1) DEFAULT NULL,
  `is_coagmento` tinyint(1) DEFAULT NULL,
  `url` text,
  `title` text,
  `source` text,
  `host` text,
  `query` text,
  `action` text,
  `timestamp` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `localTimestamp` bigint(20) DEFAULT NULL,
  `localDate` date DEFAULT NULL,
  `localTime` time DEFAULT NULL,
  `result` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `duplicated` int(11) DEFAULT NULL,
  `manually_recovered` tinyint(1) DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `group` text,
  `tab_json` text,
  `details` text,
  PRIMARY KEY (`pageID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table participant_id_to_task
# ------------------------------------------------------------

DROP TABLE IF EXISTS `participant_id_to_task`;

CREATE TABLE `participant_id_to_task` (
  `participantID` varchar(500) DEFAULT NULL,
  `topicName1` varchar(500) DEFAULT NULL,
  `taskName1` varchar(500) DEFAULT NULL,
  `questionID1` int(10) unsigned NOT NULL,
  `topicName2` varchar(500) DEFAULT NULL,
  `taskName2` varchar(500) DEFAULT NULL,
  `questionID2` int(10) unsigned NOT NULL,
  UNIQUE KEY `user_id` (`participantID`),
  UNIQUE KEY `name` (`participantID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table paste_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `paste_data`;

CREATE TABLE `paste_data` (
  `snippetID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `stageID` varchar(45) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `from_url` text,
  `from_title` text,
  `snippet` text,
  `to_url` text,
  `to_title` text,
  `timestamp` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `localTimestamp` bigint(20) DEFAULT NULL,
  `localDate` date DEFAULT NULL,
  `localTime` time DEFAULT NULL,
  `note` text,
  `type` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `group` text,
  PRIMARY KEY (`snippetID`),
  KEY `userID` (`userID`),
  KEY `projectID` (`projectID`),
  FULLTEXT KEY `snippet` (`snippet`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table queries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `queries`;

CREATE TABLE `queries` (
  `queryID` int(11) NOT NULL AUTO_INCREMENT,
  `projectID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `taskID` int(11) DEFAULT NULL,
  `querySegmentID` int(11) DEFAULT NULL,
  `querySegmentID_automatic` tinyint(1) NOT NULL DEFAULT '0',
  `sessionID` int(11) DEFAULT NULL,
  `tabID` int(11) DEFAULT NULL,
  `windowID` int(11) DEFAULT NULL,
  `trash` tinyint(11) DEFAULT NULL,
  `permanently_delete` tinyint(11) DEFAULT NULL,
  `active_tab` tinyint(11) DEFAULT NULL,
  `is_coagmento` tinyint(11) DEFAULT NULL,
  `query` text,
  `source` text,
  `host` text,
  `url` text,
  `title` text,
  `action` text,
  `timestamp` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `localTimestamp` bigint(20) DEFAULT NULL,
  `localDate` date DEFAULT NULL,
  `localTime` time DEFAULT NULL,
  `topResults` text,
  `manuallyRecovered` tinyint(1) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `group` text,
  `tab_json` text,
  `details` text,
  PRIMARY KEY (`queryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table querysegment_labels_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `querysegment_labels_user`;

CREATE TABLE `querysegment_labels_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `querySegmentLabel` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `userID` int(11) NOT NULL DEFAULT '0',
  `projectID` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table questionnaire_answers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `questionnaire_answers`;

CREATE TABLE `questionnaire_answers` (
  `userID` int(11) NOT NULL DEFAULT '0',
  `year` varchar(100) DEFAULT NULL,
  `gender` char(1) NOT NULL,
  `date_firstchoice` varchar(100) DEFAULT NULL,
  `date_secondchoice` varchar(100) NOT NULL DEFAULT '',
  `outcome_satisfaction` varchar(100) DEFAULT NULL,
  `experience_satisfaction` varchar(100) DEFAULT NULL,
  `topic_knowledge` varchar(100) DEFAULT NULL,
  `search_experience` varchar(100) DEFAULT NULL,
  `motivation` varchar(100) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `approved` tinyint(1) DEFAULT NULL,
  `strat_divide_work` int(100) DEFAULT NULL,
  `strat_schedule_meetings` int(100) DEFAULT NULL,
  `strat_assign_tasks` int(100) DEFAULT NULL,
  `strat_establish_goals` int(100) DEFAULT NULL,
  `strat_set_deadlines` int(100) DEFAULT NULL,
  `strat_use_collab_tools` int(100) DEFAULT NULL,
  `strat_meet_in_person` int(100) DEFAULT NULL,
  `strat_meet_virtual` int(100) DEFAULT NULL,
  `strat_comm_text` int(100) DEFAULT NULL,
  `strat_track_progress` int(100) DEFAULT NULL,
  `obs_sched_conflict` int(100) DEFAULT NULL,
  `obs_lack_time` int(100) DEFAULT NULL,
  `obs_comm_group` int(100) DEFAULT NULL,
  `obs_consensus` int(100) DEFAULT NULL,
  `obs_coord` int(100) DEFAULT NULL,
  `obs_meet_deadlines` int(100) DEFAULT NULL,
  `obs_unequal_participation` int(100) DEFAULT NULL,
  `obs_lack_leadership` int(100) DEFAULT NULL,
  `obs_procrastination` int(100) DEFAULT NULL,
  `obs_lack_motivation` int(100) DEFAULT NULL,
  `lk_group_assign_productive` varchar(100) DEFAULT '',
  `lk_group_ideas` varchar(100) DEFAULT '',
  `lk_group_fun` varchar(100) DEFAULT '',
  `lk_alone_efficient` varchar(100) DEFAULT NULL,
  `lk_teacher_efficient` varchar(100) DEFAULT '',
  `lk_close_work_learning` varchar(100) DEFAULT '',
  `lk_help_from_members` varchar(100) DEFAULT NULL,
  `lk_group_work_like` varchar(100) DEFAULT '',
  `lk_one_does_most` varchar(100) DEFAULT '',
  `lk_happy_as_leader` varchar(100) DEFAULT '',
  `lk_group_fits_habits` varchar(100) DEFAULT '',
  `lk_group_discuss_waste` varchar(100) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table questionnaire_entry_demographic
# ------------------------------------------------------------

DROP TABLE IF EXISTS `questionnaire_entry_demographic`;

CREATE TABLE `questionnaire_entry_demographic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `search_years` text,
  `device_expertise` text,
  `work_years` text,
  `work_role` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table questionnaire_entry_tasks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `questionnaire_entry_tasks`;

CREATE TABLE `questionnaire_entry_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `task_idcolumn` int(11) DEFAULT NULL,
  `name` text,
  `description` text,
  `frequency` int(11) DEFAULT NULL,
  `familiarity` int(11) DEFAULT NULL,
  `completiontime` int(11) DEFAULT NULL,
  `individual_complete` varchar(10) DEFAULT NULL,
  `num_collaborators` varchar(30) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table questionnaire_exit_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `questionnaire_exit_sessions`;

CREATE TABLE `questionnaire_exit_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessionID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `taskID` int(11) DEFAULT NULL,
  `intention_clarifications` text,
  `intention_transitions` text,
  `successful` int(11) DEFAULT NULL,
  `successful_description` text,
  `problematic` int(11) DEFAULT NULL,
  `problematic_description` text,
  `useful` int(11) DEFAULT NULL,
  `useful_description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table questionnaire_exit_tasks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `questionnaire_exit_tasks`;

CREATE TABLE `questionnaire_exit_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `taskID` int(11) DEFAULT NULL,
  `task_accomplishment` text,
  `task_stage` int(11) DEFAULT NULL,
  `goal` int(11) DEFAULT NULL,
  `importance` int(11) DEFAULT NULL,
  `urgency` int(11) DEFAULT NULL,
  `difficulty` int(11) DEFAULT NULL,
  `complexity` int(11) DEFAULT NULL,
  `knowledge_topic` int(11) DEFAULT NULL,
  `knowledge_procedures` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table questionnaire_exit_tool
# ------------------------------------------------------------

DROP TABLE IF EXISTS `questionnaire_exit_tool`;

CREATE TABLE `questionnaire_exit_tool` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `reviewannotation_clear` int(11) DEFAULT NULL,
  `intentions_understandable` int(11) DEFAULT NULL,
  `intentions_adequate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table questionnaire_questions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `questionnaire_questions`;

CREATE TABLE `questionnaire_questions` (
  `questionID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) DEFAULT '',
  `question_type` varchar(100) DEFAULT '',
  `question_cat` varchar(100) DEFAULT '',
  `question` text,
  `question_data` text,
  `optional` text,
  PRIMARY KEY (`questionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table questionnaire_recruitment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `questionnaire_recruitment`;

CREATE TABLE `questionnaire_recruitment` (
  `userID` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `time` time NOT NULL,
  `approved` tinyint(1) DEFAULT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `year` varchar(100) DEFAULT NULL,
  `pc` varchar(100) DEFAULT NULL,
  `gender` char(1) NOT NULL,
  `topic_knowledge` varchar(100) DEFAULT NULL,
  `motivation` varchar(100) DEFAULT NULL,
  `search_experience` varchar(100) DEFAULT NULL,
  `date_firstchoice` varchar(100) DEFAULT NULL,
  `date_secondchoice` varchar(100) DEFAULT NULL,
  `doneproj` varchar(100) NOT NULL DEFAULT '',
  `outcome_satisfaction` varchar(100) DEFAULT NULL,
  `experience_satisfaction` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table questions_progress
# ------------------------------------------------------------

DROP TABLE IF EXISTS `questions_progress`;

CREATE TABLE `questions_progress` (
  `qProgressID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `startTimestamp` int(11) DEFAULT NULL,
  `startTime` time DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `endTimestamp` int(11) DEFAULT NULL,
  `endTime` time DEFAULT NULL,
  `answer` text,
  `responses` int(11) DEFAULT '0',
  `correct` tinyint(1) DEFAULT NULL,
  `validInTime` tinyint(1) DEFAULT '1',
  `skip` int(11) DEFAULT '0',
  `finish` int(11) DEFAULT '0',
  `topicAreaID` int(11) DEFAULT '0',
  PRIMARY KEY (`qProgressID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table questions_study
# ------------------------------------------------------------

DROP TABLE IF EXISTS `questions_study`;

CREATE TABLE `questions_study` (
  `questionID` int(11) NOT NULL AUTO_INCREMENT,
  `topic` text NOT NULL,
  `category` text NOT NULL,
  `question` text NOT NULL,
  `product` varchar(100) DEFAULT NULL,
  `level` varchar(100) DEFAULT NULL,
  `goal` varchar(100) DEFAULT NULL,
  `named` tinyint(1) DEFAULT NULL,
  `walkthough` text NOT NULL,
  `answer` varchar(100) NOT NULL,
  `altAnswer` varchar(100) DEFAULT NULL,
  `complexity` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `externalResource` varchar(200) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `evaluation` varchar(45) DEFAULT NULL,
  `topicAreaID` int(11) DEFAULT NULL,
  PRIMARY KEY (`questionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table recruits
# ------------------------------------------------------------

DROP TABLE IF EXISTS `recruits`;

CREATE TABLE `recruits` (
  `recruitsID` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(100) NOT NULL DEFAULT '',
  `lastName` varchar(100) NOT NULL DEFAULT '',
  `age` int(11) NOT NULL DEFAULT '0',
  `sex` varchar(100) NOT NULL,
  `year` varchar(100) NOT NULL,
  `email1` varchar(100) NOT NULL DEFAULT '',
  `email_sha1` varchar(100) NOT NULL,
  `approved` tinyint(1) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `comments` text,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `projectID` int(11) NOT NULL DEFAULT '0',
  `userID` int(11) NOT NULL DEFAULT '0',
  `registrationID` text NOT NULL,
  `experimenter` text NOT NULL,
  `timezone` text NOT NULL,
  `date_firstchoice` varchar(100) DEFAULT '',
  `date_secondchoice` varchar(100) DEFAULT NULL,
  `receiptnumber` int(11) NOT NULL DEFAULT '0',
  `consent_furtheruseonline` tinyint(1) DEFAULT NULL,
  `reminder_sent` tinyint(1) NOT NULL,
  `crashing` tinyint(1) DEFAULT NULL,
  `bad_intention_annotation_1` tinyint(1) DEFAULT NULL,
  `bad_intention_annotation_2` tinyint(1) DEFAULT NULL,
  `bad_intention_annotation_reason_1` text NOT NULL,
  `bad_intention_annotation_reason_2` text NOT NULL,
  `recruitment_source` varchar(100) NOT NULL DEFAULT '',
  `interview_medium` text NOT NULL,
  `medium_credentials` text NOT NULL,
  PRIMARY KEY (`recruitsID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table scroll_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `scroll_data`;

CREATE TABLE `scroll_data` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `questionID` int(11) DEFAULT NULL,
  `clientX` float DEFAULT NULL,
  `clientY` float DEFAULT NULL,
  `pageX` float DEFAULT NULL,
  `pageY` float DEFAULT NULL,
  `screenX` float DEFAULT NULL,
  `screenY` float DEFAULT NULL,
  `scrollX` float DEFAULT NULL,
  `scrollY` float DEFAULT NULL,
  `type` text,
  `url` text,
  `timestamp` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `localTimestamp` bigint(20) DEFAULT NULL,
  `localTime` time DEFAULT NULL,
  `localDate` date DEFAULT NULL,
  `value` varchar(1000) DEFAULT NULL,
  `ip` text,
  `manuallyRecovered` tinyint(1) DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `group` text,
  PRIMARY KEY (`actionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table session_labels_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `session_labels_user`;

CREATE TABLE `session_labels_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessionLabel` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `userID` int(11) NOT NULL DEFAULT '0',
  `projectID` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table session_progress
# ------------------------------------------------------------

DROP TABLE IF EXISTS `session_progress`;

CREATE TABLE `session_progress` (
  `progressID` int(11) NOT NULL AUTO_INCREMENT,
  `projectID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `stageID` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  PRIMARY KEY (`progressID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table session_stages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `session_stages`;

CREATE TABLE `session_stages` (
  `stageID` smallint(4) NOT NULL,
  `description` varchar(45) NOT NULL,
  `page` varchar(45) DEFAULT NULL,
  `maxTime` int(11) DEFAULT NULL,
  `maxTimeQuestion` int(11) DEFAULT NULL,
  `minTimeQuestion` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `maxLoops` int(11) DEFAULT NULL,
  `loopStage` smallint(4) DEFAULT NULL,
  `synchStage` tinyint(4) DEFAULT NULL,
  `allowBrowsing` tinyint(4) DEFAULT NULL,
  `allowCommunication` varchar(45) DEFAULT NULL,
  `hideSidebar` tinyint(4) DEFAULT NULL,
  `stageDescription` text,
  PRIMARY KEY (`stageID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table study_progress
# ------------------------------------------------------------

DROP TABLE IF EXISTS `study_progress`;

CREATE TABLE `study_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lastupdate_timestamp` int(11) DEFAULT NULL,
  `slice_by` text,
  `slice_id` int(11) DEFAULT NULL,
  `var_name` text,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table task_labels_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `task_labels_user`;

CREATE TABLE `task_labels_user` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `taskID` int(11) NOT NULL,
  `taskName` text NOT NULL,
  `userID` int(11) NOT NULL DEFAULT '0',
  `projectID` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `exitinterview` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `participantID` text,
  `extensionID` text,
  `arrived` tinyint(1) DEFAULT NULL,
  `rerun` tinyint(1) DEFAULT '0',
  `username` varchar(100) DEFAULT NULL,
  `username_sha1` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `password_sha1` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `study` int(1) DEFAULT NULL,
  `ip` text,
  `topicAreaID1` int(11) NOT NULL,
  `topicAreaID2` int(11) NOT NULL,
  `finishTopic1` tinyint(1) NOT NULL,
  `finishTopic2` tinyint(1) NOT NULL,
  `finishIntent1` tinyint(1) NOT NULL,
  `comments` text,
  `optout` int(1) DEFAULT NULL,
  `group` text,
  `lastbootuptime` text,
  `numUsers` int(11) NOT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username` (`username`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
