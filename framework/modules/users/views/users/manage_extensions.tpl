{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
 
<div class="module users manage-extensions">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Managing User Profile Extensions"|gettext) module="manage-extensions"}
        </div>
        <h1>{"Manage User Profile Extensions"|gettext}</h1>
    </div>
	<p>
        {"From here activate or deactivate user profile extensions."|gettext}&nbsp;&nbsp;
        {"User profile extensions are used to give users the ability to put in more information about themselves."|gettext}&nbsp;&nbsp;
        {"The active extensions will add fields to the form a user has to fill out to create an account."|gettext}
    </p>
    {pagelinks paginate=$page top=1}
	<table class="exp-skin-table">
	    <thead>
		<tr>
		    {$page->header_columns}
		</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=listing name=listings}
			<tr class="{cycle values="odd,even"}">
				<td>{$listing->title}</td>
				<td>{$listing->body}</td>
				<td>
                    {if $listing->active}
                        <a href="{link action=toggle_extension id=$listing->id}">{img src=$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}</a>
                    {else}
                        <a href="{link action=toggle_extension id=$listing->id}">{img src=$smarty.const.ICON_RELATIVE|cat:'toggle_off.png'}</a>
                    {/if}
				</td>
			</tr>
			{foreachelse}
			    <td colspan="{$page->columns|count}">{'No Data'|gettext}.</td>
			{/foreach}
		</tbody>
	</table>
    {pagelinks paginate=$page bottom=1}
</div>
