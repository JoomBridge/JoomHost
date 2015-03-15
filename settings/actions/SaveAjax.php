<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Joomlahosts_SaveAjax_Action extends Settings_Vtiger_IndexAjax_View {

	public function process(Vtiger_Request $request) {
		global $log;
		$log->debug("ENTERING --> Settings_Joomlahosts_SaveAjax_Action::process() ");
		
		$recordId = $request->get('record');
		$JHost_no = $request->get('host_no');
		$presence = $request->get('presence');
		$qualifiedModuleName = $request->getModule(false);
//		$alldata = $request->getAll();
	
		$response = new Vtiger_Response();
		try {
			Settings_Joomlahosts_Record_Model::save($presence, $JHost_no);
			$response->setResult(array(true, vtranslate('JS_CONFIGURATION_SAVED', $qualifiedModuleName)));
		} catch (Exception $e) {
			$response->setError($e->getMessage());
		}
		$response->emit();
	}

}