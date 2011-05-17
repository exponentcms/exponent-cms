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
 
{css unique="data-view" corecss="tables"}

{/css}

<h2>{$title}</h2>
{$page->links}
<div style="overflow : auto; overflow-y : hidden;">
<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
    <thead>
        <tr>
            {$page->header_columns}
			<th>
				Links
			</th>
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

			<td>
				<a href="{link action=view_record module=formbuilder form_id=$f->id}{if $smarty.const.SEF_URLS == 1}/{else}&{/if}id{if $smarty.const.SEF_URLS == 1}/{else}={/if}{$user->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}view.png" title="{$_TR.alt_view}" alt="{$_TR.alt_view}" /></a>
				{if $permissions.editdata == 1}
					<a href="{link action=edit_record module=formbuilder form_id=$f->id}{if $smarty.const.SEF_URLS == 1}/{else}&{/if}id{if $smarty.const.SEF_URLS == 1}/{else}={/if}{$user->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" /></a>
				{/if}
				{if $permissions.deletedata == 1}
					<a href="{link action=delete_record module=formbuilder form_id=$f->id}{if $smarty.const.SEF_URLS == 1}/{else}&{/if}id{if $smarty.const.SEF_URLS == 1}/{else}={/if}{$user->id}" onclick="return confirm('{$_TR.delete_confirm}');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
				{/if}
			</td>
        </tr>
        {/foreach}
    </tbody>
</table>
</div>
{$page->links}
<a href="{$backlink}">{$_TR.back}</a>
