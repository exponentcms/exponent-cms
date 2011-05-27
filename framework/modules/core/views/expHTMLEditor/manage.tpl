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
{css unique="managehtml1" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/admin-global.css"}

{/css}

{css unique="managehtml2" corecss="tables"}

{/css}

<div class="module administration htmleditoranager">
    <div class="info-header">
        <div class="related-actions">
			<a class="add" href="{link module="expHTMLEditor" action="edit"}">{"Create New Configuration"|gettext}</a>
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
                <th>
                    {"Skin"|gettext}
                </th>
                <th>
					{"SpellCheck"|gettext}
                </th>
                <th>
					{"Word Pasting"|gettext}
                </th>
                <th width="20%">
                    {"Action"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="{cycle values="odd,even"}{if $module->active == 1} active{/if}">
                <td>
					{assign var=active value=0}
					{foreach from=$configs item=cfg}
						{if $cfg->active}
							{assign var=active value=1}
						{/if}
					{/foreach}
                    {if !$active}
                        <span class="active">Active</span>
                    {else}
						<a class="inactive" href="{link module="expHTMLEditor" action=activate id="default"}" title="Activate this Toolbar">Activate</a>
                    {/if}
                </td>
                <td>
                    <a href="{link module="expHTMLEditor" action=preview id="default"}" title="Preview this Toolbar">{"Default"|gettext}</a>
                </td>
                <td>
					kama
                </td>
                <td>
					On
                </td>
                <td>
					No
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
                        <a class="inactive" href="{link module="expHTMLEditor" action=activate id=$cfg->id}" title="Activate this Toolbar">Activate</a>
                    {/if}
                </td>
                <td>
					<a href="{link module="expHTMLEditor" action=preview id=$cfg->id}" title="Preview this Toolbar">{$cfg->name}</a>
                </td>
                <td>
                    {$cfg->skin}
                </td>
                <td>
					{if $cfg->scayt_on}
						On
					{else}
						Off
					{/if}
                </td>
                <td>
					{if $cfg->paste_word}
						Yes
					{else}
						No
					{/if}
                </td>
                <td>
					<div class="item-actions">
						{icon module="expHTMLEditor" action=edit title="Edit this Toolbar" record=$cfg}
						{icon module="expHTMLEditor" action=delete title="Delete this Toolbar" record=$cfg}
					</div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>

</div>
