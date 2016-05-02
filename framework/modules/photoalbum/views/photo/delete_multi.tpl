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

{uniqueid prepend="gallery" assign="name"}

{css unique="photo-album" link="`$asset_path`css/photoalbum.css"}

{/css}

<div class="module photoalbum showall delete-multi">
    <h1>{'Delete Multiple Photo Items'|gettext}</h1>
    <div id="{$name}list">
        {$cat="bad"}
        <ul class="image-list">
            {$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}
            {form action="delete_multi_act"}
                {pagelinks paginate=$page top=1}
                {foreach from=$page->records item=item name=items}
                    {if $cat !== $item->expCat[0]->id && $config.usecategories}
                        <h2 class="category">{if $item->expCat[0]->title!= ""}{$item->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2>
                    {/if}
                    <li style="width:64px;height:48px;">
                        {img class="img-small" alt=$item->alt|default:$item->expFile[0]->alt file_id=$item->expFile[0]->id w=48 h=48 far=TL f=jpeg q=$quality|default:75}
                        {$item->title}
                        {control type="checkbox" class="selectbox" name="pic[`$item->id`]" label="Delete"|gettext|cat:"?" value=1}
                    </li>
                    {$cat=$item->expCat[0]->id}
                {/foreach}
                {pagelinks paginate=$page bottom=1}
                <a class="selectall" href="#" id="sa_conts" onclick="selectAll(1); return false;">{"Select All"|gettext}</a> / <a class="selectnone" href="#" id="sn_conts" onclick="selectAll(0); return false;">{"Select None"|gettext}</a>
                {br}{br}
                {control type=buttongroup submit="Delete Selected Photo Items"|gettext cancel="Cancel"|gettext}
            {/form}
        </ul>
    </div>
</div>

{script unique="selectall"}
{literal}
    function selectAll(val) {
        var checks = document.getElementsByClassName('selectbox');
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/literal}
{/script}
