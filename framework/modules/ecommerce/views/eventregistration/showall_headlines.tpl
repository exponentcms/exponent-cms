{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
 

<div class="module events headlines">
    {if $moduletitle && !$config.hidemoduletitle}<h2>{$moduletitle}</h2>{/if}

    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {assign var=myloc value=serialize($__loc)}
    <ul>
    {foreach name=items from=$page item=item}
        {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}

        <li>
            <a class="link" href="{link action=showByTitle title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">
                {$item->title}
            </a>
            
            {if !$config.hidedate}
                <em class="date">{$item->publish_date|format_date}</em>
            {/if}
            
            {if $item->isRss != true}
                {permissions}
                <div class="item-actions">
                    {if $permissions.edit == true}
                        {icon controller="store" action=edit record=$item}
                    {/if}
                    {if $permissions.delete == true}
                        {icon controller="store" action=delete record=$item}
                    {/if}
                </div>
                {/permissions}
            {/if}
        </li>
        {/if}
    {/foreach}
    </ul>
   
</div>