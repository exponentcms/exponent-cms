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

<div class="module notfound search-results">
    <h1>{$smarty.const.SITE_404_TITLE}</h1>
    <p>{$smarty.const.SITE_404_HTML}</p>
    
    {if $page->records|@count > 0}
        <h2>{'Could this be what you are looking for'|gettext}?</h2>
        <span class="searched_for">
        {'We found the following pages which are similar to the page'|gettext} <span class="terms">"{$terms}"</span> {'you were looking for'|gettext}
	    </span>
		{foreach from=$page->records item=result}
			<div class="item {cycle values="odd,even"}">
				<a href="{$smarty.const.PATH_RELATIVE}{$result->view_link}">{$result->title}</a>
				{if $result->body != ""}{br}<span class="summary">{$result->body|strip_tags|truncate:240|highlight:$terms}</span>{/if}
				{clear}
			</div>
		{/foreach}
	{/if}
</div>
