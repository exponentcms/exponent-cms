{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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

{css unique="accordion" corecss="accordion"}

{/css}
{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.css"}

{/css}

{uniqueid assign="id"}

<div class="module filedownload showall showall-accordion">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
            {if $permissions.manage}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='filedownload' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='filedownload' text="Manage Categories"|gettext}
                {/if}
                {*{if $rank == 1}*}
                {if $config.order == 'rank'}
                    {ddrerank items=$page->records model="filedownload" label="Downloadable Items"|gettext}
                {/if}
           {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    {$myloc=serialize($__loc)}
    <div id="file-{$id}" class="dashboard">
        {foreach name=items from=$page->cats key=catid item=cat}
            <div id="item{$catid}" class="panel">
                <div class="hd"><a href="#" class="{if $config.initial_view==2||($config.initial_view==3&&$smarty.foreach.items.iteration==1)}collapse{else}expand{/if}" title="{'Collapse/Expand'|gettext}"><{$config.item_level|default:'h2'}>{if $cat->name ==""}{if $config.uncat == ""}{'The List'|gettext}{else}{$config.uncat}{/if}{else}{$cat->name}{/if}</{$config.item_level|default:'h2'}></a></div>
                <div class="piece bd {if $config.initial_view==2||($config.initial_view==3&&$smarty.foreach.items.iteration==1)}expanded{else}collapsed{/if}">
                    <ul>
                        {foreach from=$cat->records item=item}
                            <li>
                                {exp_include file='filedownloaditem.tpl'}
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        {/foreach}
    </div>
</div>

{script unique="expand-panels-`$id`" yui3mods="node,anim"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        var panels = Y.all("#file-{/literal}{$id}{literal}.dashboard .panel");
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

{if $config.show_player}
    {script unique="mediaelement-src" jquery="1" src="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelement-and-player.min.js"}
    {/script}

    {script unique="filedownload-`$id`"}
        $('audio,video').mediaelementplayer({
        	success: function(player, node) {
        		$('#' + node.id + '-mode').html('mode: ' + player.pluginType);
        	}
        });
    {/script}
{/if}
