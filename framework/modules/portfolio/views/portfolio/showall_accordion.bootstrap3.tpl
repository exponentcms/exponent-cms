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

{css unique="portfolio" link="`$asset_path`css/portfolio.css"}

{/css}
{css unique="accordion" corecss="accordion"}

{/css}

{uniqueid assign="id"}

<div class="module portfolio showall showall-accordion">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add a Portfolio Piece"|gettext}
			{/if}
            {if $permissions.manage}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='portfolio' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='portfolio' text="Manage Categories"|gettext}
                {/if}
            {/if}
			{*{if $permissions.manage && $rank == 1}*}
			{if $permissions.manage && $config.order == 'rank'}
				{ddrerank items=$page->records model="portfolio" label="Portfolio Pieces"|gettext}
			{/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    <div id="portfolio-{$id}" class="panel-group">
        {foreach name=items from=$page->cats key=catid item=cat}
            <div id="item{$catid}" class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><a data-toggle="collapse" data-parent="#portfolio-{$id}" href="#collapse-{$catid}" title="{'Collapse/Expand'|gettext}"><{$config.item_level|default:'h2'}>{if $cat->name ==""}{if $config.uncat == ""}{'The List'|gettext}{else}{$config.uncat}{/if}{else}{$cat->name}{/if}</{$config.item_level|default:'h2'}></a></div>
                </div>
                <div id="collapse-{$catid}" class="panel-collapse collapse{if ($smarty.foreach.items.iteration==1 && $config.initial_view == '3') || $config.initial_view == '2'} in{/if}">
                    <div class="piece panel-body">
                        <ul>
                            {foreach from=$cat->records item=item}
                                <li>
                                    {exp_include file='portfolioitem.tpl'}
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
</div>

{script unique="accordion" bootstrap="collapse,transition"}
{literal}

{/literal}
{/script}
