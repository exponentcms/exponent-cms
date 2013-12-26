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

    {$myloc=serialize($__loc)}
    {$cat="bad"}
    {pagelinks paginate=$page top=1}
    <ul class="image-list">
    {$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}
    {foreach from=$page->records item=record name=items}
        {if $cat !== $record->expCat[0]->id && $config.usecategories}
            <a href="{link action=$config.landing|default:showall src=$page->src gallery=$record->expCat[0]->id}" title='View this gallery'|gettext><h2 class="category">{if $record->expCat[0]->title!= ""}{$record->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2></a>
        {/if}
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
                <a href="{link action=show title=$record->sef_url}" title="{$record->alt|default:$record->title}">
            {/if}
                {img class="img-small" alt=$record->alt|default:$record->expFile[0]->alt file_id=$record->expFile[0]->id w=$config.pa_showall_thumbbox|default:"150" h=$config.pa_showall_thumbbox|default:"150" far=TL f=jpeg q=$quality|default:75}
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
                        {icon action=edit record=$record title="Edit"|gettext|cat:" `$modelname`"}
                    {/if}
                    {if $permissions.delete || ($permissions.create && $record->poster == $user->id)}
                        {icon action=delete record=$record title="Delete"|gettext|cat:" `$modelname`"}
                    {/if}
                    {if $permissions.create}
                        {icon class=add action=edit rank=$slide->rank+1 title="Add another here"|gettext  text="Add After"|gettext}
                    {/if}
                </div>
            {/permissions}
        </li>
        {$cat=$record->expCat[0]->id}
    {/foreach}
    </ul>
    {pagelinks paginate=$page bottom=1}
