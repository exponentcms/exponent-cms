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

<div class="module simplenote get-notes">
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>
                	    {if $title}{$title}{/if}
                    </th>
                </tr>
            </thead>
            <tbody>
    			<tr class="note {cycle values="odd,even"}">
    			    <td>
                    {icon action=edit class="add" content_id=$content_id content_type=$content_type tab=$tab text="Add a Note"|gettext}
                    {if $unapproved > 0}
                    <div class="unapproved">
                        || There are {$unapproved} notes awaiting approval. 
                        {icon action=manage content_id=$content_id content_type=$content_type tab=$tab text="Approve Notes"|gettext} ||
                    </div>
                    {/if}
                	{$simplenotes->links}
			        </td>
    			</tr>
            	{if !$hidenotes && $simplenotes|@count > 0}
                {foreach from=$simplenotes->records item=note name=simplenote}
    			<tr class="note {cycle values="odd,even"}">
    			    <td>
    				<h3>
    					<span class="attribution">{$note->name}</span> - <span class="date">{$note->edited_at|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}</span>
    				</h3>
    					{permissions}
    						<div class="item-actions">
    							{if $permissions.manage == 1}
    								{icon action=edit record=$note tab=$tab content_id=$content_id content_type=$content_type title="Edit Note"}
    							{/if}
    							{if $permissions.delete == 1}
    								{icon action=delete record=$note tab=$tab content_id=$content_id content_type=$content_type title="Delete Note" onclick="return confirm('Are you sure you want to delete this note?');"}
    							{/if}
    						</div>
    					{/permissions}
    				<div class="bodycopy">
    				    <p>
        					{$note->body}
                        </p>
    				</div>
			        </td>
    			</tr>
            	{/foreach}
            	{/if}
            </tbody>
        </table>
    
</div>
