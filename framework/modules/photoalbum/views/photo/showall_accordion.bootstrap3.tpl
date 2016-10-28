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
    <div id="photo-{$id}" class="panel-group">
        {foreach name=items from=$page->cats key=catid item=cat}
            <div id="item{$catid}" class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><a data-toggle="collapse" data-parent="#photo-{$id}" href="#collapse-{$catid}" title="{'Collapse/Expand'|gettext}"><{$config.item_level|default:'h2'}>{if $cat->name ==""}{if $config.uncat == ""}{'The List'|gettext}{else}{$config.uncat}{/if}{else}{$cat->name}{/if}</{$config.item_level|default:'h2'}></a></div>
                </div>
                <div id="collapse-{$catid}" class="panel-collapse collapse{if ($smarty.foreach.items.iteration==1 && $config.initial_view == '3') || $config.initial_view == '2'} in{/if}">
                    <div class="piece panel-body">
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
                                        <a class="colorbox" rel="lightbox[{$name}-{$group}]" href="{$smarty.const.PATH_RELATIVE}thumb.php?id={$record->expFile[0]->id}&{$x}={$config.pa_showall_enlarged}" title="{$record->alt|default:$record->title}">
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
            </div>
        {/foreach}
    </div>
</div>

{if $config.lightbox}
{script unique="shadowbox-`$id`" jquery='jquery.colorbox'}
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
{/if}

{script unique="accordion" bootstrap="collapse,transition"}
{literal}

{/literal}
{/script}
