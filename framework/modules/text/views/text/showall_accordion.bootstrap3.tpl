{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
 
{css unique="accordion" corecss="accordion"}

{/css}

{uniqueid assign="id"}

<div class="module text showall-accordion">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon class=add action=edit rank=1 text="Add text at the top"|gettext}
            {/if}
            {if $permissions.manage}
                {ddrerank items=$items model="text" label="Text Items"|gettext}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
    <div id="text-{$id}" class="panel-group">
        {foreach from=$items item=item key=textid name=items}
            <div id="item{$item->id}" class="panel panel-default item{if !$item->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}">
                <div class="panel-heading">
                    <div class="panel-title"><a data-toggle="collapse" data-parent="#text-{$id}" href="#collapse-{$textid}" title="{'Collapse/Expand'|gettext}"><{$config.item_level|default:'h2'}>{if $item->title ==""}&#160;{else}{$item->title}{/if}</{$config.item_level|default:'h2'}></a></div>
                </div>
                <div id="collapse-{$textid}" class="panel-collapse collapse{if ($smarty.foreach.items.iteration==1 && $config.initial_view == '3') || $config.initial_view == '2'} in{/if}">
                    <div class="piece panel-body">
                        {permissions}
                            <div class="item-actions">
                                {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                    {if $item->revision_id > 1 && $smarty.const.ENABLE_WORKFLOW}<span class="revisionnum approval" title="{'Viewing Revision #'|gettext}{$item->revision_id}">{$item->revision_id}</span>{/if}
                                    {if $myloc != $item->location_data}
                                        {if $permissions.manage}
                                            {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                        {else}
                                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                        {/if}
                                    {/if}
                                    {icon action=edit record=$item}
                                {/if}
                                {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                                    {icon action=delete record=$item}
                                {/if}
                                {if !$item->approved && $smarty.const.ENABLE_WORKFLOW && $permissions.approve && ($permissions.edit || ($permissions.create && $item->poster == $user->id))}
                                    {icon action=approve record=$item}
                                {/if}
                            </div>
                        {/permissions}
                        <div class="bodycopy">
                            {if $config.ffloat != "Below"}
                                {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item}
                            {/if}
                            {$item->body}
                            {if $config.ffloat == "Below"}
                                {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item}
                            {/if}
                        </div>
                        {clear}
                    </div>
                </div>
            </div>
            {permissions}
                <div class="module-actions">
                    {if $permissions.create}
                        {icon class=add action=edit rank=$item->rank+1 text="Add more text here"|gettext}
                    {/if}
                </div>
            {/permissions}
        {/foreach}
    </div>
</div>

{script unique="accordion" bootstrap="collapse,transition"}
{literal}

{/literal}
{/script}
