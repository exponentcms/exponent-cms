{*
 * Copyright (c) 2004-2020 OIC Group, Inc.
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

    {$myloc=serialize($__loc)}
    {pagelinks paginate=$page top=1}
    {$cat="bad"}
    {foreach from=$page->records item=item name=files}
        {if $cat !== $item->expCat[0]->id && $config.usecategories}
            <a href="{link action=showall src=$page->src group=$item->expCat[0]->id}" title='View this group'|gettext><h2 class="category">{if $item->expCat[0]->title!= ""}{$item->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2></a>
        {/if}
        {exp_include file='filedownloaditem.tpl'}
        {$cat=$item->expCat[0]->id}
    {/foreach}
    {pagelinks paginate=$page bottom=1}
