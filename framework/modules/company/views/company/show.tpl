{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div class="module company show">
    <{$config.heading_level|default:'h1'}>
        {if $record->expFile.logo[0]->id}
            {img file_id=$record->expFile.logo[0]->id w=64 alt="Image of `$record->title`" title="`$record->title`" class="large-img" id="enlarged-image"}
        {/if}
        {'Products from'|gettext} {$record->title}
    </{$config.heading_level|default:'h1'}>
    {permissions}
        {if $permissions.edit || ($permissions.create && $record->poster == $user->id)}
            {icon img='edit.png' action=edit id=$record->id title="Edit"|gettext|cat:" `$record->title`"}
        {/if}
        {if $permissions.delete || ($permissions.create && $record->poster == $user->id)}
            {icon img='delete.png' action=delete id=$record->id title="Delete"|gettext|cat:" `$record->title`"}
        {/if}
    {/permissions}
    {$page->links}
    {foreach from=$page->records item=result}
        {*if $result->canview == 1*}
        <div class="showwrapper">
            <div class="prod-img show-img">
                <a href="{link controller=store action=show title=$result->sef_url}">{img file_id=$result->expFile.mainimage[0]->id w=60 h=60}</a>
            </div>
            <div class="item {cycle values="odd,even"} showbody">
                <span class="showtitle"><a href="{link controller=store action=show title=$result->sef_url}">{$result->title}{if $result->model}, SKU: {$result->model}{/if}</a></span>
                {if $result->body != ""}<br /><span class="summary">{$result->body|strip_tags|truncate:240}</span>{/if}
            </div>
            <div class="showrightcol"> 
                <div class="showprice">
                {if $result->availability_type == 3}       
                    {'Call for Price'|gettext}
                {else}                   
                    {if $result->use_special_price}
                        <span style="font-size:14px; text-decoration: line-through;">{$result->base_price|currency}</span>
                        <span style="color:red;">{$result->special_price|currency}</span>
                    {else}
                        {$result->base_price|currency}
                    {/if}
                {/if}
                </div>
                <div style="text-align: right;">
                    <a href="{link controller=store action=show title=$result->sef_url}" class="exp-ecom-link view-item" rel="nofollow"><strong><em>{'View Item'|gettext}</em></strong></a>
                </div> 
            </div>
            {clear}
        </div>
        {*/if*}
    {/foreach}  
    {$page->links}

</div>
