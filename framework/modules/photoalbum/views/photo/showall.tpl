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

{uniqueid prepend="gallery" assign="name"}

{css unique="photo-album" link="`$asset_path`css/photoalbum.css"}

{/css}

{if $config.lightbox}
{css unique="files-gallery" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/gallery-lightbox.css"}

{/css}
{/if}
{$rel}
<div class="module photoalbum showall">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add Image"|gettext}
			{/if}
			{if $permissions.manage == 1}
                {icon controller=expCat action=manage model='photo' text="Manage Categories"|gettext}
				{ddrerank items=$page->records model="photo" label="Images"|gettext}
			{/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {assign var=myloc value=serialize($__loc)}
    {assign var="cat" value="bad"}
    {pagelinks paginate=$page top=1}
    <ul class="image-list">
    {assign var=quality value=$config.quality|default:$smarty.const.THUMB_QUALITY}	
    {foreach from=$page->records item=record name=items}
        {if $cat != $record->expCat[0]->id && $config.usecategories}
            <h2 class="category">{if $record->expCat[0]->title!= ""}{$record->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2>
        {/if}
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
        </li>
        {assign var="cat" value=$record->expCat[0]->id}
    {/foreach}
    </ul>
    {pagelinks paginate=$page bottom=1}
</div>

{if $config.lightbox}
{script unique="shadowbox" yui3mods=1}
{literal}
EXPONENT.YUI3_CONFIG.modules = {
           'gallery-lightbox' : {
               fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/js/gallery-lightbox.js',
               requires : ['base','node','anim','selector-css3']
           }
     }

YUI(EXPONENT.YUI3_CONFIG).use('gallery-lightbox', function(Y) {
    Y.Lightbox.init();    
});
{/literal}
{/script}
{/if}
