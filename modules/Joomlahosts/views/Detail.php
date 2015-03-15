<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Joomlahosts_Detail_View extends Vtiger_Detail_View {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Function shows the entire detail for the record
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
		$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();

        $moduleModel = $recordModel->getModule();
		
		$checked = Joomlahosts_Record_Model::toDisplaySQLCheck($recordId);

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('CHECKED', $checked);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

		return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
	}
	
	function showModuleSummaryView($request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);

        $moduleModel = $recordModel->getModule();
		
		$checked = Joomlahosts_Record_Model::toDisplaySQLCheck($recordId);
		
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('CHECKED', $checked);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
		$viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));

		return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
	}
}