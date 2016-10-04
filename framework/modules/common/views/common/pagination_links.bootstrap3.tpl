{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{css unique="z-pagination-bootstrap" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/pagination-bootstrap3.css"}

{/css}

{if $page->total_records > 0}
    <div class="pagination-links">
        <nav class="pagination pagination-small" aria-label="{'Page navigation'|gettext}">
            <ul class="pagination pagination-sm">
            {if $page->previous_page != ''}<li><a class="pager prev" href="{$page->previous_page}" rel="{$page->previous_pagenum}" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li> {/if}
            {if $page->firstpage != ''}<li><a class="pager" href="{$page->firstpage}" rel="1"><span class="sr-only">{'Go to page'|gettext} </span>1</a></li> {/if}
            {if $page->previous_shift != ''}<li><a class="pager" href="{$page->previous_shift}" rel="{$page->previous_shiftnum}">...</a></li> {/if}
            {if $page->total_pages > 1}
                {foreach from=$page->pages item=link key=curpage}
                        {if $curpage == $page->page}
                            <li class="active disabled"><a href="#">{$curpage}<span class="sr-only">({'current page'|gettext})</span></a></li>
                        {else}
                            <li><a class="pager" href="{$link}" rel="{$curpage}"><span class="sr-only">{'Go to page'|gettext} </span>{$curpage}</a></li>
                        {/if}
                {/foreach}
            {/if}
            {if $page->next_shift != ''}<li><a class="pager" href="{$page->next_shift}" rel="{$page->next_shiftnum}">...</a></li> {/if}
            {if $page->lastpage != ''}<li><a class="pager" href="{$page->lastpage}" rel="{$page->total_pages}"><span class="sr-only">{'Go to page'|gettext} </span>{$page->total_pages}</a></li> {/if}
            {if $page->next_page != ''} <li><a class="pager next" href="{$page->next_page}" rel="{$page->next_pagenum}" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>{/if}
            </ul>
        </nav>
        <span class="pagetotals">{'Showing'|gettext} <span class="frecord">{$page->firstrecord}</span>-<span class="lrecord">{$page->lastrecord}</span> {'of'|gettext} <span class="total">{$page->total_records}</span></span>
        {clear}
        <span class="loader"></span>
    </div>
{/if}
