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

<div class="module notfound page-not-found">
    <h1>{$smarty.const.SITE_404_TITLE}</h1>
    <p>{$smarty.const.SITE_404_HTML}</p>
    
    {if $results|@count > 0}
        <h2>Could these be what you are looking for?</h2>
        <p>We did find the following pages that were similar to the page you were looking for</p>
        <div class="module search search">
            {foreach from=$results item=result}
	            <div class="item {cycle values="odd,even"}">
		            <a href="{$result->view_link}">{$result->title}</a>
		            {if $result->body != ""}<br /><span class="summary">{$result->body|strip_tags|truncate:240}</span>{/if}
		            {clear}
	            </div>
	        {/foreach}
	    </div>
	{/if}
</div>
