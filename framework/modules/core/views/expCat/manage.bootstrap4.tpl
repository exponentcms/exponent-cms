{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

{if $model == 'file'}
    {$catnames = "Folders"|gettext}
    {$catname = "Folder"|gettext}
{else}
    {$catnames = "Categories"|gettext}
    {$catname = "Category"|gettext}
{/if}
<div class="module expcat manage">
    <div class="info-header">
        <div class="related-actions">
            {if !empty($page)}
                {help text="Get Help with"|gettext|cat:" "|cat:("Managing"|gettext|cat:" `$catnames`") module="manage-categories"}
            {elseif $model == 'file'}
                {help text="Get Help with"|gettext|cat:" "|cat:("Managing"|gettext|cat:" `$catnames`") module="manage-file-folders"}
            {else}
                {help text="Get Help with"|gettext|cat:" "|cat:("Managing"|gettext|cat:" `$catnames`") module="manage-site-categories"}
            {/if}
        </div>
        <h2>{"Manage"|gettext|cat:" `$catnames`"}</h2>
    </div>
	{permissions}
    	{if $permissions.create}
            {if !empty($page)}
                {icon class="add" controller=$model_name action=edit model=$page->model rank=1 text="Create a new"|gettext|cat:" `$catname`"}
            {else}
                {icon class="add" controller=$model_name action=edit rank=1 text="Create a new"|gettext|cat:" `$catname`"}
            {/if}
    	{/if}
    {/permissions}
    <div id="{$id}" class="">
        <ul class="nav nav-tabs" role="tablist">
            {if !empty($page)}
                <li role="presentation" class="nav-item"><a href="#tab0" class="nav-link active" role="tab" data-toggle="tab">{$page->model|capitalize} {'Items'|gettext}</a></li>
            {/if}
            {foreach name=tabs from=$cats->modules key=moduleid item=module}
                <li role="presentation"  class="nav-item"><a href="#tab{$smarty.foreach.tabs.iteration}" class="nav-link{if $smarty.foreach.tabs.first && empty($page)} active{/if}" role="tab" data-toggle="tab">{$moduleid|capitalize} {$catnames}</a></li>
            {foreachelse}
                <li role="presentation" class="nav-item"><a href="#tab0" class="nav-link active" role="tab" data-toggle="tab">{if $model == 'file'}{'No Folders Defined'|gettext}{else}{'No Categories Defined'|gettext}{/if}</a></li>
            {/foreach}
        </ul>
        <div class="tab-content">
            {if !empty($page)}
                <div id="tab0" role="tabpanel" class="tab-pane fade show active">
                    <h3>{'Change'|gettext} {if $model == 'file'}{'File Folders'|gettext}{else}{$page->model|capitalize} {'Item Categories'|gettext}{/if}</h3>
                    {form action=change_cats}
                        {control type=hidden name=mod value=$page->model}
                        {$page->links}
                        <table class="exp-skin-table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type='checkbox' name='checkallp' title="{'Select All/None'|gettext}" onchange="selectAllp(this.checked)">
                                    </th>
                                    <th>
                                        {"Item"|gettext}
                                    </th>
                                    <th>
                                        {$catname}
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
                        {$page->links}
                        <blockquote>{'Select the item(s) to change, then select the new'|gettext|cat:" `$catname`"}</blockquote>
                        {control type="dropdown" name=newcat label="Module"|gettext|cat:" `$catnames`" items=$catlist}
                        {control type=buttongroup submit="Change Selected Items"|gettext|cat:" `$catname`" cancel="Cancel"|gettext returntype="viewable"}
                    {/form}
                </div>
            {/if}
            {foreach name=items from=$cats->modules key=moduleid item=module}
                <div id="tab{$smarty.foreach.items.iteration}" role="tabpanel" class="tab-pane fade{if $smarty.foreach.items.first && empty($page)} show active{/if}">
                    {if $permissions.manage}
                        {*{ddrerank id=$moduleid items=$cats->records model="expCat" module=$moduleid label=$moduleid|cat:' '|cat:"Categories"|gettext}*}
                        {ddrerank id=$moduleid items=$module model="expCat" module=$moduleid label=$moduleid|cat:' '|cat:$catnames}
                    {/if}
                    {*{$cats->links}*}
                    <table class="exp-skin-table">
                        <thead>
                            <tr>
                                <th>
                                {$catname} {"Name"|gettext}
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
                                            {if $permissions.edit}
                                                {icon controller=$controller action=edit record=$listing title="Edit this"|gettext|cat:" `$catname`"}
                                            {/if}
                                            {if $permissions.delete}
                                                {icon controller=$controller action=delete record=$listing title="Delete this"|gettext|cat:" `$catname`" onclick="return confirm('"|cat:("Are you sure you want to delete this?"|gettext)|cat:"');"}
                                            {/if}
                                        {/permissions}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                    {*{$cats->links}*}
                </div>
            {foreachelse}
                <div id="tab0" role="tabpanel" class="tab-pane fade show active">
                    {if $model == 'file'}{'No Folders Defined'|gettext}{else}{'No Categories Defined'|gettext}{/if}
                </div>
            {/foreach}
        </div>
    </div>
    {*<div class="loadingdiv">{'Loading'|gettext}</div>*}
    {loading}
</div>
{clear}

{script unique="managecats" jquery=1 bootstrap="tab"}
{literal}
    $('.loadingdiv').remove();

    function selectAllp(val) {
        var checks = document.getElementsByName("change_cat[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/literal}
{/script}