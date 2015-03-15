<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * File version 1.1.2. at 2014-10-02
 *************************************************************************************/

class Joomlahosts_Record_Model extends Vtiger_Record_Model {

	/**
	 * Function to check SQL host connection
	 * @return true or false in the case of invalid connection
	 */	
	public static function CheckJoomlaSQL($data, $HostId) {
		global $log, $adb;
		$log->debug("ENTERING --> Joomlahosts_Record_Model::CheckJoomlaSQL( Hostdata, $HostId)");
		
		if ($data) {
			// params to call: $dbtype, $host, $dbname, $username, $passwd
			try {
				$JoomlaDB = new PearDatabase(
						$data['joomlahost_dbtype'],
						$data['j_host'],
						$data['j_dbname'],
						$data['j_dbuser'],
						$data['j_dbpassword']			
					);
			} catch (Exception $e) {
				$log->fatal('@@@ Check JoomlaDB connection failed: '.print_r($e->getMessage(), true) );
				return false;
			}
			
		} else {
			$log->fatal('@@@ Check JoomlaDB connection failed: '.print_r($data, true) );
			return false;
		}
		$jtables = $JoomlaDB->get_tables();
		
		if ($jtables) {
			//get the user groups (user_leveles) data if it does not exist
			$JUserLTablename = $data['j_dbprefix'].'usergroups';
			if ( in_array( strtolower($JUserLTablename), $jtables) ) {
				$UserLevels = array();
				
				//get user Levels definition data
				try {
					$jresult = $JoomlaDB->pquery("SELECT id, title FROM $JUserLTablename ORDER BY id ASC", array(), true );
				} catch (Exception $e) {
					$log->fatal("@@@ Check JoomlaDB connection failed at query: $JUserLTablename ".print_r($e->getMessage(), true) );
				}
				
				if ( $JoomlaDB->num_rows($jresult) ) {
					while ( $row = $JoomlaDB->fetch_array($jresult) ) { 
						$UserLevels[$row['id']] = $row['title'];
					}
					
					//Check the vtiger_joomla_userlevels table related to the JoomlaHost if it exists	
					$VT_UserLTablename = 'vtiger_joomla_userlevels_'.strtolower($data['host_no']);
					
					if ( !Vtiger_Utils::CheckTable($VT_UserLTablename) ) {
						Vtiger_Utils::CreateTable(
							$VT_UserLTablename, 
								'(joomla_userlevelsid int(11) NOT NULL AUTO_INCREMENT, '.	//1
								'joomla_userlevels varchar(200) NOT NULL, '.				//2
								'presence int(1) NOT NULL DEFAULT 1, '.						//3
								'sortorderid int(11) NOT NULL DEFAULT 0, '.					//4
							'primary key (joomla_userlevelsid))');
					}					
					//Check the initial data in the table
					if ( Vtiger_Utils::CheckTable($VT_UserLTablename) ) {
						//Check the table rows if exists
						$r = $adb->pquery("SELECT * FROM $VT_UserLTablename WHERE joomla_userlevelsid = 1;", array());
						if ( !$adb->num_rows($r) ) { 
						
							// Insert the first - initial JUser levels data - if the table is empty
							$sort = 1;
							foreach ( $UserLevels as $id => $title) {
								$presence = 1;
								if( $id == 6 || $id == 7 || $id == 8 || $title == 'Guest') { $presence = 0;} //disable admins from the picklist
								$newquery = "INSERT INTO $VT_UserLTablename (joomla_userlevelsid, joomla_userlevels, presence, sortorderid) VALUES (".$id.", '".$title."', ".$presence.", ".$sort.");";
								Vtiger_Utils::ExecuteQuery($newquery);
								$sort++;
							}
							$log->fatal("$VT_UserLTablename Table added with data to the database");
						} else { 
							//remove the old data
							$tquery = "TRUNCATE TABLE $VT_UserLTablename;";
							Vtiger_Utils::ExecuteQuery($tquery);
							
							// Insert the new JUser levels data - after making table empty
							$sort = 1;
							foreach ( $UserLevels as $id => $title) {
								$presence = 1;
								if( $id == 6 || $id == 7 || $id == 8 || $title == 'Guest') { $presence = 0;} //disable admins from the picklist
								$newquery = "INSERT INTO $VT_UserLTablename (joomla_userlevelsid, joomla_userlevels, presence, sortorderid) VALUES (".$id.", '".$title."', ".$presence.", ".$sort.");";
								Vtiger_Utils::ExecuteQuery($newquery);
								$sort++;
							}
							$log->fatal("$VT_UserLTablename Table added with data to the database");
						
						}
					}		
				}			
		
			} else {
				//To-Do: set error_message: wrong database prefix    
				return false;
			}
			
		//Check Hikashop install
			$HikashopTablename = $data['j_dbprefix'].'hikashop_config';
			if ( in_array( strtolower($HikashopTablename), $jtables) ) {
				//Hikashop component installed, set the flag
				$adb->pquery("UPDATE vtiger_joomlahosts SET hikashop = 1 WHERE joomlahostsid = ?", array( $HostId ) );
				
				$configarr = array();
				//get major Hikashop config data
				try {
					$hresult = $JoomlaDB->pquery("SELECT config_namekey, config_value FROM $HikashopTablename WHERE config_namekey = 'level'", array(), true );
				} catch (Exception $e) {
					$log->fatal("@@@ Check JoomlaDB connection failed at query: $HikashopTablename ".print_r($e->getMessage(), true) );
				}
				if ( $JoomlaDB->num_rows($hresult) ) {
					while ( $row = $JoomlaDB->fetch_array($hresult) ) { 
						$configarr[] = $row['config_value'];
					}
				}
				try {
					$hresult = $JoomlaDB->pquery("SELECT config_namekey, config_value FROM $HikashopTablename WHERE config_namekey = 'version'", array(), true );
				} catch (Exception $e) {
					$log->fatal("@@@ Check JoomlaDB connection failed at query: $HikashopTablename ".print_r($e->getMessage(), true) );
				}
				if ( $JoomlaDB->num_rows($hresult) ) {
					while ( $row = $JoomlaDB->fetch_array($hresult) ) { 
						$configarr[] = $row['config_value'];
					}
				}
				if( !empty($configarr) ) {
					$configstr = implode(' |##| ', $configarr);
					$adb->pquery("UPDATE vtiger_joomlahosts SET hikashop_data = ? WHERE joomlahostsid = ?", array( $configstr, $HostId ) );
				}
			} else {
				//Hikashop component not installed, unset the flag
				$adb->pquery("UPDATE vtiger_joomlahosts SET hikashop = 0 WHERE joomlahostsid = ?", array( $HostId ) );
				$adb->pquery("UPDATE vtiger_joomlahosts SET hikashop_data = '' WHERE joomlahostsid = ?", array( $$HostId ) );
			}
			
		//Check Acymailing install
			$AcymailingTablename = $data['j_dbprefix'].'acymailing_config';
			if ( in_array( strtolower($AcymailingTablename), $jtables) ) {
				//Acymailing component installed, set the flag
				$adb->pquery("UPDATE vtiger_joomlahosts SET acymailing = 1 WHERE joomlahostsid = ?", array( $HostId ) );
				
				$configarr = array();
				//get major Acymailing config data
				try {
					$hresult = $JoomlaDB->pquery("SELECT namekey, value FROM $AcymailingTablename WHERE namekey = 'level'", array(), true );
				} catch (Exception $e) {
					$log->fatal("@@@ Check JoomlaDB connection failed at query: $AcymailingTablename ".print_r($e->getMessage(), true) );
				}
				if ( $JoomlaDB->num_rows($hresult) ) {
					while ( $row = $JoomlaDB->fetch_array($hresult) ) { 
						$configarr[] = $row['value'];
					}
				}
				try {
					$hresult = $JoomlaDB->pquery("SELECT namekey, value FROM $AcymailingTablename WHERE namekey = 'version'", array(), true );
				} catch (Exception $e) {
					$log->fatal("@@@ Check JoomlaDB connection failed at query: $AcymailingTablename ".print_r($e->getMessage(), true) );
				}
				if ( $JoomlaDB->num_rows($hresult) ) {
					while ( $row = $JoomlaDB->fetch_array($hresult) ) { 
						$configarr[] = $row['value'];
					}
				}
				if( !empty($configarr) ) {
					$configstr = implode(' |##| ', $configarr);
					$adb->pquery("UPDATE vtiger_joomlahosts SET acymailing_data = ? WHERE joomlahostsid = ?", array( $configstr, $HostId ) );
				}
			} else {
				//Acymailing component not installed, unset the flag
				$adb->pquery("UPDATE vtiger_joomlahosts SET acymailing = 0 WHERE joomlahostsid = ?", array( $HostId ) );
				$adb->pquery("UPDATE vtiger_joomlahosts SET acymailing_data = '' WHERE joomlahostsid = ?", array( $HostId ) );
			}
			$JoomlaDB->disconnect();
			return true;
			
		} else {
			return false;
		}
	}

