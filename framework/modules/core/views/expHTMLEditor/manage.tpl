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

{css unique="managehtml1" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/admin-global.css"}

{/css}

{css unique="managehtml2" corecss="tables"}

{/css}

<div class="module administration htmleditor">
    <div class="info-header">
        <div class="related-actions">
			<a class="add" href="{link module="expHTMLEditor" action="edit"}">{"Create New Configuration"|gettext}</a>
            {help text="Get Help"|gettext|cat:" "|cat:("Managing CKEditor Toolbars"|gettext) module="ckeditor-toolbar-configuration"}
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
					{"Custom Toolbar"|gettext}
                </th>
                <th>
					{"Other Customizations"|gettext}
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
                        <span class="active">{'Active'|gettext}</span>
                    {else}
						<a class="inactive" href="{link module="expHTMLEditor" action=activate id="default"}" title="Activate this Toolbar"|gettext>Activate</a>
                    {/if}
                </td>
                <td>
                    <a href="{link module="expHTMLEditor" action=preview id=0}" title="Preview this Toolbar"|gettext>{"Default"|gettext}</a>
                </td>
                <td>
					kama
                </td>
                <td>
					{'No'|gettext}
                </td>
	            <td>
					{'No'|gettext}
	            </td>
                <td>

                </td>
            </tr>
            {foreach from=$configs item=cfg}
            <tr class="{cycle values="odd,even"}{if $module->active == 1} active{/if}">
                <td>
                    {if $cfg->active}
                        <span class="active">{'Active'|gettext}</span>
                    {else}
                        <a class="inactive" href="{link module="expHTMLEditor" action=activate id=$cfg->id}" title="Activate this Toolbar"|gettext>Activate</a>
                    {/if}
                </td>
                <td>
					<a href="{link module="expHTMLEditor" action=preview id=$cfg->id}" title="Preview this Toolbar"|gettext>{$cfg->name}</a>
                </td>
                <td>
                    {$cfg->skin}
                </td>
                <td>
					{if $cfg->data}
						{'Yes'|gettext}
					{else}
						{'No'|gettext}
					{/if}
                </td>
	            <td>
					{if !$cfg->scayt_on || $cfg->plugins || $cfg->paste_word || $cfg->stylesset || $cfg->formattags || $cfg->fontnames}
						{'Yes'|gettext}
					{else}
						{'No'|gettext}
					{/if}
	            </td>
                <td>
					<div class="item-actions">
						{icon module="expHTMLEditor" action=edit record=$cfg title="Edit this Toolbar"|gettext}
						{icon module="expHTMLEditor" action=delete record=$cfg title="Delete this Toolbar"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this toolbar?"|gettext)|cat:"');"}
					</div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
