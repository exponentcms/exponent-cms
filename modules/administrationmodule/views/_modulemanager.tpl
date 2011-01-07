{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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
<div class="module administrationmodule modulemanager">
    {form action=}
    {/form}
    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>
                {"Active"|gettext}
                </th>
                <th>
                {"Name"|gettext}
                </th>
                <th>
                {"Description"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$modules item=module}
            <tr class="{cycle values="odd,even"}">
                <td>
                <input type="checkbox" name="mods[{$module->class}]"{if $module->active == 1} checked {/if}value=1>
                </td>
                <td>
                {$module->name}
                </td>
                <td>
                {$module->description}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    <div id="modulelist" class="modules">
        {foreach from=$modules item=module}
        {if $module->active == 1}{assign var=active value=active}{else}{assign var=active value=inactive}{/if}
        <div class="moduleitem {cycle values='odd,even'} {$active}">
            <h2>{$module->name} </h2>
            <h3>{$_TR.by} {$module->author}</h3>
            <!-- a href="{link module=info action=showfiles type=$smarty.const.CORE_EXT_MODULE name=$module->class}">{$_TR.view_files}</a>
            <a href="{link action=examplecontent name=$module->class}">{$_TR.example_content}</a -->
            <p>
            {$module->description}
            </p>
            {if $module->active == 1}
                <a class="activation" title="{$_TR.deactivate_reason}" href="{link action=modmgr_activate mod=$module->class activate=0}">{$_TR.deactivate}</a>
            {else}
                <a class="activation" title="{$_TR.activate_reason}" href="{link action=modmgr_activate mod=$module->class activate=1}">{$_TR.activate}</a>
            {/if}
        </div>
        {/foreach}
    </div>
    <a href="{last_url_of type="editable"}">Done</a>
    
</div>

