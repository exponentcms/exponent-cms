{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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

{css unique="managediscounts" corecss="tables"}

{/css}

{permissions}
{if $permissions.manage}
    <div class="module ecomconfig manage-discounts">
        <div class="form_header">
            <h2>{"Manage Discounts"|gettext}</h2>
            <blockquote>{"Here you can configure discounts to be used in your store."|gettext}</blockquote>
        </div>

        {*icon class=edit action=edit_optiongroup_master record=$group}
        {icon class=delete action=delete_optiongroup_master record=$group onclick="return confirm('This option group is being used by `$group->timesImplemented` products. Deleting this option group will also delete all of the options related to it. Are you sure you want to delete this option group?');"*}

        {icon class=add controller="ecomconfig" action="edit_discount" text="Create a New Store Discount"|gettext}

        {if $page->records|@count > 0}
            <h3>{"Modify existing store discounts"|gettext}</h3>
           {pagelinks paginate=$page top=1}
			<table id="discounts" class="exp-skin-table">
				<thead>
					<tr>
                        {$page->header_columns}
                        <th>{'Action'|gettext}</th>
					</tr>
				</thead>
				<tbody>
                    {foreach from=$page->records item=listing name=listings}
                        <tr class="{cycle values='odd,even'}">
                            {form action=update_discount}
                                {control type="hidden" name="id" value=$listing->id}
                                <td style="text-align:center;">
                                    {if $listing->enabled}
                                        <a href="{link action=activate_discount id=$listing->id enabled=0}" title="Disable Discount">{img src=$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}</a>
                                    {else}
                                        <a href="{link action=activate_discount id=$listing->id enabled=1}" title="Enable Discount">{img src=$smarty.const.ICON_RELATIVE|cat:'toggle_off.png'}</a>
                                    {/if}
                                </td>
                                <td>
                                    {$listing->title}
                                </td>
                                <td>
                                    {$listing->actions[$listing->action_type]}
                                </td>
                                <td>
                                    {$listing->coupon_code}
                                </td>
                                <td>
                                    {if $listing->never_expires}
                                        {"Never Expires"|gettext}
                                    {else}
                                        {$listing->enddate_time|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}
                                    {/if}
                                </td>
                                <td>
                                    {if $permissions.edit || ($permissions.create && $listing->poster == $user->id)}
                                        <div class="item-actions">
                                            {icon class=edit action=edit_discount record=$listing title="Edit Discount"}
                                        </div>
                                    {/if}
                                </td>
                            {/form}
                        </tr>
                    {/foreach}
				</tbody>
			</table>
			{pagelinks paginate=$page bottom=1}
        {else}
            <div>{"You do not have any discounts currently."|gettext}</div>
        {/if}
    </div>
{/if}
{/permissions}
