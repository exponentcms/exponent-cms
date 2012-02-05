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



<div class="prod-listing">    
    <div class="image">
        <a href="{link controller=store action=showByTitle title=$listing->sef_url}">
            {if $listing->expFile.images[0]->id != ""}
                {img file_id=$listing->expFile.images[0]->id constraint=1 width=165 alt=$listing->title}
            {else}
                {'No Image'|gettext}
            {/if}
        </a>
    </div>

    {permissions}
    <div class="item-actions">
        {if $permissions.configure == 1 or $permissions.manage == 1}
            <a href="{link action=edit id=$listing->id}" title="{"Edit this entry"|gettext}">
                <img src="{$smarty.const.ICON_RELATIVE|cat:'edit.png'}" title="{"Edit this entry"|gettext}" alt="{"Edit this entry"|gettext}" />
            </a>
            {icon action=delete record=$listing title="Delete this product"|gettext}
        {/if}
    </div>
    {/permissions}

    <h3><a href="{link controller=store action=showByTitle title=$listing->sef_url}">{$listing->title}</a></h3>
    <div class="bodycopy">
    <strong class="date">{$listing->eventdate|date_format:"%a, %B %e"}</strong> - 
    {$listing->body}
    </div>
    <div class="price">{currency_symbol}{$listing->price|number_format:2}</div>
    <a href="{link controller=cart action=addItem product_id=$listing->id product_type=$listing->product_type}" class="exp-ecom-link addtocart">{'Register Today'|gettext} <span></span></a>
    {clear}
</div>
