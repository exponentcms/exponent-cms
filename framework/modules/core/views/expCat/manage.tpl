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

{uniqueid assign="id"}

<div class="module expcat manage">
    <div class="info-header">
        <div class="related-actions">
            {if !empty($page)}
                {help text="Get Help"|gettext|cat:" "|cat:("Managing Categories"|gettext) module="manage-categories"}
            {else}
                {help text="Get Help"|gettext|cat:" "|cat:("Managing Categories"|gettext) module="manage-site-categories"}
            {/if}
        </div>
        <h1>{"Manage Categories"|gettext}</h1>
    </div>
	{permissions}
    	{if $permissions.create == 1}
    		<a class="add" href="{link controller=$model_name action=edit rank=1}">{"Create a new Category"|gettext}</a>
    	{/if}
    {/permissions}

    <div id="{$id}" class="yui-navset exp-skin-tabview hide">
        <ul>
            {if !empty($page)}
                <li><a href="#tab0">{$page->model|capitalize} {'Items'|gettext}</a></li>
            {/if}
            {foreach name=tabs from=$cats->modules key=moduleid item=module}
                <li><a href="#tab{$smarty.foreach.items.iteration}">{$moduleid|capitalize} {'Categories'|gettext}</a></li>
            {/foreach}
        </ul>
        <div>
            {if !empty($page)}
                <div id="#tab0">
                    <h3>{'Change'|gettext} {$page->model|capitalize} {'Item Categories'|gettext}</h3>
                    {form action=change_cats}
                        {control type=hidden name=mod value=$page->model}
                        <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type='checkbox' name='checkallp' title="{'Select All/None'|gettext}" onChange="selectAllp(this.checked)">
                                    </th>
                                    <th>
                                        {"Item"|gettext}
                                    </th>
                                    <th>
                                        {"Category"|gettext}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$page->records item=record}
                                    <tr class="{cycle values="odd,even"}">
                                        <td>
                                            {control type="checkbox" name="change_cat[]" label=" " value=$record->id}
                                        </td>
                                        <td>
                                            {$record->title|truncate:50:"..."}
                                        </td>
                                        <td>
                                            {$record->expCat[0]->title|truncate:50:"..."}
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                        <p>{'Select the item(s) to change, then select the new category'|gettext}</p>
                        {control type="dropdown" name=newcat label="Module Categories"|gettext items=$catlist}
                        {control type=buttongroup submit="Change Category on Selected Items"|gettext cancel="Cancel"|gettext returntype="viewable"}
                    {/form}
                </div>
            {/if}
            {foreach name=items from=$cats->modules key=moduleid item=module}
                <div id="tab{$smarty.foreach.items.iteration}">
                    {if $permissions.manage == 1}
                        {ddrerank id=$moduleid items=$cats->records model="expCat" module=$moduleid label=$moduleid|cat:' '|cat:"Categories"|gettext}
                    {/if}
                    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
                        <thead>
                            <tr>
                                <th>
                                {"Category Name"|gettext}
                                </th>
                                <th>
                                {"Use Count"|gettext}
                                </th>
                                <th>
                                {"Actions"|gettext}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {*{foreach from=cats->records item=listing}*}
                            {foreach from=$module item=listing}
                                <tr class="{cycle values="odd,even"}">
                                    <td>
                                        <strong>{$listing->title}</strong>
                                    </td>
                                    <td>
                                        {$listing->attachedcount}
                                    </td>
                                    <td>
                                        {permissions}
                                            {if $permissions.edit == 1}
                                                {icon controller=$controller action=edit record=$listing title="Edit this category"|gettext}
                                            {/if}
                                            {if $permissions.delete == 1}
                                                {icon controller=$controller action=delete record=$listing title="Delete this category"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this category?"|gettext)|cat:"');"}
                                            {/if}
                                        {/permissions}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            {/foreach}
        </div>
    </div>
    <div class="loadingdiv">{'Loading'|gettext}</div>
</div>
{clear}

{script unique="`$id`" yui3mods="1"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
        var tabview = new Y.TabView({srcNode:'#{/literal}{$id}{literal}'});
        tabview.render();
        Y.one('#{/literal}{$id}{literal}').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}

    function selectAllp(val) {
        var checks = document.getElementsByName("change_cat[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }

{/script}