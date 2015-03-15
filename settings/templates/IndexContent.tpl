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
<div class="container-fluid">
	<div class="listViewEntriesDiv span10" style='overflow-x:auto;'>
		<div class="row-fluid">
		<div class="span10"><p>&nbsp;<br /><br /></p></div>
		<div class="span10">
			<h1>{vtranslate('LBL_JUSERLEVEL_INFORMATION', $QUALIFIED_MODULE_NAME)}<br /></h1><br />
			<h4>{vtranslate('LBL_JUSERLEVEL_CONFIGURATION', $QUALIFIED_MODULE_NAME)}<br /></h4><br />
			<p>{vtranslate('LBL_JUSERLEVEL_CONFIG_DESC', $QUALIFIED_MODULE_NAME)}</p>
		</div>
		<div class="span10"><p>&nbsp;<br /><br /></p></div>
			<span class="listViewLoadingImageBlock hide modal" id="loadingListViewModal">
				<img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image" title="{vtranslate('LBL_LOADING', $QUALIFIED_MODULE_NAME)}"/>
				<p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $QUALIFIED_MODULE_NAME)}........</p>
			</span>
			{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
			<table class="table table-bordered table-condensed listViewEntriesTable">
				<thead>
					<tr class="listViewHeaders">
						<th width="24" class="{$WIDTHTYPE}"></th>
						{assign var=WIDTH value={80/(count($LISTVIEW_HEADERS))}}
						{foreach $LISTVIEW_HEADERS as $LISTVIEW_HEADER => $LISTVIEW_HEADER_TITLE}
						<th width="{$WIDTH}%" nowrap {if $LISTVIEW_HEADER@last}colspan="2" {/if} class="{$WIDTHTYPE}">{vtranslate($LISTVIEW_HEADER_TITLE, $QUALIFIED_MODULE_NAME)}</th>
						{/foreach}
					</tr>
				</thead>

				<tbody>
				{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES}
					<tr class="listViewEntries" data-id="{$LISTVIEW_ENTRY->getId()}"
							{* {if method_exists($HOST_MODEL,'getEditViewUrl')}data-recordurl="{$HOST_MODEL->getEditViewUrl()}"{/if} *}
					 >
					<td width="24" nowrap class="{$WIDTHTYPE}">
						<img src="{vimage_path('Joomlahosts.png')}" class="alignCenter" title="{vtranslate('LBL_JOOMLA_INSTANCE',$QUALIFIED_MODULE_NAME)}" />
					</td>
						{foreach $LISTVIEW_HEADERS as $LISTVIEW_HEADER => $LISTVIEW_HEADER_TITLE}
							{assign var=LAST_COLUMN value=$LISTVIEW_HEADER@last}

							<td class="listViewEntryValue {$WIDTHTYPE}"  width="{$WIDTH}%" nowrap valign="middle">
								
								{if $LISTVIEW_HEADER == 'picklisttable'}
								&nbsp;{vtranslate('LBL_EDIT', $QUALIFIED_MODULE_NAME)}&nbsp;&nbsp;<b><a id="{$LISTVIEW_ENTRY->getDisplayValue('host_no')}" title="{vtranslate('LBL_EDIT_ITEMS', $QUALIFIED_MODULE_NAME)}" onclick='Settings_Joomlahosts_Index_Js.triggerEdit(event, "index.php?module=Joomlahosts&parent=Settings&view=Edit&record={$LISTVIEW_ENTRY->getDisplayValue('joomlahostsid')}&hostno={strtolower($LISTVIEW_ENTRY->getDisplayValue('host_no'))}");return false;'>vtiger_joomla_userlevels_{strtolower($LISTVIEW_ENTRY->getDisplayValue('host_no'))}</a></b>&nbsp;&nbsp;{vtranslate('LBL_TABLE', $QUALIFIED_MODULE_NAME)}
								{else}
								&nbsp;{$LISTVIEW_ENTRY->getDisplayValue($LISTVIEW_HEADER)}
								{/if}
							</td>
						
						{/foreach}
					</tr>
				{/foreach}
				</tbody>

			</table>
		</div>
	</div>
</div>
{/strip}