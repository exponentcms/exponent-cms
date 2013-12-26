{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{$rel}
<div class="module photoalbum galleries showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add Image"|gettext}
                {icon class=add action=multi_add title="Quickly Add Many Images"|gettext text="Add Multiple Images"|gettext}
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
    {foreach name=items from=$page->cats key=catid item=cat}
        <a href="{link action=$config.landing|default:showall src=$page->src gallery=$catid}" title="{'View this gallery'|gettext}"><h2 class="category">{$cat->name}</h2></a>
        <ul class="image-list">
        {$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}
            <li style="width:{$config.pa_showall_thumbbox|default:"150"}px;height:{$config.pa_showall_thumbbox|default:"150"}px;">
                <a href="{link action=$config.landing|default:showall src=$page->src gallery=$catid}" title="{'View this gallery'|gettext}">
                    {img class="img-small" alt=$cat->records[0]->alt file_id=$cat->records[0]->expFile[0]->id w=$config.pa_showall_thumbbox|default:"150" h=$config.pa_showall_thumbbox|default:"150" far=TL f=jpeg q=$quality|default:75}
                </a>
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $records[0]->poster == $user->id)}
                            {if $myloc != $cat->records[0]->location_data}
                                {if $permissions.manage}
                                    {icon action=merge id=$cat->records[0]->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$cat->records[0] title="Edit"|gettext|cat:" `$modelname`"}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $records[0]->poster == $user->id)}
                            {icon action=delete record=$cat->records[0] title="Delete"|gettext|cat:" `$modelname`"}
                        {/if}
                        {if $permissions.create}
                            {icon class=add action=edit rank=$cat->records[0]->rank+1 title="Add another here"|gettext  text="Add After"|gettext}
                        {/if}
                    </div>
                {/permissions}
            </li>
            {*<li>*}
                {*<a href="{link action=$config.landing|default:showall src=$page->src gallery=$catid}" title="{'View this gallery'|gettext}"><h3>{$cat->records[0]->title}</h3></a>*}
            {*</li>*}
            {*<li>*}
                {*<div class="bodycopy">*}
                    {*{$cat->records[0]->body}*}
                {*</div>*}
            {*</li>*}
        </ul>
    {/foreach}
</div>

{if $config.lightbox}
{script unique="shadowbox" yui3mods=1}
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

    YUI(EXPONENT.YUI3_CONFIG).use('gallery-lightbox', function(Y) {
        Y.Lightbox.init();
    });
{/literal}
{/script}
{/if}
