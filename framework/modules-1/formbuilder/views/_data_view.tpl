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

{css unique="data-view" corecss="button, tables"}

{/css}

<h2>{$title}</h2>
{permissions}
	<div class="module-actions">
		<a class="downloadfile" href="{link action=export_csv module=formbuilder id=$f->id}">{"Export as CSV"|gettext}</a>
        {export_pdf_link landscapepdf=1 limit=999 prepend='&#160;&#160;|&#160;&#160;'}
	</div>
{/permissions}
{$page->links}
<div style="overflow : auto; overflow-y : hidden;">
<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
    <thead>
        <tr>
            {$page->header_columns}
            <div class="item-actions">
    			<th>{'Actions'|gettext}</th>
            </div>
        </tr>
    </thead>
    <tbody>
        {foreach from=$page->records item=user key=ukey name=user}
        <tr class="{cycle values="even,odd"}">    

			{foreach from=$page->columns item=column name=column}
				<td>
					{$user->$column}
				</td>
            {/foreach}

            <div class="item-actions">
                <td>
                    <a href="{link action=view_record module=formbuilder form_id=$f->id id=$user->id}"><img style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'view.png'}" title="{'View all data fields for this record'|gettext}" alt="{'View all data fields for this record'|gettext}" /></a>
                    {if $permissions.editdata == 1}
                        <a href="{link action=edit_record module=formbuilder form_id=$f->id id=$user->id}"><img style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'edit.png'}" title="{'Edit this record'|gettext}" alt="{'Edit this record'|gettext}" /></a>
                    {/if}
                    {if $permissions.deletedata == 1}
                        <a href="{link action=delete_record module=formbuilder form_id=$f->id id=$user->id}" onclick="return confirm('{'Are you sure you want to delete this record?'|gettext}');"><img style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'delete.png'}" title="{'Delete this record'|gettext}" alt="{'Delete this record'|gettext}" /></a>
                    {/if}
                </td>
            </div>
        </tr>
        {/foreach}
    </tbody>
</table>
</div>
{$page->links}
<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{$backlink}">{'Back'|gettext}</a>
