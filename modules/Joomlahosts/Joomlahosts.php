<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'modules/Vtiger/CRMEntity.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'vtlib/Vtiger/Event.php';

class Joomlahosts extends CRMEntity {
	var $log;
	var $db;
	
	/**
	 * Base module table and index.
	 */
	var $table_name = 'vtiger_joomlahosts';
	var $table_index= 'joomlahostsid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_joomlahostscf', 'joomlahostsid');
	var $related_tables = Array('vtiger_joomlahostscf' => array('joomlahostsid', 'vtiger_joomlahosts', 'joomlahostsid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_joomlahosts', 'vtiger_joomlahostscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
			'vtiger_crmentity' => 'crmid',
			'vtiger_joomlahosts' => 'joomlahostsid',
			'vtiger_joomlahostscf'=>'joomlahostsid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
			/* Format: Field Label => Array(tablename, columnname) */
			// tablename should not have prefix 'vtiger_'
			'Instance Name' 		=> Array('joomlahosts', 'iname')			,
			'Host No' 				=> Array('joomlahosts', 'host_no')			,
			'Joomla DB Type' 		=> Array('joomlahosts', 'joomlahost_dbtype'),
			'Joomla DB Host' 		=> Array('joomlahosts', 'j_host')			,
			'Joomla DB Name' 		=> Array('joomlahosts', 'j_dbname')			,
			'Joomla DB User' 		=> Array('joomlahosts', 'j_dbuser')			,
			'Joomla DB Prefix' 		=> Array('joomlahosts', 'j_dbprefix')		,
			'Enabled' 				=> Array('joomlahosts', 'enabled')			,
			'Description' 			=> Array('joomlahosts', 'description')		,
	);
	var $list_fields_name = Array (
			/* Format: Field Label => fieldname */
			'Instance Name' 		=> 'iname'				,
			'Host No' 				=> 'host_no'			,
			'Joomla DB Type' 		=> 'joomlahost_dbtype'	,
			'Joomla DB Host' 		=> 'j_host'				,
			'Joomla DB Name' 		=> 'j_dbname'			,
			'Joomla DB User' 		=> 'j_dbuser'			,
			'Joomla DB Prefix' 		=> 'j_dbprefix'			,
			'Enabled' 				=> 'enabled'			,
			'Description' 			=> 'description'		,
	);

	// Make the field link to detail view
	var $list_link_field = 'iname';

	// For Popup listview and UI type support
	var $search_fields = Array(
			/* Format: Field Label => Array(tablename, columnname) */
			// tablename should not have prefix 'vtiger_'
			'Instance Name' 	=> Array('joomlahosts', 'iname')				,
			'Host No' 			=> Array('joomlahosts', 'host_no')				,
			'Joomla DB Name' 	=> Array('joomlahosts', 'j_dbname')				,
			'Assigned To' 		=> Array('vtiger_crmentity','assigned_user_id')	,
	);
	var $search_fields_name = Array (
			/* Format: Field Label => fieldname */
			'Instance Name' 	=> 'iname'				,
			'Host No' 			=> 'host_no'			,
			'Joomla DB Name' 	=> 'j_dbname'			,
			'Assigned To' 		=> 'assigned_user_id'	,
	);

	// For Popup window record selection
	var $popup_fields = Array ('iname');

	// For Alphabetical search
	var $def_basicsearch_col = 'iname';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'iname';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('iname', 'joomlahost_dbtype', 'j_host', 'j_dbname', 'j_dbuser', 'j_dbpassword', 'j_dbprefix', 'assigned_user_id','createdtime' ,'modifiedtime');

	var $default_order_by = 'host_no';
	var $default_sort_order='ASC';
	
	function Joomlahosts(){
		$this->log = LoggerManager::getLogger('joomlahosts');  //TODO Check it
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Joomlahosts');
    }

     /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type) {
 		global $adb;
		global $log;
		
		require_once 'vtlib/Vtiger/Module.php';
		require_once 'modules/ModTracker/ModTracker.php';
		
		$JHostsModule = Vtiger_Module::getInstance($modulename);
		$tabid = $JHostsModule->getId();

		if ($event_type == 'module.postinstall') {
			// TODO Handle actions after this module is installed.
			
			//Check the existing module sequence numbering
			$result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
			if (!($adb->num_rows($result))) {
				//Initialize module sequence for the module
				$adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $modulename, 'JHost', 1, 1, 1));
			}
			
			$this->AddEventHandler($JHostsModule);
			$this->AddTableColumn();
