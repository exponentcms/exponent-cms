{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
    {if $moduletitle != ""}<h1>{$moduletitle}</h1>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add to the top" text="Add Image"}
			{/if}
			{if $permissions.manage == 1}
				{ddrerank items=$page->records model="photo" label="Images"}
			{/if}
		</div>
    {/permissions}
    {pagelinks paginate=$page top=1}
    <ul class="image-list">
    {assign var=quality value=$config.quality|default:$smarty.const.THUMB_QUALITY}	
    {foreach from=$page->records item=record name=items}
        <li style="width:{$config.pa_showall_thumbbox|default:"150"}px;height:{$config.pa_showall_thumbbox|default:"150"}px;">
            
            {if $config.lightbox}
            {if $record->expFile[0]->width >= $record->expFile[0]->height}{assign var=x value="w"}{else}{assign var=x value="w"}{/if}
            <a rel="lightbox[{$name}]" href="{$smarty.const.URL_FULL}thumb.php?id={$record->expFile[0]->id}&{$x}={$config.pa_showall_enlarged}" title="{$record->title|default:$record->expFile[0]->title}">
            {else}
            <a href="{link action=show title=$record->sef_url}" title="{$record->title|default:$record->expFile[0]->title}">
            {/if}
            
                {img class="img-small" alt=$record->alt|default:$record->expFile[0]->alt file_id=$record->expFile[0]->id w=$config.pa_showall_thumbbox|default:"150" h=$config.pa_showall_thumbbox|default:"150" zc=1 q=$quality|default:75}            
            </a>
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit == 1}
                        {icon action=edit record=$record title="Edit `$modelname`"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete record=$record title="Delete `$modelname`"}
                    {/if}
                    {if $permissions.create == 1}
						{icon class=add action=edit rank=$text->rank+1 title="Add another `$modelname` after this one" text="Add After"}
                    {/if}
                </div>
            {/permissions}
        </li>
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
