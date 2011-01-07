{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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
	{if !$hidenotes && $simplenotes|@count > 0}
	    {if $title}<h3>{$title}</h3>{/if}
	    
	    {if $unapproved > 0}
	    <div class="unapproved">
	        There are {$unapproved} notes awaiting approval.
	        <a href="{link action=manage content_id=$content_id content_type=$content_type tab=$tab}">Click here to manage approvals</a>
	    </div>
	    {/if}
        
	    <ol class="notelist">		
        {foreach from=$simplenotes->records item=note name=simplenote}
		<li class="note">
			<cite>
                <span class="comment-data">{$note->edited_at|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}</span> - <span class="attribution">{$note->name}</span>
			</cite>
			{permissions level=$smarty.const.UILEVEL_NORMAL}
                {permissions level=$smarty.const.UILEVEL_NORMAL}
                    {if $permissions.manage == 1}
                        {icon img=edit.png action=edit id=$note->id tab=$tab content_id=$content_id content_type=$content_type title="Edit Note"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon img=delete.png action=delete id=$note->id tab=$tab content_id=$content_id content_type=$content_type title="Delete Note" onclick="return confirm('Are you sure you want to delete this note?');"}
                    {/if}
                {/permissions}
            {/permissions}
			<div class="note-text bodycopy">			
    			{$note->body}
			</div>
		</li>
    	{/foreach}
    	</ol>
    	{$simplenotes->links}
	{/if}
    
    <a href="{link action=edit content_id=$content_id content_type=$content_type tab=$tab}">Add a Note</a>
</div>
