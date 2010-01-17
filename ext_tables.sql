#
# Table structure for table 'tx_powermail_fieldsets'
#
CREATE TABLE tx_powermail_fieldsets (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,	
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	tt_content int(11) DEFAULT '0' NOT NULL,
	felder int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	class text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_powermail_fields'
#
CREATE TABLE tx_powermail_fields (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,	
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,	
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	fieldset int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	formtype text NOT NULL,
	flexform text NOT NULL,
	fe_field text NOT NULL,
	name tinytext NOT NULL,
	description text NOT NULL,
	class text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_powermail_mails'
#
CREATE TABLE tx_powermail_mails (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	formid int(11) DEFAULT '0' NOT NULL,
	recipient tinytext NOT NULL,
	cc_recipient text NOT NULL,
	subject_r tinytext NOT NULL,
	sender tinytext NOT NULL,
	content text NOT NULL,
	piVars text NOT NULL,
	feuser int(11) DEFAULT '0' NOT NULL,
	senderIP tinytext NOT NULL,
	UserAgent text NOT NULL,
	Referer text NOT NULL,
	SP_TZ tinytext NOT NULL,
	Additional text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_powermail_title tinytext NOT NULL,
	tx_powermail_recipient text NOT NULL,
	tx_powermail_subject_r tinytext NOT NULL,
	tx_powermail_subject_s tinytext NOT NULL,
	tx_powermail_sender tinytext NOT NULL,
	tx_powermail_sendername tinytext NOT NULL,
	tx_powermail_confirm tinyint(3) DEFAULT '0' NOT NULL,
	tx_powermail_pages tinytext NOT NULL,
	tx_powermail_multiple tinyint(3) DEFAULT '0' NOT NULL,
	tx_powermail_recip_table text NOT NULL,
	tx_powermail_recip_id text NOT NULL,
	tx_powermail_recip_field text NOT NULL,
	tx_powermail_thanks text NOT NULL,
	tx_powermail_mailsender text NOT NULL,
	tx_powermail_mailreceiver text NOT NULL,
	tx_powermail_redirect tinytext NOT NULL,
	tx_powermail_fieldsets int(11) DEFAULT '0' NOT NULL,
	tx_powermail_users int(11) DEFAULT '0' NOT NULL,
	tx_powermail_preview int(1) DEFAULT '0' NOT NULL,
);
