{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}

{css unique="manage_groups" corecss="tables"}

{/css}

<div class="module users manage">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help"|gettext|cat:" "|cat:("Managing Users"|gettext) module="manage-users"}
        </div>
        <h1>{$moduletitle|default:"Manage Users"}</h1>
    </div>
	<p>
        {'From here, you can create, modify and remove normal user accounts.
        You will not be able to create, modify or remove administrator accounts (these options will be disabled).'|gettext}
    </p>
	<div class="module-actions">
		{icon class=add module=users action=create text="Create a New User"|gettext}
	</div>
	
	<div id="user_autocomplete">
		<label for="user_dt_input">{'Filter by First Name, Last Name, or Email Address:'|gettext}</label>
		<input id="user_dt_input" type="text" />
	</div>	
	<div id="dt_ac_container"></div>
	
    {* pagelinks paginate=$page top=1 *}
	<div id="pagelinks">&nbsp;</div>
	<div id="totalResult">&nbsp;</div>
	<div id="manage_user_dynamicdata">
    
    </div>
	
	<!--
	<table class="exp-skin-table">
	    <thead>
			<tr>
				{$page->header_columns}
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=user name=listings}
			<tr class="{cycle values="odd,even"}">
				<td>{$user->username}</td>
				<td>{$user->firstname}</td>
				<td>{$user->lastname}</td>
				<td>
				{if $user->is_acting_admin == 1}
				    <img src="{$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}">
				{/if}
				</td>
			    <td>
			        {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
						<div class="item-actions">
							{icon class=edit action=edituser record=$user}
							{icon class="password" action=change_password record=$user title="Change this users password"|gettext text="Password"|gettext}
							{icon action=delete record=$user title="Delete"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this user?"|gettext)|cat:"');"}
						</div>
                    {/permissions}
			    </td>
			</tr>
			{foreachelse}
			    <td colspan="{$page->columns|count}">No Data.</td>
			{/foreach}
		</tbody>
	</table>
    {* pagelinks paginate=$page bottom=1 *}
	-->
</div>
<script type="text/javascript">
	{literal}
		YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event','yui2-container','yui2-json','yui2-datasource','yui2-connection','yui2-autocomplete','yui2-element','yui2-paginator','yui2-datatable', function(Y) {
			var YAHOO=Y.YUI2;
			var myDataSource = null;
			var myDataTable = null;
			var at = YAHOO.util.Dom.get('user_dt_input');
			
			 //set up autocomplete
			var getTerms = function(query) {
				myDataSource.sendRequest('sort=id&dir=asc&startIndex=0&results=10&query=' + query,myDataTable.onDataReturnInitializeTable, myDataTable);
		
			};
			
			var oACDS = new YAHOO.util.FunctionDataSource(getTerms);
			oACDS.queryMatchContains = true;
			var oAutoComp = new YAHOO.widget.AutoComplete("user_dt_input","dt_ac_container", oACDS);
			oAutoComp.minQueryLength = 0;
			// Formatters for datatable columns

        // filename formatter
		
		var formatID = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = oRecord.getData().usernamelabel;
        };
		
		var formatActingAdmin = function(elCell, oRecord, oColumn, sData) {
			if(oRecord.getData().is_acting_admin == "1") {
				 elCell.innerHTML ='<img src="{/literal}{$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}{literal}">'
			}
        };
		
		
        var formatactions = function(elCell, oRecord, oColumn, sData) {
           {/literal}{permissions level=$smarty.const.UILEVEL_PERMISSIONS}{literal}
		   
				 elCell.innerHTML = '<div class="item-actions">';
				 editstring       = '{/literal}{icon class="edit" action="edituser" id="editstringid" title="Edit this user"|gettext}{literal}';
				 passwordstring   = '{/literal}{icon class="password" action="change_password" id="passwordstringid" title="Change this users password"|gettext text="Password"|gettext}{literal}';
				 deletestring     = '{/literal}{icon action="delete" id="deletestringid" title="Delete this user"|gettext onclick="return confirm(\'"|cat:("Are you sure you want to delete this user?"|gettext)|cat:"\');"}{literal}';
				 editstring     = editstring.replace('editstringid',oRecord._oData.id);
				 passwordstring = passwordstring.replace('passwordstringid',oRecord._oData.id);
				 deletestring   = deletestring.replace('deletestringid',oRecord._oData.id);
				 
				elCell.innerHTML += editstring + passwordstring + deletestring +'</div>';
				
			{/literal}{/permissions}{literal}
        };
    
	
			// Column definitions
			var myColumnDefs = [ // sortable:true enables sorting
			{ key:"id",label:"Username",formatter:formatID},
			{ key:"firstname",label:"First Name"},
			{ key:"lastname",label:"Last Name"},
			{ key:"is_acting_admin",label:"Is Admin",formatter:formatActingAdmin},
			{ label:"Actions",label:"", sortable:false,formatter: formatactions}
			];
			// DataSource instance
			var myDataSource = new YAHOO.util.DataSource(EXPONENT.URL_FULL+"index.php?controller=users&action=getFilesByJSON&json=1&ajax_action=1&");
			myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
			myDataSource.responseSchema = {
				resultsList: "records",
				fields: [
					"id",
					{key:"username"},
					{key:"firstname"},
					{key:"lastname"},
					{key:"email"},
					{key:"is_acting_admin"},
					{key:"usernamelabel"}
				],
				metaFields: {
					totalRecords: "totalRecords" // Access to value in the server response
				}
			};
			
			var requestBuilder = function (oState, oSelf) {
				/* We aren't initializing sort and dir variables. If you are
				using column sorting built into the DataTable, use this
				set of variable initializers.
				var sort, dir, startIndex, results; */
				
				var startIndex, results;
				
				oState = oState || {pagination: null, sortedBy: null};
				
				/* If using column sorting built into DataTable, these next two lines
				will properly set the current _sortedBy_ column and the _sortDirection_
				sort = (oState.sortedBy) ? oState.sortedBy.key : oSelf.getColumnSet().keys[0].getKey();
				dir = (oState.sortedBy && oState.sortedBy.dir === DataTable.CLASS_DESC) ? "desc" : "asc"; */
				
				startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
				results = (oState.pagination) ? oState.pagination.rowsPerPage : null;
				
				
				return  "results=" 	+ results +
						"&startIndex=" 	+ startIndex +
						"&sort=id&dir=asc" +
						"&query=" + at.value;
			}
			
			 // DataTable configuration
			var myConfigs = {
				generateRequest: requestBuilder,
				initialRequest: "sort=id&dir=asc&startIndex=0&results=10", // Initial request for first page of data
				dynamicData: true, // Enables dynamic server-driven data
				sortedBy : {key:"id", dir:YAHOO.widget.DataTable.CLASS_DESC}, // Sets UI initial sort arrow
				paginator: new YAHOO.widget.Paginator({rowsPerPage:10,containers:"pagelinks"}) // Enables pagination 
			};
		
			// DataTable instance
			var myDataTable = new YAHOO.widget.DataTable("manage_user_dynamicdata", myColumnDefs, myDataSource, myConfigs);
			
			 // Update totalRecords on the fly with value from server
			myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
		
				if (oPayload == null) {
					oPayload = {};
				}
				oPayload.totalRecords = oResponse.meta.totalRecords;
				
				var df = YAHOO.util.Dom.get('totalResult');
				df.innerHTML = "Total Results: " + oResponse.meta.totalRecords;
			
				return oPayload;
			}
				
			
		});
	{/literal}
</script>