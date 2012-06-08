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

{uniqueid prepend="gallery" assign="id"}

{css unique="photo-album" link="`$asset_path`css/photoalbum.css"}

{/css}

{if $config.lightbox}
{css unique="files-gallery" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/gallery-lightbox.css"}

{/css}
{/if}
{$rel}
<div class="module photoalbum showall showall-tabbed">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{/if}
    {if $config.enable_rss == true}
        <a class="rsslink" href="{rsslink}" title="{'Subscribe to'|gettext} {$config.feed_title}"></a>
    {/if}
    {if $moduletitle && !$config.hidemoduletitle}{$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add to the Top"|gettext text="Add Image"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {icon class="manage" controller=expTag action=manage text="Manage Tags"|gettext}
                {icon controller=expCat action=manage model='photoalbum' text="Manage Categories"|gettext}
            {/if}
			{if $permissions.manage == 1 && $rank == 1}
				{ddrerank items=$page->records model="photoalbum" label="Images"|gettext}
			{/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {assign var=myloc value=serialize($__loc)}
    {assign var=quality value=$config.quality|default:$smarty.const.THUMB_QUALITY}
    <div id="{$id}" class="yui-navset exp-skin-tabview hide">
        <ul>
            {foreach name=tabs from=$page->cats key=catid item=cat}
                <li><a href="#tab{$smarty.foreach.items.iteration}">{$cat->name}</a></li>
            {/foreach}
        </ul>
        <div>
            {foreach name=items from=$page->cats key=catid item=cat}
                <div id="tab{$smarty.foreach.items.iteration}">
                    <ul class="image-list">
                        {foreach from=$cat->records item=record}
                            <li style="width:{$config.pa_showall_thumbbox|default:"150"}px;height:{$config.pa_showall_thumbbox|default:"150"}px;">
                                {if $config.lightbox}
                                    {if $record->expFile[0]->width >= $record->expFile[0]->height}{assign var=x value="w"}{else}{assign var=x value="w"}{/if}
                                    <a rel="lightbox[{$name}]" href="{$smarty.const.PATH_RELATIVE}thumb.php?id={$record->expFile[0]->id}&{$x}={$config.pa_showall_enlarged}" title="{$record->title|default:$record->expFile[0]->title}">
                                {else}
                                    <a href="{link action=show title=$record->sef_url}" title="{$record->title|default:$record->expFile[0]->title}">
                                {/if}
                                    {img class="img-small" alt=$record->alt|default:$record->expFile[0]->alt file_id=$record->expFile[0]->id w=$config.pa_showall_thumbbox|default:"150" h=$config.pa_showall_thumbbox|default:"150" zc=1 q=$quality|default:75}
                                </a>
                                {permissions}
                                    <div class="item-actions">
                                        {if $permissions.edit == 1}
                                            {if $myloc != $record->location_data}
                                                {if $permissions.manage == 1}
                                                    {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                                                {else}
                                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                                {/if}
                                            {/if}
                                            {icon action=edit record=$record title="Edit"|gettext|cat:" `$modelname`"}
                                        {/if}
                                        {if $permissions.delete == 1}
                                            {icon action=delete record=$record title="Delete"|gettext|cat:" `$modelname`"}
                                        {/if}
                                        {if $permissions.create == 1}
                                            {icon class=add action=edit rank=$slide->rank+1 title="Add another slide here"|gettext  text="Add After"|gettext}
                                        {/if}
                                    </div>
                                {/permissions}
                    `       </li>
                        {/foreach}
                    </ul>
                </div>
            {/foreach}
        </div>
    </div>
    <div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{script unique="`$id`" yui3mods="1"}
{literal}
    EXPONENT.YUI3_CONFIG.modules = {
       'gallery-lightbox' : {
           fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/js/gallery-lightbox.js',
           requires : ['base','node','anim','selector-css3']
       }
    }

	YUI(EXPONENT.YUI3_CONFIG).use('tabview','gallery-lightbox', function(Y) {
	    var tabview = new Y.TabView({srcNode:'#{/literal}{$id}{literal}'});
	    tabview.render();
		Y.one('#{/literal}{$id}{literal}').removeClass('hide');
		Y.one('.loadingdiv').remove();
        Y.Lightbox.init();
	});
{/literal}
{/script}
