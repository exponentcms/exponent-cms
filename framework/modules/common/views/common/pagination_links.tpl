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
 
{css unique="pagination-links" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/pagination.css"}
    
{/css}

{if $page->total_records > 0}
<div class="pagination-links">	
	<span class="pagination">
	{if $page->previous_page != ''}<a href="{$page->previous_page}"><< Previous</a> {/if}
	{if $page->firstpage != ''}<a href="{$page->firstpage}">1</a> {/if}
	{if $page->previous_shift != ''}<a href="{$page->previous_shift}">...</a> {/if}
	{if $page->total_pages > 1}
	{foreach from=$page->pages item=link key=curpage}
		<span class="pagelink">
			{if $curpage == $page->page}
				<span class="currentpage">{$curpage}</span>
			{else}
				<a href="{$link}">{$curpage}</a>
			{/if}
		</span>
	{/foreach}
	{/if}
	{if $page->next_shift != ''}<a href="{$page->next_shift}">...</a> {/if}
	{if $page->lastpage != ''}<a href="{$page->lastpage}">{$page->total_pages}</a> {/if}
	{if $page->next_page != ''} <a href="{$page->next_page}">Next >></a>{/if}
	</span>
	<span class="pagetotals">Showing <span class="frecord">{$page->firstrecord}</span>-<span class="lrecord">{$page->lastrecord}</span> of <span class="total">{$page->total_records}</span></span>
</div>
{/if}
