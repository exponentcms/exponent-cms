{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{css unique="paginationtable" corecss="tables"}

{/css}

<div class="exp-skin-table">
	<table>
	    <thead>
    		<tr>
    		    {$page->header_columns}
    		</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=listing name=listings}
                <tr class="{cycle values="odd,even"}">
                    {foreach from=$page->columns item=col key=key}
                        <td>
                            {if $key=="actupon"}
                                <input type=checkbox name=act-upon[] value={$listing->id} />
                            {else}
                                {if $page->linkables[$key]}
                                    <a href="{link parse_attrs=$page->linkables[$key] record=$listing}">{$listing->$col}</a>
                                {else}
                                    {$listing->$col}
                                {/if}
                            {/if}
                        </td>
                    {/foreach}
                    <!--td>
                    {permissions}
                        <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {icon controller=$page->controller action=edit record=$item}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                            {icon controller=$page->controller action=delete record=$item}
                        {/if}
                        </div>
                    {/permissions}
                    </td-->
                </tr>
			{foreachelse}
			    <td colspan="{$page->columns|count}">{'No Data'|gettext}</td>
			{/foreach}
		</tbody>
	</table>
</div>
