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
	<h1>{"Manage Categories"|gettext}</h1>
	{permissions}
    	{if $permissions.create == 1}
    		<a class="add" href="{link controller=$model_name action=create rank=1}">{"Create a new Category"|gettext}</a>
    	{/if}
    {/permissions}

    <div id="{$id}" class="yui-navset exp-skin-tabview hide">
        <ul>
            {foreach name=tabs from=$page->modules key=moduleid item=module}
                <li><a href="#tab{$smarty.foreach.items.iteration}">{$moduleid|capitalize}</a></li>
            {/foreach}
        </ul>
        <div>
            {foreach name=items from=$page->modules key=moduleid item=module}
                <div id="tab{$smarty.foreach.items.iteration}">
                    {if $permissions.manage == 1}
                        {ddrerank id=$moduleid items=$page->records model="expCat" module=$moduleid label=$moduleid|cat:' '|cat:"Categories"|gettext}
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
                            {*{foreach from=$page->records item=listing}*}
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
{/script}