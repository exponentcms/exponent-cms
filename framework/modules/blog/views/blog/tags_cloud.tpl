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

{*
    This view is broken. I think it's basing itself off a click count, which I'm not sure exists as a feature of tags yet...
    Leaving this for Ben. I thik he created it.
*}

<div class="module blog tag-cloud">
    <h2>{$moduletitle|default:"Tags"}</h2>
    {foreach from=$tags item=tag}
       {if $tag->cnt != ""}
       		<span style="font-size:{if $tag->cnt lt 9}9px{elseif $tag->cnt gt 20}20px{else}{$tag->cnt}px{/if}">
       			<a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}{*<!--({$tag->count})-->*}</a>
        	</span>&nbsp;&nbsp;
        {/if} 
    {/foreach}
</div>
   