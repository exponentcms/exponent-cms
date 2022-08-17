{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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
    <div id="text-{$id}" class="dashboard">
        {foreach from=$items item=item name=items}
            <div id="item{$item->id}" class="panel item{if !$item->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}">
                <div class="hd"><a href="#" class="{if $config.initial_view==2||($config.initial_view==3&&$smarty.foreach.items.iteration==1)}collapse{else}expand{/if}" title="{'Collapse/Expand'|gettext}"><h2>{if $item->title ==""}&#160;{else}{$item->title}{/if}</h2></a></div>
                <div class="piece bd {if $config.initial_view==2||($config.initial_view==3&&$smarty.foreach.items.iteration==1)}expanded{else}collapsed{/if}">
                    <ul><li>
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
                    </li></ul>
                    {clear}
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

{script unique="expand-panels-`$id`" yui3mods="node,anim"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var panels = Y.all("#text-{/literal}{$id}{literal}.dashboard .panel");
    var expandHeight = [];
    var exclusiveExp = {/literal}{if $config.initial_view==1||$config.initial_view==3}true{else}false{/if}{literal};
    var action = function(e){
        e.halt();
        var pBody = e.target.ancestor('.panel').one('.bd');
        var pID = e.target.ancestor('.panel').getAttribute('id');
        var savedState = e.target.ancestor('.panel').one('.hd a').getAttribute("class");
        var cfg = {
            node: pBody,
            duration: 0.5,
            easing: Y.Easing.easeOut
        }

        if (exclusiveExp) {
            panels.each(function(n,k){
                var cfg = {
                    node: n.one('.bd'),
                    duration: 0.5,
                    easing: Y.Easing.easeOut
                }
                n.one('.hd a').replaceClass('collapse','expand');
                n.one('.bd').replaceClass('expanded','collapsed');
                cfg.to = { height: 0 };
                var anim = new Y.Anim(cfg);
                anim.run();
            });
        }

        if (savedState=="collapse") {
            cfg.to = { height: 0 };
            cfg.from = { height: expandHeight[pID] };
            pBody.setStyle('height',expandHeight[pID]+"px");
            pBody.replaceClass('expanded','collapsed');
            e.target.ancestor('.panel').one('.hd a').replaceClass('collapse','expand');
        } else {
            pBody.setStyle('height',0);
            cfg.from = { height: 0 };
            cfg.to = { height: expandHeight[pID] };
            pBody.replaceClass('collapsed','expanded');
            e.target.ancestor('.panel').one('.hd a').replaceClass('expand','collapse');
        }
        var anim = new Y.Anim(cfg);
        anim.run();
    }
    panels.each(function(n,k){
        n.delegate('click',action,'.hd a');
//            n.one('.hd a').replaceClass('collapse','expand');
//            n.one('.bd').addClass('collapsed');
        expandHeight[n.get('id')] = n.one('.bd ul').get('offsetHeight');
    });
});
{/literal}
{/script}
