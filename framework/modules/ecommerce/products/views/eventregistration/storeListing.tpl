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

<div class="prod-listing">    
    <div class="image">
        <a href="{link controller=store action=show title=$listing->sef_url}">
            {if $listing->expFile.mainimage[0]->id != ""}
                {img file_id=$listing->expFile.mainimage[0]->id constraint=1 w=$config.listingwidth|default:140 h=$config.listingheight|default:150 alt=$listing->title}
            {else}
                {img src="`$asset_path`images/no-image.jpg" constraint=1 w=$config.listingwidth|default:140 h=$config.listingheight|default:150 alt="'No Image Available'|gettext"}
            {/if}
        </a>
    </div>
   {permissions}
    <div class="item-actions">
        {if $permissions.edit || ($permissions.create && $listing->poster == $user->id)}
            {icon action=edit id=$listing->id title="Edit this entry"|gettext}
            {icon action=copyProduct class="copy" record=$listing text="Copy" title="Copy `$listing->title` "}
        {/if}
        {if $permissions.delete || ($permissions.create && $listing->poster == $user->id)}
            {icon action=delete record=$listing title="Delete this product"|gettext}
        {/if}
    </div>
    {/permissions}
    <h3><a href="{link controller=store action=show title=$listing->sef_url}">{$listing->title}</a></h3>
    <div class="bodycopy">
        <strong class="date">{$listing->eventdate|format_date:"%a, %B %e"}</strong> -
        {$listing->body}
    </div>
    <div class="price">{$listing->price|currency}</div>
    <a href="{link controller=cart action=addItem product_id=$listing->id product_type=$listing->product_type}" class="exp-ecom-link addtocart">{'Register for this Event'|gettext} <span></span></a>
    {clear}
</div>
