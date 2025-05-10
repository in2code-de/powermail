#
# Table structure for table 'tx_powermail_domain_model_form'
#
CREATE TABLE tx_powermail_domain_model_form (
	title varchar(255) DEFAULT '' NOT NULL,
	note tinyint(4) unsigned DEFAULT '0' NOT NULL,
	css varchar(255) DEFAULT '' NOT NULL,
	pages varchar(255) DEFAULT '' NOT NULL,
	autocomplete_token varchar(3) DEFAULT '' NOT NULL,
	is_dummy_record tinyint(1) DEFAULT '0' NOT NULL,

	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_powermail_domain_model_page'
#
CREATE TABLE tx_powermail_domain_model_page (
	form int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	css varchar(255) DEFAULT '' NOT NULL,
	fields int(11) unsigned DEFAULT '0' NOT NULL,

	KEY parent_form (form),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_powermail_domain_model_field'
#
CREATE TABLE tx_powermail_domain_model_field (
	page int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	type varchar(255) DEFAULT '' NOT NULL,
	settings text NOT NULL,
	path varchar(255) DEFAULT '' NOT NULL,
	content_element int(11) DEFAULT '0' NOT NULL,
	text text NOT NULL,
	prefill_value text NOT NULL,
	placeholder text NOT NULL,
	placeholder_repeat text NOT NULL,
	create_from_typoscript text NOT NULL,
	validation int(11) DEFAULT '0' NOT NULL,
	validation_configuration varchar(255) DEFAULT '' NOT NULL,
	css varchar(255) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,
	multiselect tinyint(4) unsigned DEFAULT '0' NOT NULL,
	datepicker_settings varchar(255) DEFAULT '' NOT NULL,
	feuser_value varchar(255) DEFAULT '' NOT NULL,
	sender_email tinyint(4) unsigned DEFAULT '0' NOT NULL,
	sender_name tinyint(4) unsigned DEFAULT '0' NOT NULL,
	mandatory tinyint(4) unsigned DEFAULT '0' NOT NULL,
	own_marker_select tinyint(4) unsigned DEFAULT '0' NOT NULL,
	marker varchar(255) DEFAULT '' NOT NULL,
	mandatory_text varchar(255) DEFAULT '' NOT NULL,
	autocomplete_token   varchar(20)  DEFAULT '' NOT NULL,
	autocomplete_section varchar(100) DEFAULT '' NOT NULL,
	autocomplete_type    varchar(8)   DEFAULT '' NOT NULL,
	autocomplete_purpose varchar(8)   DEFAULT '' NOT NULL,

	# Dummy Fields
	auto_marker tinyint(2) unsigned DEFAULT '0' NOT NULL,

	KEY parent_page (page),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_powermail_domain_model_mail'
#
CREATE TABLE tx_powermail_domain_model_mail (
	sender_name varchar(255) DEFAULT '' NOT NULL,
	sender_mail varchar(255) DEFAULT '' NOT NULL,
	subject varchar(255) DEFAULT '' NOT NULL,
	receiver_mail varchar(1024) DEFAULT '' NOT NULL,
	body text NOT NULL,
	feuser int(11) DEFAULT '0' NOT NULL,
	sender_ip tinytext NOT NULL,
	user_agent text NOT NULL,
	time int(11) DEFAULT '0' NOT NULL,
	form int(11) DEFAULT '0' NOT NULL,
	answers int(11) unsigned DEFAULT '0' NOT NULL,
	marketing_referer_domain text,
	marketing_referer text,
	marketing_country text,
	marketing_mobile_device tinyint(4) unsigned DEFAULT '0' NOT NULL,
	marketing_frontend_language int(11) DEFAULT '0' NOT NULL,
	marketing_browser_language text,
	marketing_page_funnel text,
	spam_factor varchar(255) DEFAULT '' NOT NULL,

	KEY form (form),
	KEY feuser (feuser)
);

#
# Table structure for table 'tx_powermail_domain_model_answer'
#
CREATE TABLE tx_powermail_domain_model_answer (
	mail int(11) unsigned DEFAULT '0' NOT NULL,

	value text NOT NULL,
	value_type int(11) unsigned DEFAULT '0' NOT NULL,
	field int(11) unsigned DEFAULT '0' NOT NULL,

	KEY mail (mail),
	KEY deleted (deleted),
	KEY hidden (hidden),
	KEY language (l10n_parent,sys_language_uid)
);
