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
 
    {$myloc=serialize($__loc)}
    {if $config.show_search}
        {control type=text name="portfoliosearchinput" label='Limit items to those including:'|gettext}
    {/if}
    {pagelinks paginate=$page top=1}
    {$cat="bad"}
    {foreach from=$page->records item=item}
        {if $cat !== $item->expCat[0]->id && $config.usecategories}
            <h2 class="category">{if $item->expCat[0]->title!= ""}{$item->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2>
        {/if}
        {exp_include file='portfolioitem.tpl'}
        {$cat=$item->expCat[0]->id}
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
            if (containsText) {
                {/literal}{if $config.usecategories}
                $(item).prev('h2.category').fadeIn();
                {/if}{literal}
                $(item).fadeIn();
            } else {
                {/literal}{if $config.usecategories}
                $(item).prev('h2.category').fadeOut();
                {/if}{literal}
                $(item).fadeOut();
            }
        }
    });
{/literal}
{/script}
{/if}