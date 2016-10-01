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

{uniqueid prepend="gallery" assign="id"}

{css unique="photo-album" link="`$asset_path`css/photoalbum.css"}

{/css}

{$rel}
<div class="module photoalbum showall showall-tabbed">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add to the Top"|gettext text="Add Image"|gettext}
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
    {$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}
    <div id="photos-{$id}" class="">
        <ul class="nav nav-tabs" role="tablist">
            {foreach name=tabs from=$page->cats key=catid item=cat}
                <li role="presentation"{if $smarty.foreach.tabs.first} class="active"{/if}><a href="#tab{$smarty.foreach.tabs.iteration}-{$id}" role="tab" data-toggle="tab">{$cat->name}</a></li>
            {/foreach}
        </ul>
        <div class="tab-content">
            {foreach name=items from=$page->cats key=catid item=cat}
                <div id="tab{$smarty.foreach.items.iteration}-{$id}" role="tabpanel" class="tab-pane fade{if $smarty.foreach.items.first} in active{/if}">
                    <ul class="image-list">
                        {foreach from=$cat->records item=item}
                            <li style="width:{$config.pa_showall_thumbbox|default:"150"}px;height:{$config.pa_showall_thumbbox|default:"150"}px;">
                                {if $config.lightbox}
                                    {if $item->expCat[0]->title!= ""}
                                        {$group = $item->expCat[0]->title}
                                    {elseif $config.uncat!=''}
                                        {$group = $config.uncat}
                                    {else}
                                        {$group = 'Uncategorized'|gettext}
                                    {/if}
                                    {if $item->expFile[0]->image_width >= $item->expFile[0]->image_height}{$x="w"}{else}{$x="w"}{/if}
                                    <a class="colorbox" rel="lightbox[{$name}-{$group}]" href="{$smarty.const.PATH_RELATIVE}thumb.php?id={$item->expFile[0]->id}&{$x}={$config.pa_showall_enlarged}" title="{$item->alt|default:$item->title}">
                                {else}
                                    <a href="{link action=show title=$item->sef_url}" title="{$item->alt|default:$item->title}">
                                {/if}
                                    {img class="img-small" alt=$item->alt file_id=$item->expFile[0]->id w=$config.pa_showall_thumbbox|default:"150" h=$config.pa_showall_thumbbox|default:"150" far=TL f=jpeg q=$quality|default:75}
                                </a>
                                {permissions}
                                    <div class="item-actions">
                                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                            {if $myloc != $item->location_data}
                                                {if $permissions.manage}
                                                    {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                                {else}
                                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                                {/if}
                                            {/if}
                                            {icon action=edit record=$item title="Edit"|gettext|cat:" `$model_name`"}
                                        {/if}
                                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                                            {icon action=delete record=$item title="Delete"|gettext|cat:" `$model_name`"}
                                        {/if}
                                        {if $permissions.create}
                                            {icon class=add action=edit rank=$item->rank+1 title="Add another here"|gettext  text="Add After"|gettext}
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
    {loading}
</div>

{script unique="shadowbox-`$__loc->src`" jquery='jquery.colorbox'}
{literal}
    $('a.colorbox').colorbox({
        href: $(this).href,
        ref: $(this).rel,
        photo: true,
        maxWidth: "100%",
        close:'<i class="fa fa-close" aria-label="close modal"></i>',
        previous:'<i class="fa fa-chevron-left" aria-label="previous photo"></i>',
        next:'<i class="fa fa-chevron-right" aria-label="next photo"></i>',
    });
{/literal}
{/script}

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}