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
 
{css unique="pagination-links" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/pagination.css"}

{/css}

{if $page->total_records > 0}
    <div class="pagination-links">
        <span class="pagination">
            {if $page->previous_page != ''}<a class="pager" href="{$page->previous_page}" rel="{$page->previous_pagenum}">&laquo;</a> {/if}
            {if $page->firstpage != ''}<a class="pager" href="{$page->firstpage}" rel="1">1</a> {/if}
            {if $page->previous_shift != ''}<a class="pager" href="{$page->previous_shift}" rel="{$page->previous_shiftnum}">...</a> {/if}
            {if $page->total_pages > 1}
                {foreach from=$page->pages item=link key=curpage}
                    <span class="pagelink">
                        {if $curpage == $page->page}
                            <span class="currentpage">{$curpage}</span>
                        {else}
                            <a class="pager" href="{$link}" rel="{$curpage}">{$curpage}</a>
                        {/if}
                    </span>
                {/foreach}
            {/if}
            {if $page->next_shift != ''}<a class="pager" href="{$page->next_shift}" rel="{$page->next_shiftnum}">...</a> {/if}
            {if $page->lastpage != ''}<a class="pager" href="{$page->lastpage}" rel="{$page->total_pages}">{$page->total_pages}</a> {/if}
            {if $page->next_page != ''} <a class="pager" href="{$page->next_page}" rel="{$page->next_pagenum}">&raquo;</a>{/if}
        </span>
        <span class="pagetotals">{'Showing'|gettext} <span class="frecord">{$page->firstrecord}</span>-<span class="lrecord">{$page->lastrecord}</span> {'of'|gettext} <span class="total">{$page->total_records}</span></span>
    </div>
{/if}
