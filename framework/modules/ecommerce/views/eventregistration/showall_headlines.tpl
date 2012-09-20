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
{css unique="event-listings" link="`$asset_path`css/storefront.css" corecss="button,tables"}
{literal}
	.headlines .events {
		overflow: hidden;
	}
	
	.headlines .events .event-image {
		float: left;
		width: 125px;
		margin-right: 20px;
	}
	
	.headlines .events .event-info {
		float: left;
		width: 550px;
	}
{/literal}
{/css}

<div class="module events headlines">
    {if $moduletitle && !$config.hidemoduletitle}<h2>{$moduletitle}</h2>{/if}

    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {assign var=myloc value=serialize($__loc)}
    <ul>
    {foreach name=items from=$page->records item=item}
        {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}

        <li>
            <a class="link" href="{link action=showByTitle title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">
                {$item->title}
            </a>
            <div class="events">
				<div class="event-image">
					 <a href="{link action=showByTitle title=$item->sef_url}">
					{img file_id=$item->expFile.images[0]->id w=125 alt=$item->image_alt_tag|default:"Image of `$item->title`" title="`$item->title`"}
					</a>
				</div>
			
			
				<div class="event-info">
					<em class="date">{$item->eventdate|date_format:"%A, %B %e, %Y"}</em>
					   <p>{$item->body|truncate:175:"..."}</p>
					<a href="{link action=showByTitle title=$item->sef_url}" class="readmore">Read More...</a>
					
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
				</div>
			</div>
        </li>
        {/if}
    {/foreach}
    </ul>
   
</div>