{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="module store upcoming-events">
    {if $moduletitle != ""}<h1>{$moduletitle}</h1>{/if}
    
    <ul>
    {foreach name=uce from=$page->records item=item}
        {if $smarty.foreach.uce.iteration <= 3}
        <li>
            <a href="{link section=10 controller=store action=showByTitle title=$item->sef_url}">{$item->eventdate|date_format:"%A, %B %e, %Y"}</a>
            {*<p>{$item->summary|truncate:75:"..."}</p>*}
            <p>{$item->title}</p>
        </li>
    {/if}
    {/foreach}
    </ul>
</div>