//			$this->CorrectBaseTable();		//already supported
			$this->addSettingsLinks();
			
			ModTracker::enableTrackingForModule($tabid);
			
        } else if ($event_type == 'module.disabled') {
			// TODO Handle actions after this module is disabled.
			
			$em = new VTEventsManager($adb);
			$em->setHandlerInActive('Joomlahosts');
			
			$this->removeSettingsLinks();
			
			ModTracker::disableTrackingForModule($tabid);
			
        } else if ($event_type == 'module.enabled') {
			// TODO Handle actions after this module is enabled.
			
			$em = new VTEventsManager($adb);
			$em->setHandlerActive('Joomlahosts');
			
			ModTracker::enableTrackingForModule($tabid);
			
			//Check the existing module sequence numbering
			$result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
			if (!($adb->num_rows($result))) {
				//Initialize module sequence for the module
				$adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $modulename, 'JHost', 1, 1, 1));
			}
			
			//Check the existing EventHandler settings
			$events = $adb->pquery("SELECT module_name FROM vtiger_eventhandler_module WHERE module_name = ?", array($modulename));
			if (!($adb->num_rows($events))) {
				$this->AddEventHandler($JHostsModule);
			}			
			$this->addSettingsLinks();

        } else if ($event_type == 'module.preuninstall') {
			// TODO Handle actions before this module is uninstalled.
			
			$this->removeSettingsLinks();

        } else if ($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.

        } else if ($event_type == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
			
			$this->AddTableColumn();
			
			//Check the existing module sequence numbering
			$result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
			if (!($adb->num_rows($result))) {
				//Initialize module sequence for the module
				$adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $modulename, 'JHost', 1, 1, 1));
			}
			//remove old settings
			$this->removeOldSettingsLinks();
			
			//add new settings
			$this->addSettingsLinks();
			//Check the existing EventHandler settings
			$events = $adb->pquery("SELECT module_name FROM vtiger_eventhandler_module WHERE module_name = ?", array($modulename));
			if (!($adb->num_rows($events))) {
				$this->AddEventHandler($JHostsModule);
			}
			ModTracker::enableTrackingForModule($tabid);
        }
    }

    /** Function to handle module specific operations when saving a entity
	*/
	function save_module($module){
	}
	
	static function registerLinks() {
	}

     /**
     * Add event handler to the module
     * @param Module instance
     */	
	function AddEventHandler($Moduleinstance) {
		global $log;
		require_once 'vtlib/Vtiger/Event.php';
		if ( empty($Moduleinstance) ) {
			$module = new Vtiger_Module();
			$moduleInstance = $module->getInstance('Joomlahosts');		
		}
		$log->debug('ENTERING --> AddEventHandler() method to the Joomlahosts');	
		if(Vtiger_Event::hasSupport()) {
			Vtiger_Event::register(
				$Moduleinstance, 'vtiger.entity.aftersave',
				'JoomlahostsHandler', 'modules/Joomlahosts/JoomlahostsHandler.php'
			);
			Vtiger_Event::register(
				$Moduleinstance, 'vtiger.entity.beforesave',
				'JoomlahostsHandler', 'modules/Joomlahosts/JoomlahostsHandler.php'
			);
			$log->fatal('Joomlahosts Events are added.');
		}
	}
	
    /**
     * To add Integration->Joomlahosts block in Settings page
    */
    function addSettingsLinks(){
        global $log;
        $adb = PearDatabase::getInstance();
        $integrationBlock = $adb->pquery('SELECT * FROM vtiger_settings_blocks WHERE label=?',array('LBL_INTEGRATION'));
        $integrationBlockCount = $adb->num_rows($integrationBlock);
        
        // To add Block
        if($integrationBlockCount > 0){
            $blockid = $adb->query_result($integrationBlock, 0, 'blockid');
        }else{
            $blockid = $adb->getUniqueID('vtiger_settings_blocks');
            $sequenceResult = $adb->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_blocks", array());
            if($adb->num_rows($sequenceResult)) {
                $sequence = $adb->query_result($sequenceResult, 0, 'sequence');
            }
            $adb->pquery("INSERT INTO vtiger_settings_blocks(blockid, label, sequence) VALUES(?,?,?)", array($blockid, 'LBL_INTEGRATION', ++$sequence));
        }
        
        // To add a Field
        $fieldid = $adb->getUniqueID('vtiger_settings_field');
        $adb->pquery("INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active)
            VALUES(?,?,?,?,?,?,?,?)", array($fieldid, $blockid, 'LBL_JUSER_LEVELS', '','LBL_JUSERLEVEL_CONFIGURATION', 'index.php?module=Joomlahosts&parent=Settings&view=Index', 2, 0));
        $log->fatal('Joomlahosts Settings Block and Field added');
    }

    /**
     * To delete Integration->Joomlahost block in Settings page
    */
    function removeSettingsLinks(){
        global $log;
        $adb = PearDatabase::getInstance();
        $adb->pquery('DELETE FROM vtiger_settings_field WHERE name=?', array('LBL_JUSER_LEVELS'));
        $log->fatal('Joomlahosts Settings Field Removed'); 
    }

    /**
     * To delete Integration->Joomlahost links in Settings page at update
    */
    function removeOldSettingsLinks(){
        global $log;
        $adb = PearDatabase::getInstance();
        $adb->pquery("DELETE FROM vtiger_settings_field WHERE linkto LIKE '%Joomlahost%'", array());
        $log->fatal('OLD Joomlahosts Settings Field Removed'); 
    }
	
	function AddTableColumn() {
		global $log;
		require_once 'vtlib/Vtiger/Utils.php';		
		$log->debug('ENTRY TO: AddTableColumn() --- Joomlahosts ');
		Vtiger_Utils::AddColumn( "vtiger_joomlahosts", "checked", "tinyint(3) NOT NULL DEFAULT '0'" );
		//add columns to check installed Joomla components
		Vtiger_Utils::AddColumn( "vtiger_joomlahosts", "hikashop", "tinyint(3) NOT NULL DEFAULT '0'" );
		Vtiger_Utils::AddColumn( "vtiger_joomlahosts", "hikashop_data", "varchar(255) NOT NULL DEFAULT ''" );
		Vtiger_Utils::AddColumn( "vtiger_joomlahosts", "acymailing", "tinyint(3) NOT NULL DEFAULT '0'" );
		Vtiger_Utils::AddColumn( "vtiger_joomlahosts", "acymailing_data", "varchar(255) NOT NULL DEFAULT ''" );
	}
/*	
	function CorrectBaseTable() {
		global $log;
		require_once 'vtlib/Vtiger/Utils.php';		
		$log->debug('ENTRY TO: CorrectBaseTable() --- Joomlahosts ');
		Vtiger_Utils::AlterTable( "vtiger_joomlahosts", "ADD PRIMARY KEY (joomlahostsid)" );
		Vtiger_Utils::AlterTable( "vtiger_joomlahosts", "ADD CONSTRAINT fk_1_vtiger_joomlahosts FOREIGN KEY (joomlahostsid) REFERENCES vtiger_crmentity (crmid) ON DELETE CASCADE" );
		Vtiger_Utils::AlterTable( "vtiger_joomlahostscf", "ADD CONSTRAINT fk_1_vtiger_joomlahostscf FOREIGN KEY (joomlahostsid) REFERENCES vtiger_crmentity (crmid) ON DELETE CASCADE" );
	
	}
*/	
/*  
ALTER TABLE `vtiger_joomlahosts` ADD PRIMARY KEY (`joomlahostsid`);
ALTER TABLE `vtiger_joomlahosts` ADD CONSTRAINT `fk_1_vtiger_joomlahosts` FOREIGN KEY (`joomlahostsid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE;
ALTER TABLE `vtiger_joomlahostscf` ADD CONSTRAINT `fk_1_vtiger_joomlahostscf` FOREIGN KEY (`joomlahostsid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE;
Correct: vtiger_parenttabrel (missing data)
*/

}