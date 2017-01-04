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

{uniqueid prepend="gallery" assign="id"}

{css unique="photo-album" corecss="accordion" link="`$asset_path`css/photoalbum.css"}

{/css}

{$rel}

{uniqueid assign="id"}

<div class="module photoalbum showall showall-accordion">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add Image"|gettext}
                {icon class=add action=multi_add title="Quickly Add Many Images"|gettext text="Add Multiple Images"|gettext}
			{/if}
            {if $permissions.delete}
                {icon class=delete action=delete_multi title="Delete Many Images"|gettext text="Delete Multiple Images"|gettext onclick='null;'}
            {/if}
            {if $permissions.manage}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='photo' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='photo' text="Manage Categories"|gettext}
                {/if}
                {if $config.order == 'rank'}
                    {ddrerank items=$page->records model="photo" label="Images"|gettext}
                {/if}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    <div id="photo-{$id}" class="dashboard">
        {foreach name=items from=$page->cats key=catid item=cat}
            <div id="item{$catid}" class="panel">
                <div class="hd"><a href="#" class="{if $config.initial_view==2||($config.initial_view==3&&$smarty.foreach.items.iteration==1)}collapse{else}expand{/if}" title="{'Collapse/Expand'|gettext}"><{$config.item_level|default:'h2'}>{if $cat->name ==""}{if $config.uncat == ""}{'The List'|gettext}{else}{$config.uncat}{/if}{else}{$cat->name}{/if}</{$config.item_level|default:'h2'}></a></div>
                <div class="piece bd {if $config.initial_view==2||($config.initial_view==3&&$smarty.foreach.items.iteration==1)}expanded{else}collapsed{/if}">
                    <ul class="image-list">
                        {foreach from=$cat->records item=record}
                            <li style="width:{$config.pa_showall_thumbbox|default:"150"}px;height:{$config.pa_showall_thumbbox|default:"150"}px;">
                                {if $config.lightbox}
                                    {if $record->expCat[0]->title!= ""}
                                        {$group = $record->expCat[0]->title}
                                    {elseif $config.uncat!=''}
                                        {$group = $config.uncat}
                                    {else}
                                        {$group = 'Uncategorized'|gettext}
                                    {/if}
                                    {if $record->expFile[0]->image_width >= $record->expFile[0]->image_height}{$x="w"}{else}{$x="w"}{/if}
                                    <a rel="lightbox[{$name}-{$group}]" href="{$smarty.const.PATH_RELATIVE}thumb.php?id={$record->expFile[0]->id}&{$x}={$config.pa_showall_enlarged}" title="{$record->alt|default:$record->title}">
                                {else}
                                    <a href="{link action=show title=$record->sef_url}" title="{$record->alt}">
                                {/if}
                                    {img class="img-small" alt=$record->alt file_id=$record->expFile[0]->id w=$config.pa_showall_thumbbox|default:"150" h=$config.pa_showall_thumbbox|default:"150" far=TL f=jpeg q=$quality|default:75}
                                </a>
                                {permissions}
                                    <div class="item-actions">
                                        {if $permissions.edit || ($permissions.create && $record->poster == $user->id)}
                                            {if $myloc != $record->location_data}
                                                {if $permissions.manage}
                                                    {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                                                {else}
                                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                                {/if}
                                            {/if}
                                            {icon action=edit record=$record title="Edit"|gettext|cat:" `$model_name`"}
                                        {/if}
                                        {if $permissions.delete || ($permissions.create && $record->poster == $user->id)}
                                            {icon action=delete record=$record title="Delete"|gettext|cat:" `$model_name`"}
                                        {/if}
                                        {if $permissions.create}
                                            {icon class=add action=edit rank=$record->rank+1 title="Add another here"|gettext  text="Add After"|gettext}
                                        {/if}
                                    </div>
                                {/permissions}
                    `       </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        {/foreach}
    </div>
</div>

{script unique="expand-panels-`$id`" yui3mods="node,anim,gallery-lightbox"}
{literal}
EXPONENT.YUI3_CONFIG.modules = {
   'gallery-lightbox' : {
       fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/js/gallery-lightbox.js',
       requires : ['base','node','anim','selector-css3','lightbox-css']
    },
    'lightbox-css': {
        fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/css/gallery-lightbox.css',
        type: 'css'
   }
}

YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var panels = Y.all("#photo-{/literal}{$id}{literal}.dashboard .panel");
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
    Y.Lightbox.init();
});
{/literal}
{/script}