	/**
	 * Function to get value checked by $HostId
	 * @return <mixed> the value of field checked
	 */	
	public static function toDisplaySQLCheck($HostId) {
		global $log, $adb;
		$log->debug('ENTERING --> Joomlahosts_Record_Model::toDisplaySQLCheck()');
		
		$instances = array();
		$maps = $adb->pquery("SELECT checked FROM vtiger_joomlahosts WHERE joomlahostsid = ?", array($HostId));
		if ($adb->num_rows($maps)) {
			while ($row = $adb->fetch_array($maps)) {
				$checked = $row['checked'];
			}
			
			return $checked;
		} else {
			return false;
		}
	}

	/**
	 * Function to set value checked by $HostId (= record_id)
	 * @param $data (array) Host access data
	 * @param $HostId (integer) record_id in normal format
	 * @return -- O or 1
	 */	
	public static function SetSQLCheck($data, $HostId) {
		global $log, $adb;
		$log->debug('ENTERING --> Joomlahosts_Record_Model::SetSQLCheck()');
		if ( Joomlahosts_Record_Model::CheckJoomlaSQL($data, $HostId) ) {
			//Connection OK
			$log->debug( 'RUNING --> Joomlahosts_Record_Model::SetSQLCheck() : OK' );
			$adb->pquery("UPDATE vtiger_joomlahosts SET checked = 1 WHERE joomlahostsid = ?", array($HostId));
			return 1;
		
		} else {
			//Bad connection
			$log->debug( 'RUNING --> Joomlahosts_Record_Model::SetSQLCheck() : BAD connection' );
			$adb->pquery("UPDATE vtiger_joomlahosts SET checked = 0 WHERE joomlahostsid = ?", array($HostId));
			return 0;
		}
	}

}