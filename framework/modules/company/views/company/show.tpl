{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

<div class="module company show">
    <h1>Products from {$record->title}</h1>
    {permissions}
        {if $permissions.edit == 1}
            {icon img='edit.png' action=edit id=$record->id title="Edit `$record->title`"}
        {/if}
        {if $permissions.delete == 1}
            {icon img='delete.png' action=delete id=$record->id title="delete `$record->title`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
        {/if}
    {/permissions}
    {$page->links}

    {foreach from=$page->records item=result}
        {*if $result->canview == 1*}
        <div class="showwrapper">
            <div class="prod-img show-img">
                <a href="{link controller=store action=showByTitle title=$result->sef_url}">{img file_id=$result->expFile.mainimage[0]->id w=60 h=60}</a>
            </div>
            <div class="item {cycle values="odd,even"} showbody">
                <span class="showtitle"><a href="{link controller=store action=showByTitle title=$result->sef_url}">{$result->title}{if $result->model}, SKU: {$result->model}{/if}</a></span>
                {if $result->body != ""}<br /><span class="summary">{$result->body|strip_tags|truncate:240}</span>{/if}
            </div>
            <div class="showrightcol"> 
                <div class="showprice">
                {if $result->availability_type == 3}       
                    Call for Price
                {else}                   
                    {if $result->use_special_price}
                        <span style="font-size:14px; text-decoration: line-through;">{currency_symbol}{$result->base_price|number_format:2}</span>
                        <span style="color:red;">{currency_symbol}{$result->special_price|number_format:2}</span>
                    {else}
                        {currency_symbol}{$result->base_price|number_format:2}
                    {/if}
                {/if}
                </div>
                <div style="text-align: right;">
                    <a href="{link controller=store action=showByTitle title=$result->sef_url}" class="exp-ecom-link view-item" rel="nofollow"><strong><em>View Item</em></strong></a>   
                </div> 
            </div>
            {clear}
        </div>
        {*/if*}
    {/foreach}  
    {$page->links}

</div>
