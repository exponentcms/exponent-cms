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

<div class="module banner manage">
	<h1>{$moduletitle|default:"Manage Banners"}</h1>	
	<p>
        You can manage the banners for your site's banner modules here.  The banners you create and configure
        here will be available to all the banner modules you have on your site.
    </p>
    {icon module="banner" action="create" title="Create a new banner" alt="Create a new banner"}{br}
    {icon module="banner" action="export" title="Export banner data" alt="Export banner data"}{br}
    {icon module="banner" action="reset_stats" title="Reset banner stats" alt="Reset banner stats"  onclick="return confirm('Are you sure you want to reset the Impression and Click statistics of your banners?');"}{br}
    {icon module="company" action="create" title="Create a new company" alt="Create a new company"}{br}
	{$page->links}
	<table class="exp-skin-table">
	    <thead>
		<tr>
		    <th>&nbsp;</th>
		    {$page->header_columns}
			<th>Admin</th>
		</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=listing name=listings}
			<tr class="{cycle values="odd,even"}">
			    <td>{img file_id=$listing->expFile[0]->id width=75 height=48}</td>
				<td>{$listing->title}</td>
				<td>{$listing->company->title}</td>
				<td>{$listing->impressions}</td>
				<td>{$listing->clicks}</td>
			    <td>
			        {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
                    <div class="item-actions">
                    {if $permissions.edit == true}
                        {icon controller=$page->controller action=edit id=$listing->id title="Edit"}
                    {/if}
                    {if $permissions.delete == true}
                        {icon controller=$page->controller action=delete id=$listing->id title="Delete" onclick="return confirm('Are you sure you want to delete this?');"}
                    {/if}
                    </div>
                    {/permissions}
			    </td>
			</tr>
			{foreachelse}
			<tr class="{cycle values="odd,even"}">
			    <td colspan="{$page->columns|count}">No Data.</td>
			</tr>
			{/foreach}
		</tbody>
		</table>
		{$page->links}
	</table>
</div>
