{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
<style type="text/css">
{literal}
.small-jhost {height: 16px;}
{/literal}
</style>
<div class="modal" style="height: auto !important; min-height: 615px !important;">
	<div class="modal-header contentsBackground">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>{vtranslate('LBL_JUSERL_CONFIGURATION', $QUALIFIED_MODULE_NAME)} vtiger_joomla_userlevels_{$HOSTNO}</h3>
	</div>

	<form class="form-horizontal" id="jUserLConfig">
		<div class="modal-body configContent">

			<input type="hidden" value="{$RECORD_ID}" name="record" id="recordId"/>
			<input type="hidden" value="{$HOSTNO}" name="host_no" id="host_no"/>
			<table class="table table-bordered listViewEntriesTable">
			{assign var=WIDTHTYPE value="small-jhost"}
				<thead>
					<tr class="listViewHeaders">
						{foreach $LISTVIEW_HEADERS as $LISTVIEW_HEADER => $LISTVIEW_HEADER_TITLE}
						<th nowrap class="middle" {if $LISTVIEW_HEADER eq 'joomla_userlevels'} style="text-align:left" {else} style="text-align:center" {/if}>{vtranslate($LISTVIEW_HEADER_TITLE, $QUALIFIED_MODULE_NAME)}</th>
						{/foreach}
					</tr>
				</thead>
			{foreach $LEVELS as $ROWS => $ROW}
				<tr class="listViewEntries">
					<td class="listViewEntryValue {$WIDTHTYPE}"  width="20%" nowrap valign="middle" style="text-align:center">{$ROW['joomla_userlevelsid']}</td>
					<td class="listViewEntryValue {$WIDTHTYPE}"  width="30%" nowrap valign="middle">{$ROW['joomla_userlevels']}</td>
					<td class="listViewEntryValue {$WIDTHTYPE}"  width="30%" nowrap valign="middle" style="text-align:center">
					<input type="radio" name="presence[{$ROW['joomla_userlevelsid']}]" value='1' {if $ROW['presence']} checked="checked" {/if} />&nbsp;{vtranslate('LBL_YES', $QUALIFIED_MODULE_NAME)}&nbsp;&nbsp;&nbsp;
					<input type="radio" name="presence[{$ROW['joomla_userlevelsid']}]" value='0' {if !$ROW['presence']} checked="checked" {/if}/>&nbsp;{vtranslate('LBL_NO', $QUALIFIED_MODULE_NAME)}
					</td>
					<td class="listViewEntryValue {$WIDTHTYPE}"  width="20%" nowrap valign="middle" style="text-align:center">{$ROW['sortorderid']}</td>
				</tr>
			{/foreach}
			</table>

		</div>
		{include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
	</form>

</div>
{/strip}