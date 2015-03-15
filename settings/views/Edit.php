<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Joomlahosts_Edit_View extends Settings_Vtiger_IndexAjax_View {

	public function process(Vtiger_Request $request) {
		global $log;
		$log->debug("ENTERING --> Settings_Joomlahosts_Edit_View::process() ");	
	
		$recordId = $request->get('record');
		$jhost_no = $request->get('hostno');
		$qualifiedModuleName = $request->getModule(false);
		
		$JUserLevels = Settings_Joomlahosts_Record_Model::getJUserLevelData($jhost_no);
		$ModuleModel = new Settings_Joomlahosts_Module_Model;
		$listview_headers = $ModuleModel->PicklistTitle;

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD_ID', $recordId);
		$viewer->assign('HOSTNO', $jhost_no);
		$viewer->assign('LEVELS', $JUserLevels);
		$viewer->assign('LISTVIEW_HEADERS', $listview_headers);
		$viewer->assign('QUALIFIED_MODULE_NAME', $qualifiedModuleName);

		$viewer->view('Edit.tpl', $qualifiedModuleName);
	}
}