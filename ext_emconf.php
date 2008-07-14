<?php

########################################################################
# Extension Manager/Repository config file for ext: "powermail"
#
# Auto generated 01-07-2008 16:35
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'powermail',
	'description' => 'Powerful and easy mailform extension with many features like IRRE, database storing (Excel and CSV export), different HTML templates, javascript validation, morestep forms, works with date2cal and static_info_tables and many more...',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.3.8',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_powermail/files',
	'modify_tables' => 'tt_content',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Alexander Kellner, Mischa Heissmann',
	'author_email' => 'alexander.kellner@einpraegsam.net, typo3.2008@heissmann.org',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '4.0.0-0.0.0',
			'typo3' => '3.8.0-0.0.0',
		),
		'conflicts' => array(
			'dbal' => '',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:146:{s:21:"ext_conf_template.txt";s:4:"8949";s:12:"ext_icon.gif";s:4:"4fcc";s:17:"ext_localconf.php";s:4:"b05a";s:14:"ext_tables.php";s:4:"408e";s:14:"ext_tables.sql";s:4:"26c7";s:28:"ext_typoscript_constants.txt";s:4:"6cd5";s:24:"ext_typoscript_setup.txt";s:4:"1404";s:28:"icon_tx_powermail_fields.gif";s:4:"9a15";s:31:"icon_tx_powermail_fields__h.gif";s:4:"07b3";s:31:"icon_tx_powermail_fieldsets.gif";s:4:"35ac";s:34:"icon_tx_powermail_fieldsets__h.gif";s:4:"ebeb";s:27:"icon_tx_powermail_forms.gif";s:4:"80fe";s:30:"icon_tx_powermail_forms__h.gif";s:4:"b151";s:27:"icon_tx_powermail_mails.gif";s:4:"fcba";s:30:"icon_tx_powermail_mails__h.gif";s:4:"2f4b";s:13:"locallang.xml";s:4:"abb0";s:16:"locallang_db.xml";s:4:"2525";s:7:"tca.php";s:4:"42e5";s:18:"css/multipleJS.css";s:4:"609e";s:17:"css/sampleCSS.css";s:4:"c372";s:13:"doc/Thumbs.db";s:4:"492a";s:14:"doc/manual.sxw";s:4:"0114";s:19:"doc/wizard_form.dat";s:4:"2318";s:20:"doc/wizard_form.html";s:4:"8791";s:31:"doc/database_relation/Thumbs.db";s:4:"482f";s:53:"doc/database_relation/powermail_database_relation.gif";s:4:"20e9";s:19:"doc/hook/hooks.html";s:4:"790f";s:18:"doc/hook/hooks.ods";s:4:"0435";s:32:"doc/powermail_graphics/Thumbs.db";s:4:"06ab";s:35:"doc/powermail_graphics/db_icons.psd";s:4:"0ac0";s:35:"doc/powermail_graphics/ext_icon.gif";s:4:"4fcc";s:51:"doc/powermail_graphics/icon_tx_powermail_fields.gif";s:4:"9a15";s:54:"doc/powermail_graphics/icon_tx_powermail_fields__h.gif";s:4:"e819";s:54:"doc/powermail_graphics/icon_tx_powermail_fieldsets.gif";s:4:"35ac";s:57:"doc/powermail_graphics/icon_tx_powermail_fieldsets__h.gif";s:4:"c8e0";s:50:"doc/powermail_graphics/icon_tx_powermail_forms.gif";s:4:"80fe";s:53:"doc/powermail_graphics/icon_tx_powermail_forms__h.gif";s:4:"bcd5";s:50:"doc/powermail_graphics/icon_tx_powermail_mails.gif";s:4:"fcba";s:53:"doc/powermail_graphics/icon_tx_powermail_mails__h.gif";s:4:"d40c";s:37:"doc/powermail_graphics/moduleicon.gif";s:4:"93ca";s:34:"doc/powermail_graphics/pm_logo.gif";s:4:"02dd";s:34:"doc/powermail_graphics/pm_logo.psd";s:4:"dabe";s:35:"doc/realurl/example_realurlpart.php";s:4:"f00b";s:16:"img/icon_csv.gif";s:4:"ddf9";s:18:"img/icon_table.gif";s:4:"cb96";s:16:"img/icon_xls.gif";s:4:"f031";s:23:"js/checkbox/checkbox.js";s:4:"4e13";s:29:"js/mandatoryjs/fabtabulous.js";s:4:"b727";s:28:"js/mandatoryjs/validation.js";s:4:"55c0";s:31:"js/mandatoryjs/lib/prototype.js";s:4:"a553";s:29:"js/mandatoryjs/src/builder.js";s:4:"5d00";s:30:"js/mandatoryjs/src/controls.js";s:4:"dfd8";s:30:"js/mandatoryjs/src/dragdrop.js";s:4:"8c2c";s:29:"js/mandatoryjs/src/effects.js";s:4:"0d8f";s:35:"js/mandatoryjs/src/scriptaculous.js";s:4:"b4e1";s:28:"js/mandatoryjs/src/slider.js";s:4:"7df7";s:30:"js/mandatoryjs/src/unittest.js";s:4:"cbdd";s:33:"lang/locallang_csh_tt_content.php";s:4:"c8f1";s:13:"lib/basket.js";s:4:"059f";s:29:"lib/class.tx_powermail_db.php";s:4:"7acb";s:41:"lib/class.tx_powermail_dynamicmarkers.php";s:4:"ab27";s:40:"lib/class.tx_powermail_functions_div.php";s:4:"b500";s:34:"lib/class.tx_powermail_markers.php";s:4:"99bc";s:36:"lib/class.tx_powermail_removexss.php";s:4:"00ef";s:35:"lib/class.tx_powermail_sessions.php";s:4:"a0d8";s:49:"lib/class.user_powermail_tx_powermail_example.php";s:4:"d39f";s:57:"lib/class.user_powermail_tx_powermail_fields_fe_field.php";s:4:"9d1b";s:53:"lib/class.user_powermail_tx_powermail_fields_type.php";s:4:"9aee";s:56:"lib/class.user_powermail_tx_powermail_fieldsetchoose.php";s:4:"8017";s:55:"lib/class.user_powermail_tx_powermail_forms_preview.php";s:4:"0db1";s:56:"lib/class.user_powermail_tx_powermail_forms_recip_id.php";s:4:"57b1";s:59:"lib/class.user_powermail_tx_powermail_forms_recip_table.php";s:4:"46aa";s:60:"lib/class.user_powermail_tx_powermail_forms_sender_field.php";s:4:"a530";s:45:"lib/class.user_powermail_tx_powermail_uid.php";s:4:"a7ac";s:28:"lib/def/def_field_button.xml";s:4:"da47";s:29:"lib/def/def_field_captcha.xml";s:4:"77f3";s:27:"lib/def/def_field_check.xml";s:4:"cc05";s:29:"lib/def/def_field_content.xml";s:4:"68d7";s:35:"lib/def/def_field_countryselect.xml";s:4:"7e09";s:41:"lib/def/def_field_countryselect_error.xml";s:4:"5185";s:26:"lib/def/def_field_date.xml";s:4:"65b4";s:43:"lib/def/def_field_date2calversion_error.xml";s:4:"f41d";s:32:"lib/def/def_field_date_error.xml";s:4:"0501";s:30:"lib/def/def_field_datetime.xml";s:4:"f6b4";s:27:"lib/def/def_field_error.xml";s:4:"ee33";s:26:"lib/def/def_field_file.xml";s:4:"24d3";s:28:"lib/def/def_field_hidden.xml";s:4:"ec09";s:26:"lib/def/def_field_html.xml";s:4:"0833";s:27:"lib/def/def_field_label.xml";s:4:"b25b";s:30:"lib/def/def_field_password.xml";s:4:"ba2b";s:27:"lib/def/def_field_radio.xml";s:4:"dcb0";s:27:"lib/def/def_field_reset.xml";s:4:"7e3e";s:28:"lib/def/def_field_select.xml";s:4:"c621";s:28:"lib/def/def_field_submit.xml";s:4:"2b8a";s:35:"lib/def/def_field_submitgraphic.xml";s:4:"a010";s:26:"lib/def/def_field_text.xml";s:4:"886f";s:30:"lib/def/def_field_textarea.xml";s:4:"765a";s:26:"lib/def/def_field_time.xml";s:4:"b684";s:32:"lib/def/def_field_typoscript.xml";s:4:"1bc3";s:35:"lib/example/examplefield_button.gif";s:4:"10e0";s:36:"lib/example/examplefield_captcha.gif";s:4:"1cc9";s:34:"lib/example/examplefield_check.gif";s:4:"32a9";s:36:"lib/example/examplefield_content.gif";s:4:"8f01";s:42:"lib/example/examplefield_countryselect.gif";s:4:"54d0";s:33:"lib/example/examplefield_date.gif";s:4:"b589";s:37:"lib/example/examplefield_datetime.gif";s:4:"a8b3";s:33:"lib/example/examplefield_file.gif";s:4:"9a1b";s:33:"lib/example/examplefield_html.gif";s:4:"6f6b";s:34:"lib/example/examplefield_label.gif";s:4:"9658";s:37:"lib/example/examplefield_password.gif";s:4:"a16d";s:34:"lib/example/examplefield_radio.gif";s:4:"8abe";s:34:"lib/example/examplefield_reset.gif";s:4:"395c";s:35:"lib/example/examplefield_select.gif";s:4:"fae2";s:35:"lib/example/examplefield_submit.gif";s:4:"522e";s:42:"lib/example/examplefield_submitgraphic.gif";s:4:"d39c";s:33:"lib/example/examplefield_text.gif";s:4:"3279";s:37:"lib/example/examplefield_textarea.gif";s:4:"eecb";s:33:"lib/example/examplefield_time.gif";s:4:"a8b3";s:39:"lib/example/examplefield_typoscript.gif";s:4:"77dd";s:37:"mod1/class.tx_powermail_bedetails.php";s:4:"b201";s:34:"mod1/class.tx_powermail_belist.php";s:4:"7634";s:34:"mod1/class.tx_powermail_export.php";s:4:"e980";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"838c";s:14:"mod1/index.php";s:4:"dfd7";s:18:"mod1/locallang.xml";s:4:"b2b7";s:22:"mod1/locallang_mod.xml";s:4:"a5f3";s:19:"mod1/moduleicon.gif";s:4:"93ca";s:20:"pi1/JSvalidation.php";s:4:"3588";s:14:"pi1/ce_wiz.gif";s:4:"3e0f";s:39:"pi1/class.tx_powermail_confirmation.php";s:4:"fe4c";s:31:"pi1/class.tx_powermail_form.php";s:4:"07b1";s:31:"pi1/class.tx_powermail_html.php";s:4:"1a33";s:36:"pi1/class.tx_powermail_mandatory.php";s:4:"3ee0";s:30:"pi1/class.tx_powermail_pi1.php";s:4:"6c94";s:38:"pi1/class.tx_powermail_pi1_wizicon.php";s:4:"133c";s:33:"pi1/class.tx_powermail_submit.php";s:4:"7b7c";s:17:"pi1/locallang.xml";s:4:"42ee";s:23:"templates/tmpl_all.html";s:4:"d1e2";s:32:"templates/tmpl_confirmation.html";s:4:"e047";s:26:"templates/tmpl_emails.html";s:4:"658e";s:29:"templates/tmpl_fieldwrap.html";s:4:"4b5d";s:28:"templates/tmpl_formwrap.html";s:4:"872f";s:29:"templates/tmpl_mandatory.html";s:4:"c6d8";s:30:"templates/tmpl_multiplejs.html";s:4:"f624";s:23:"templates/tmpl_thx.html";s:4:"7d67";}',
);

?>