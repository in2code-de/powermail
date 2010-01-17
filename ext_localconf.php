<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');
if (TYPO3_MODE == 'BE') {
	include_once(t3lib_extMgm::extPath('powermail') . 'lib/class.user_powermail_tx_powermail_fieldsetchoose.php');
}
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']); // Get backandconfig
include_once(t3lib_extMgm::extPath('powermail') . 'lib/user_powermailOnCurrentPage.php'); // Conditions for JS including
include_once(t3lib_extMgm::extPath('powermail') . 'lib/user_powermail_misc.php'); // Some powermail userFunc (Conditions if any further step)

t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_powermail_fieldsets=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_powermail_fields=1');
t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_powermail_pi1.php','_pi1','CType',0);

$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_powermail_fieldsets'][0] = array(
	'fList' => 'uid,title',
	'icon' => TRUE,
);
$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_powermail_fields'][0] = array(
	'fList' => 'uid,title,name,type,fieldset',
	'icon' => TRUE,
);



// Set realurlconf for type = 3131 (needed to get a dynamic JavaScript for formcheck)
if (t3lib_extMgm::isLoaded('realurl',0) && $confArr['disablePMRealUrlConfig'] != 1) { // only if realurl is loaded and automatic configuration should be activated
	
	// $TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']
	if (isset($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'])) { // only if array is already set in localconf.php
		$i=0; $set=0; // init counter and flag
		if (isset($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars']) && is_array($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'])) { // if preVars already set in realurl conf
			foreach ($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'] as $key => $value) { // one loop for every preVar
				if ($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][$i]['GETvar'] == 'type') { // if current preVar == type
					$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][$i]['valueMap']['validation'] = '3131'; // add validation => 3131
					$set = 1; // validation alreade added - so flag = 1
				}
				$i++; // increase loop counter
			}
		}
		if ($set==0) { // if flag == 0 (valdiation => 3131 not set) add complete type array
			$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][] = array ( // add complete type array
				'GETvar' => 'type',
				'valueMap' => array (
					'validation' => '3131'
				),
				'noMatch' => 'bypass'
			);
		}
	} else { // set preVars for realurl
		$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][] = array ( // add complete type array
        	'GETvar' => 'type',
        	'valueMap' => array (
            	'validation' => '3131'
        	),
        	'noMatch' => 'bypass'
        );
	}
	
	
	// $TYPO3_CONF_VARS['EXTCONF']['realurl']['www.currentURL.com']
    if (isset($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']])) { // only if array is already set in localconf.php
        $i=0; $set=0; // init counter and flag
        if (isset($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars']) && is_array($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars'])) { // if preVars already set in realurl conf
            foreach ($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars'] as $key => $value) { // one loop for every preVar
                if ($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars'][$i]['GETvar'] == 'type') { // if current preVar == type
                    $TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['preVars'][$i]['valueMap']['validation'] = '3131'; // add validation => 3131
                    $set = 1; // validation alreade added - so flag = 1
                }
                $i++; // increase loop counter
            }
        }
        if ($set==0) { // if flag == 0 (valdiation => 3131 not set) add complete type array
           
			// Bugfix 2008-04-10 for special realurlconf
			if (!is_array($TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']])) {
				$key = $TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']];
			} else {
				$key = $_SERVER['HTTP_HOST'];
			}

            $TYPO3_CONF_VARS['EXTCONF']['realurl'][$key]['preVars'][] = array ( // add complete type array
                'GETvar' => 'type',
                'valueMap' => array (
                    'validation' => '3131'
                ),
                'noMatch' => 'bypass'
            );
        }
    }
	
}

?>