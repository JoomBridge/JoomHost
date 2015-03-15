<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Joomlahosts_Record_Model extends Settings_Vtiger_Record_Model {

	const tableName = 'vtiger_joomlahosts';
	/**
	 * Function to get Id of this record instance
	 * @return <Integer> Id
	 */
	public function getId() {
		return $this->get('joomlahostsid');
	}

	/**
	 * Function to get Name of this record instance
	 * @return <String> Name
	 */
	public function getName() {
		return '';
	}
	
	/**
	 * Function to get Name of this record instance
	 * @return <String> Name
	 */
	public function getJHost() {
		return strtolower($this->get('host_no'));
	}

	/**
	 * Function to get module of this record instance
	 * @return <Settings_Webforms_Module_Model> $moduleModel
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * Function to set module instance to this record instance
	 * @param <Settings_Webforms_Module_Model> $moduleModel
	 * @return <Settings_Webforms_Record_Model> this record
	 */
	public function setModule($moduleModel) {
		$this->module = $moduleModel;
		return $this;
	}

	/**
	 * Function to get Edit view url
	 * @return <String> Url
	 */
	public function getEditViewUrl() {
		$moduleModel = $this->getModule();
		return 'index.php?module='.$moduleModel->getName().'&parent='.$moduleModel->getParentName().'&view=Edit&record='.$this->getId().'&hostno='.$this->getJHost();
	}
	
	/**
	 * Function to get record links
	 * @return <Array> list of link models <Vtiger_Link_Model>
	 */
	public function getRecordLinks() {
		$links = array();
		$recordLinks = array(
				array(
						'linktype' => 'LISTVIEWRECORD',
						'linklabel' => 'LBL_EDIT',
						'linkurl' => "javascript:Settings_Joomlahosts_Index_Js.triggerEdit(event, '".$this->getEditViewUrl()."');",
						'linkicon' => 'icon-pencil'
				),
		);
		foreach($recordLinks as $recordLink) {
			$links[] = Vtiger_Link_Model::getInstanceFromValues($recordLink);
		}
		return $links;
	}

	/**
	 * Function to getDisplay value of every field
	 * @param <String> field name
	 * @return <String> field value
	 */
	public function getDisplayValue($key) {
		$value = $this->get($key);
		if ($key === 'presence') {
			if ($value) {
				$value = 'Yes';
			} else {
				$value = 'No';
			}
		}
		return $value;
	}

	/**
	 * Function to get Editable fields for this instance
	 * @return <Array> field models list <Settings_Joomlabridge_Field_Model>
	 */
	public function getEditableFields() {
		$editableFieldsList = $this->getModule()->getEditableFields();
		return $editableFieldsList;
	}

	/**
	 * Function to save the record/table
	 */
	public function save($params, $host_no) {
		global $log;		
		$log->debug("ENTERING --> Settings_Joomlahosts_Record_Model::save() HOST: $host_no " );
		
		$db = PearDatabase::getInstance();
	
		$tablename = 'vtiger_joomla_userlevels_'.strtolower($host_no);
	
		foreach ($params as $hid => $presence) {
			$db->pquery("UPDATE $tablename SET presence = ? WHERE joomla_userlevelsid = ?", array($presence, $hid));
		}
	}
	
	/**
	 * Function to save the record/table
	 */
	public function getJUserLevelData($host_no) {
		global $log;
		$log->debug("ENTERING --> Settings_Joomlahosts_Record_Model::getJUserLevelData( $host_no ) ");
		
		$db = PearDatabase::getInstance();
		
		$tablename = 'vtiger_joomla_userlevels_'.strtolower($host_no);
		$result = $db->pquery("SELECT * FROM $tablename", array());
		
		if ($db->num_rows($result)) {
			$JUserLevelData = array();
			for($i=0; $i<$db->num_rows($result); $i++) {
				$JUserLevelData[] = $db->query_result_rowdata($result, $i);
			}
			return $JUserLevelData;
		} else {
			return false;
		}
	}
	
	/**
	 * Function to get record instance by using id and moduleName
	 * @param <Integer> $recordId
	 * @param <String> $qualifiedModuleName
	 * @return <Settings_Joomlabridge_Record_Model> RecordModel
	 */
	static public function getInstanceById($recordId, $qualifiedModuleName) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT joomlahostsid, iname, host_no FROM '.self::tableName.' WHERE joomlahostsid = ?', array($recordId));

		if ($db->num_rows($result)) {
			$moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
			$rowData = $db->query_result_rowdata($result, 0);

			$recordModel = new self();
			$recordModel->setData($rowData)->setModule($moduleModel);

			return $recordModel;
		}
		return false;
	}

	/**
	 * Function to get clean record instance by using moduleName
	 * @param <String> $qualifiedModuleName
	 * @return <Settings_Joomlabridge_Record_Model>
	 */
	static public function getCleanInstance($qualifiedModuleName) {
		$recordModel = new self();
		$moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
		return $recordModel->setModule($moduleModel);
	}


	/**
	 * Function returns all the Joomla Host Models
	 * @return <Array of Settings_Joomlahosts_Module_Model>
	 */
	function getAll() {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT joomlahostsid, iname, host_no FROM '.self::tableName.' WHERE enabled = 1', array());
		if ($db->num_rows($result)) {
			$JHosts = array();
			for($i=0; $i<$db->num_rows($result); $i++) {
				$JHost = new self();
				$JHost->setData($db->query_result_rowdata($result, $i));
				$JHosts[] = $JHost;
			}
			return $JHosts;
		} else {
			return false;
		}
	}
		
}