{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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

<div class="administrationmodule usermanager">
<div class="form_header">
        <h1>{$_TR.form_title}</h1>
        <p>{$_TR.form_header}</p>
	{if $smarty.const.SITE_ALLOW_REGISTRATION == 0}
		<blockquote class="error"><i>{$_TR.no_registration}</i></blockquote>
	{/if}
	<a href="{link module=userprofilemodule action=edit id=0}">{$_TR.new_user}</a>
</div>
<div class="datatable">
        <div id="userdt">
        <table id="users">
        <thead>
        <tr>
                <th>Real Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$users item=user}
                <tr>
                <td>{$user->firstname} {$user->lastname}</td>
                <td>{$user->username}</td>
                <td>{$user->email}</td>
                <td>
			{if $user->is_locked}{icon img=lock.png title="Account Locked"}{/if}	
			{if $user->is_admin == 0}
				{*icon img="edit.png" action=umgr_edit id=$user->id title="Edit User `$user->username`"*}
				{icon img="edit.png" module=userprofilemodule action=edit id=$user->id title="Edit User `$user->username`"}
				{icon img="delete.png" action=umgr_delete id=$user->id title="Delete User `$user->username`"}
			{else}
				{icon img="edit.disabled.png" title="Admin Users Can't Be Edited"}
                                {icon img="delete.disabled.png" title="Admin Users Can't Be Deleted"}
			{/if}
			
		</td>
                </tr>
        {/foreach}
        </tbody>
        </table>
        </div>
</div>

{script unique="userdt" yuimodules='"datatable"'}
        {literal}
                YAHOO.example.EnhanceFromMarkup = new function() {
                        var myColumnDefs = [
                            {key:"realname",label:"Real Name",sortable:true},
                            {key:"username",label:"Username", sortable:true, width:"100", minWidth:"100", resizeable:true},
                            {key:"email",label:"Email",sortable:true},
                            {key:"actions",label:"Actions",sortable:false}
                      ];

                      this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get("users"));
                      this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
                      this.myDataSource.responseSchema = {
                          fields: [{key:"realname"},
                                  {key:"username"},
                                  {key:"email"},
                                  {key:"actions"}
                            ]
                        };

                        this.myDataTable = new YAHOO.widget.DataTable("userdt", myColumnDefs, this.myDataSource, {
                                caption:"",
                                width: "600",
                                scrollable: true,
                                sortedBy:{key:"realname",dir:"asc"}
				{/literal}{if $users|@count > 10}{literal},paginator: new YAHOO.widget.Paginator({rowsPerPage: 10}){/literal}{/if}{literal}
                                }
                        );
                    };
        {/literal}
{/script}
</div>
