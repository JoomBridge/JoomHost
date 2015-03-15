<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class Settings_Joomlahosts_Index_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
		global $log;
		$log->debug('ENTERING --> Settings_Joomlahosts_Index_View::process() ');
		
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);

		$listview_entries = Settings_Joomlahosts_Record_Model::getAll($qualifiedModuleName);
		
		$ModuleModel = new Settings_Joomlahosts_Module_Model;
		$listview_headers = $ModuleModel->listFields;
		
		$viewer->assign('LISTVIEW_HEADERS', $listview_headers);
		$viewer->assign('LISTVIEW_ENTRIES', $listview_entries);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('QUALIFIED_MODULE_NAME', $qualifiedModuleName);

		echo $viewer->view('IndexContent.tpl', $qualifiedModuleName,true);
	}

}