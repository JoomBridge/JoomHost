<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class JoomlahostsHandler extends VTEventHandler {

	function handleEvent($eventName, $entityData) {
/*		
        $moduleName = $entityData->getModuleName();

        // Validate the event target
        if ($moduleName != 'Joomlahosts') {
            return;
        }

        //Get Current User Information
        global $current_user, $currentModule;
		global $log, $adb;		
		
		if($eventName == 'vtiger.entity.beforesave') {
			// Entity is about to be saved, take required action

			//Check the SQL connection validity

			$HostId = $entityData->getId();
			$data = $entityData->getData();
			$log->debug("Joomlahosts EVENTHandler --- vtiger.entity.beforesave HOST_ID : $HostId, DATA : ".print_r($data, true));

			if ( Joomlahosts_Record_Model::CheckJoomlaSQL($data, $HostId) ) {
				//Connection OK
				$log->debug( 'Joomlahosts EVENTHandler --- Joomlahosts_Record_Model::CheckJoomlaSQL($data) : OK' );
				$adb->pquery("UPDATE vtiger_joomlahosts SET checked = 1 WHERE joomlahostsid = ?", array($HostId));
			
			} else {
				//Bad connection
				$log->debug( 'Joomlahosts EVENTHandler --- Joomlahosts_Record_Model::CheckJoomlaSQL($data) : BAD connection' );
				$adb->pquery("UPDATE vtiger_joomlahosts SET checked = 0 WHERE joomlahostsid = ?", array($HostId));
				
			}


		}
		if($eventName == 'vtiger.entity.aftersave') {
			// Entity has been saved, take next action
		}
*/
	}
}
?>