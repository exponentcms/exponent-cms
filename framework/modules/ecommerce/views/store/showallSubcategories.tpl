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

<div class="module store showall-subcategories">
    {assign var=depth value=0}
    <h1>{$moduletitle|default:""}</h1>
    <div id="catnav">
        <ul>
            <li><a href="{link controller=store action=showall}">Browse all Products</a></li>
            {foreach from=$ancestors item=ancestor name=path}
                {math equation="x*10" x=$smarty.foreach.path.iteration assign=depth} 
                <li style="margin-left: {$depth}px">
                    {if $ancestor->id != $category->id}
                        <a href="{link controller=store action=showall title=$ancestor->sef_url}">{$ancestor->title}</a>
                    {else}
                        <strong>{$ancestor->title}</strong>
                    {/if}
                </li>
                {/foreach}      
            {math equation="x+10" x=$depth assign=childdepth}   
            {foreach from=$categories item=category}
                <li style="margin-left: {$childdepth}px">
                    <a href="{link controller=store action=showall title=$category->sef_url}">{$category->title}</a> <span class="productsincategory">({$category->product_count})</span>
                </li>
            {/foreach}          
            {br}
            {if $user->is_admin == 1 || $user->is_acting_admin}
                <li><a href="{link controller=store action=showallUncategorized}">Show uncategoried products</a></li>
            {/if}
        </ul>
    </div>
</div>
