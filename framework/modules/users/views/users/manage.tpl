{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{css unique="manage_users" corecss="tables,autocomplete"}
#user_dt_input {
    position:relative;
    width:200px;
    height:20px;
}
{/css}

<div class="module users manage yui-skin-sam">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help"|gettext|cat:" "|cat:("Managing Users"|gettext) module="manage-users"}
        </div>
        <h1>{$moduletitle|default:"Manage Users"|gettext}</h1>
    </div>
	<p>
        {'From here, you can create, modify and remove normal user accounts.'|gettext}&#160;&#160;
        {'You will not be able to create, modify or remove administrator accounts (these options will be disabled).'|gettext}
    </p>
	<div class="module-actions">
		{icon class=add module=users action=create text="Create a New User"|gettext}
	</div>
	
	<div id="user_autocomplete">
		<label for="user_dt_input">{'Filter by First Name, Last Name, or Email Address:'|gettext}</label>
		<input id="user_dt_input" type="text" />
	</div>	
	<div id="dt_ac_container"></div>
	
	<div id="pagelinks">&#160;</div>
	<div id="totalResult">&#160;</div>
	<div id="manage_user_dynamicdata">
    
    </div>
	
</div>

{*FIXME convert to yui3*}
{script unique="manage_users"}
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
               {/literal}{permissions}{literal}

                    elCell.innerHTML = '<div class="item-actions">';
                    editstring       = '{/literal}{icon img="edit.png" action="edituser" id="editstringid" title="Edit this user"|gettext}{literal}';
                    passwordstring   = '{/literal}{icon img="key.png" action="change_password" ud="passwordstringid" title="Change this users password"|gettext}{literal}';
                    deletestring     = '{/literal}{icon img="delete.png" action="delete" id="deletestringid" title="Delete this user"|gettext onclick="return confirm(\'"|cat:("Are you sure you want to delete this user?"|gettext)|cat:"\');"}{literal}';
                    editstring     = editstring.replace('editstringid',oRecord._oData.id);
                    passwordstring = passwordstring.replace('passwordstringid',oRecord._oData.id);
                    deletestring   = deletestring.replace('deletestringid',oRecord._oData.id);

                    elCell.innerHTML += editstring + passwordstring + deletestring +'</div>';

                {/literal}{/permissions}{literal}
            };
    
			// Column definitions
			var myColumnDefs = [ // sortable:true enables sorting
                { key:"id",label:"{/literal}{"Username"|gettext}{literal}",sortable:true,formatter:formatID},
                { key:"firstname",label:"{/literal}{"First Name"|gettext}{literal}",sortable:true},
                { key:"lastname",label:"{/literal}{"Last Name"|gettext}{literal}",sortable:true},
                { key:"is_acting_admin",label:"{/literal}{"Is Admin"|gettext}{literal}",sortable:true,formatter:formatActingAdmin},
                {/literal}{permissions}{literal}
                { key:"Actions",label:"{/literal}{"Actions"|gettext}{literal}",sortable:false,formatter: formatactions}
                {/literal}{/permissions}{literal}
			];

			// DataSource instance
			var myDataSource = new YAHOO.util.DataSource(EXPONENT.PATH_RELATIVE+"index.php?controller=users&action=getUsersByJSON&json=1&ajax_action=1&filter={/literal}{$filter}{literal}&");
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
				
				var sort, dir, startIndex, results;
				
				oState = oState || {pagination: null, sortedBy: null};
				
				/* If using column sorting built into DataTable, these next two lines
				will properly set the current _sortedBy_ column and the _sortDirection_
				sort = (oState.sortedBy) ? oState.sortedBy.key : oSelf.getColumnSet().keys[0].getKey();
				dir = (oState.sortedBy && oState.sortedBy.dir === DataTable.CLASS_DESC) ? "desc" : "asc"; */
                sort = (oState.sortedBy) ? oState.sortedBy.key : "id";
                dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "assc";
				startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
				results = (oState.pagination) ? oState.pagination.rowsPerPage : null;
				
				return  "results=" + results +
						"&startIndex=" + startIndex +
						"&sort=" + sort + "&dir=" + dir +
						"&query=" + at.value;
			}
			
			 // DataTable configuration
			var myConfigs = {
				generateRequest: requestBuilder,
				initialRequest: "sort=id&dir=asc&startIndex=0&results=10", // Initial request for first page of data
				dynamicData: true, // Enables dynamic server-driven data
				sortedBy : {key:"id", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
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
				df.innerHTML = "{/literal}{"Total Results"|gettext}:{literal}"+" " + oResponse.meta.totalRecords;
			
				return oPayload;
			}
				
		});
	{/literal}
{/script}