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

<div class="module search search-results">
	
	<h1>{'Search Results'|gettext}</h1>
    {pagelinks paginate=$page top=1}
	<span class="searched_for">
	{'Your search for'|gettext} <span class="terms">"{$terms}"</span> {'returned'|gettext} <span class="result-count">{$page->total_records}</span> {'results'|gettext}<br />
	</span>
	{if $config->is_categorized == 0}
		{foreach from=$page->records item=result}
			{*if $result->canview == 1*}
				<div class="item {cycle values="odd,even"}">
					<a href="{$smarty.const.PATH_RELATIVE}{$result->view_link}">{$result->title|highlight:$terms}</a>
					{if $result->body != ""}{br}<span class="summary">{$result->body|strip_tags|truncate:240|highlight:$terms}</span>{/if}
					{clear}
				</div>
			{*/if*}
		{/foreach}
	{else}{* categorized, list of crap is two levels deep *}
		{foreach from=$results key=category item=subresults}
			<h2><a name="#{$category}">{$category} {'matching'|gettext} "{$query}":</a></h2>
			{foreach from=$subresults item=result}
				<div class="item {cycle values="odd,even"}">
					<a href="{$smarty.const.PATH_RELATIVE}{$result->view_link}">{$result->title}</a>
					{if $result->sum != ""}<br /><span class="summary">{$result->sum}</span>{/if}
					{*<br /><span class="search_result_item_link">{$result->view_link}</span>*}
				</div>
			{/foreach}
		{/foreach}
	{/if}
    {pagelinks paginate=$page bottom=1}
</div>
