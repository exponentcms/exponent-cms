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
{css unique="managemods" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/admin-global.css"}

{/css}

<div class="module administration htmleditoranager">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help Managing CKEditor Toolbars" module="managecke"}
        </div>
        <h1>{"CKEditor Toolbar Manager"|gettext}</h1>
    </div>
    
    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th width="10%">
                    {"Active"|gettext}
                </th>
                <th>
                    {"Name"|gettext}
                </th>
                <th width="20%">
                    {"Action"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="{cycle values="odd,even"}{if $module->active == 1} active{/if}">
                <td>
                    {if !$cfg}
                        <span class="active">Active</span>
                    {else}
                        <a class="inactive" href="{link action="activate" id="default"}">Activate</a>
                    {/if}
                </td>
                <td>
                    <a href="{link action="preview" id="default"}">{"Default"|gettext}</a>
                </td>
                <td>

                </td>
            </tr>
            {foreach from=$configs item=cfg}
            <tr class="{cycle values="odd,even"}{if $module->active == 1} active{/if}">
                <td>
                    {if $cfg->active}
                        <span class="active">Active</span>
                    {else}
                        <a class="inactive" href="{link action="activate" id=$cfg->id}">Activate</a>
                    {/if}
                </td>
                <td>
                    <a href="{link action="preview" id=$cfg->id}">{$cfg->name}</a>
                </td>
                <td>
					<div class="item-actions">
						{icon action=edit record=$cfg}
						{icon action=delete record=$cfg}
					</div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>

</div>
