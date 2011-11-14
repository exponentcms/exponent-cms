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

{css unique="managebanners" corecss="tables"}

{/css}

<div class="module banner manage">
	<h1>{$moduletitle|default:"Manage Banners"|gettext}</h1>
	<p>
        {'You can manage the banners for your site\'s banner modules here.  The banners you create and configure
        here will be available to all the banner modules you have on your site.'|gettext}
    </p>
	{icon class=add action=create text="Create a new banner"|gettext}{br}
	{icon action=export text="Export banner data"|gettext}{br}
    {icon action=reset_stats text="Reset banner stats"|gettext onclick="return confirm('Are you sure you want to reset the Impression and Click statistics of your banners?');"}{br}
	{*{icon class=add module=company action=create text="Create a new company"|gettext}{br}*}
	{icon class=manage module=company action=showall text="Manage companies"|gettext}{br}
    {pagelinks paginate=$page top=1}
	<table class="exp-skin-table">
	    <thead>
			<tr>
				<th>&nbsp;</th>
				{$page->header_columns}
				<th>{'Admin'|gettext}</th>
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
								{icon action=edit record=$listing}
							{/if}
							{if $permissions.delete == true}
								{icon action=delete record=$listing}
							{/if}
						</div>
                    {/permissions}
			    </td>
			</tr>
			{foreachelse}
				<tr class="{cycle values="odd,even"}">
					<td colspan="{$page->columns|count}">{'No Data'|gettext}.</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
    {pagelinks paginate=$page bottom=1}
</div>
