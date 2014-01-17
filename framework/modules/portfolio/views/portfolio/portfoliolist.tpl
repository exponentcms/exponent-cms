{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
    {if $config.show_search}
        {control type=text name="portfoliosearchinput" label='Limit items to those including:'|gettext}
    {/if}
    {pagelinks paginate=$page top=1}
    {$cat="bad"}
    {foreach from=$page->records item=record}
        {if $cat !== $record->expCat[0]->id && $config.usecategories}
            <h2 class="category">{if $record->expCat[0]->title!= ""}{$record->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2>
        {/if}
        {include 'portfolioitem.tpl'}
        {$cat=$record->expCat[0]->id}
    {/foreach}
    {clear}
    {pagelinks paginate=$page bottom=1}

{if $config.show_search}
{script unique="`$name`search" jquery='jquery.searcher'}
{literal}
    $(".portfolio.showall").searcher({
        itemSelector: ".item",
        textSelector: "h3{/literal}{if !$config.search_title_only},.bodycopy{/if}{literal}",
        inputSelector: "#portfoliosearchinput",
        toggle: function(item, containsText) {
            // use a typically jQuery effect instead of simply showing/hiding the item element
            if (containsText)
                $(item).fadeIn();
            else
                $(item).fadeOut();
        }
    });
{/literal}
{/script}
{/if}