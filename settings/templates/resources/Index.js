/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Settings_Vtiger_Index_Js("Settings_Joomlahosts_Index_Js",{
	
	/**
	 * Function to trigger edit and add new configuration for Joomla SQL hosts
	 */
	triggerEdit : function(event, url) {
		event.stopPropagation();
		var instance = new window["Settings_Joomlahosts_Index_Js"]();
		instance.EditRecord(url);
	},
	
},{
	/**
	 * Function to show the Joomla User Level picklist data for edit presence
	 */
	EditRecord : function(url) {
		var thisInstance = this;
		AppConnector.request(url).then(
			function(data) {
				
				var callBackFunction = function(data) {
					var form = jQuery('#jUserLConfig');
									
					var params = app.getvalidationEngineOptions(true);
					params.onValidationComplete = function(form, valid){
						if(valid) {
							thisInstance.saveConfiguration(form).then(
								function(data) {
									if(data['success']) {
										var params = {};
										params['text'] = app.vtranslate('JS_CONFIGURATION_SAVED');
										Settings_Vtiger_Index_Js.showMessage(params);
										thisInstance.getBasicView();
									}
								},
								function(error, err) {

								}
							);
						}
						//To prevent form submit
						return false;
					}
					form.validationEngine(params);
					
				}
				
				app.showModalWindow(data,function(data) {
					if(typeof callBackFunction == 'function') {
						callBackFunction(data);
					}
				});
			},
			function(error,err){
			}
		);
	},
	
	/**
	 * Function to save the Joomla SQL host Configuration Details from edit and Add new configuration 
	 */
	saveConfiguration : function(form) {
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		var progressIndicatorElement = jQuery.progressIndicator({
			'position' : 'html',
			'blockInfo' : {
				'enabled' : true
			}
		});
		
		var params = form.serializeFormData();
		params['module'] = "Joomlahosts";
		params['parent'] = "Settings";
		params['action'] = "SaveAjax";
			
		AppConnector.request(params).then(
			function(data) {	
				progressIndicatorElement.progressIndicator({'mode' : 'hide'});
				aDeferred.resolve(data);
			},
			function(error) {
				progressIndicatorElement.progressIndicator({'mode' : 'hide'});
				aDeferred.reject(error);
			}
		);
		return aDeferred.promise();
	},
	
	/*
	 * Function which will give you all the list view params
	 */
	getBasicView : function(urlParams) {
		if(typeof urlParams == 'undefined') {
			urlParams = "index.php?module=Joomlahosts&parent=Settings&view=Index";
		}
		return window.location.href = urlParams;
	},
	
	
	/**
	 * Function to register all the events
	 */
	registerEvents : function() {
		//nothing to register now
	}
})