{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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
<div class="administrationmodule groupmanager">
<div class="form_header">
        <h1>{'Manage Group Accounts'|gettext}</h1>
        <p>{'Groups are used to treat a set of users as a single entity, mostly for permission management.  This form allows you to determine which users belong to which groups, create new groups, modify existing groups, and remove groups.<br /><br />When a new user account is created, it will be automatically added to all groups with a Type of "Default"'|gettext}</p>
	{if $perm_level == 2}
		<a class="mngmntlink administration_mngmntlink" href="{link action=gmgr_editprofile id=0}">{'New Group Account'|gettext}</a>
	{/if}
</div>

<div class="datatable">
        <div id="groupdt">
        <table id="groups">
        <thead>
        <tr>
                <th>Group Name</th>
                <th>Type</th>
                <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$groups item=group}
                <tr>
                <td>{$group->name}</td>
                <td>{if $group->inclusive}<b>{'Default'|gettext}</b>{else}Normal{/if}</td>
                <td>
                        {if $perm_level == 2}
                                {icon class=edit action=gmgr_editprofile record=$group title="Edit Group `$group->name`"}
								{icon class=delete action=gmgr_delete record=$group title="Delete Group `$group->name`"}
                                {icon img="groupperms.png" action=gmgr_membership record=$group title="Add/Remove Members to Group `$group->name`"}
                        {/if}

                </td>
                </tr>
        {/foreach}
        </tbody>
        </table>
        </div>
</div>

{script unique="groupdt" yuimodules='"datatable"'}
        {literal}
                YAHOO.example.EnhanceFromMarkup = new function() {
                        var myColumnDefs = [
                            {key:"groupname",label:"Group Name",sortable:true, width:"200", minWidth:"200", resizeable:true},
                            {key:"type",label:"Type",sortable:true},
                            {key:"actions",label:"Actions",sortable:false}
                      ];

                      this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get("groups"));
                      this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
                      this.myDataSource.responseSchema = {
                          fields: [{key:"groupname"},
                                  {key:"type"},
                                  {key:"actions"}
                            ]
                        };

                        this.myDataTable = new YAHOO.widget.DataTable("groupdt", myColumnDefs, this.myDataSource, {
                                caption:"",
                                width: "600",
                                scrollable: true,
                                sortedBy:{key:"groupname",dir:"asc"}
				{/literal}{if $groups|@count > 10}{literal},paginator: new YAHOO.widget.Paginator({rowsPerPage: 10}){/literal}{/if}{literal}
                                }
                        );
                    };
        {/literal}
{/script}
 </div>
