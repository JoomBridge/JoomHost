<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Joomlahosts_Module_Model extends Settings_Vtiger_Module_Model {

	var $baseTable = 'vtiger_joomla_userlevels_jhost1';
	var $baseIndex = 'joomlahostsid';

	/**
	 * joomlahostsid
	 * iname
	 * host_no
	 * picklisttable
	 */

	var $listFields = array(
			'joomlahostsid' 	=> 'LBL_JOOMLA_HOST_ID'	,		//Joomla Host ID
			'iname' 			=> 'LBL_INSTANCE_NAME'	, 		//Instance name
			'host_no' 			=> 'LBL_HOST_NO'		, 		//Host No.
			'picklisttable' 	=> 'LBL_EDIT_TABLE'		,		//JUser Leveles picklist items to edit
		);
	var $nameFields = array('');
	var $name = 'Joomlahosts';
	
	var $PicklistTitle = array(
			'joomla_userlevelsid' 	=> 'LBL_ID', 				//ID
			'joomla_userlevels' 	=> 'LBL_LEVEL', 			//JUser Level name
			'presence' 				=> 'LBL_PRESENCE', 			//Presence
			'sortorderid' 			=> 'LBL_SORT',				//Sorter ID
		);
	/**
	 * Function to get editable fields from this module
	 * @return <Array> list of editable fields
	 */
	public function getEditableFields() {
		$fieldsList = array(
			array('name' => 'presence',			'label' => 'Presence',			'type' => 'radio'),
		);

		$fieldModelsList = array();
		foreach ($fieldsList as $fieldInfo) {
			$fieldModelsList[$fieldInfo['name']] = Settings_Joomlahosts_Field_Model::getInstanceByRow($fieldInfo);
		}
		return $fieldModelsList;
	}

	/**
	 * Function to get List view url
	 * @return <String> Url
	 */
	public function getListViewUrl() {
		return "index.php?module=".$this->getName()."&parent=".$this->getParentName()."&view=List";
	}

}