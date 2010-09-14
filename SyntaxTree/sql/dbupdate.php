<#1>
DROP TABLE IF EXISTS `il_qpl_qst_syntaxtree_question`;
CREATE TABLE IF NOT EXISTS `il_qpl_qst_syntaxtree_question` (
  `question_fi` int(11) NOT NULL default '0',
  `textgap_rating` enum('ci','cs','l1','l2','l3','l4','l5') default NULL,
  `correctanswers` int(11) default '0',
  PRIMARY KEY  (`question_fi`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `il_qpl_qst_syntaxtree_answer`;
CREATE TABLE IF NOT EXISTS `il_qpl_qst_syntaxtree_answer` (
  `answer_id` int(10) unsigned NOT NULL auto_increment,
  `question_fi` int(10) unsigned NOT NULL default '0',
  `answertext` text NOT NULL,
  `points` double NOT NULL default '0',
  `aorder` int(10) unsigned NOT NULL default '0',
  `lastchange` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`answer_id`),
  KEY `question_fi` (`question_fi`)
) ENGINE=MyISAM;


<#2>
REPLACE INTO `qpl_qst_type`(type_tag, plugin) VALUES ('SyntaxTree', 1);

<#3>
DROP TABLE IF EXISTS `il_qpl_qst_syntaxtree_feedback`;
CREATE TABLE IF NOT EXISTS `il_qpl_qst_syntaxtree_feedback` (
  `feedback_id` int(11) unsigned NOT NULL default '0',
  `question_fi` int(11) unsigned NOT NULL default '0',
  `answer` int(11) unsigned NOT NULL default '0',
  `feedback` text NOT NULL, 
  `tstamp` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`feedback_id`),
  KEY `question_fi` (`question_fi`)
) ENGINE=MyISAM;
