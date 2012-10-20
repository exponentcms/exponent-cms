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

{uniqueid assign="id"}

{css unique="portfolio" link="`$asset_path`css/portfolio.css"}

{/css}

<div class="module portfolio showall showall-tabbed">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add a Portfolio Piece"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='portfolio' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='portfolio' text="Manage Categories"|gettext}
                {/if}
            {/if}
			{*{if $permissions.manage == 1 && $rank == 1}*}
			{if $permissions.manage == 1 && $config.order == 'rank'}
				{ddrerank items=$page->records model="portfolio" label="Portfolio Pieces"|gettext}
			{/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {*{assign var=myloc value=serialize($__loc)}*}
    {$myloc=serialize($__loc)}
    <div id="{$id}" class="yui-navset exp-skin-tabview hide">
        <ul>
            {foreach name=tabs from=$page->cats key=catid item=cat}
                <li><a href="#tab{$smarty.foreach.items.iteration}">{$cat->name}</a></li>
            {/foreach}
        </ul>
        <div>
            {foreach name=items from=$page->cats key=catid item=cat}
                <div id="tab{$smarty.foreach.items.iteration}">
                     {foreach from=$cat->records item=record}
                        {include 'portfolioitem.tpl'}
                    {/foreach}
                </div>
            {/foreach}
        </div>
    </div>
    <div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{script unique="`$id`" yui3mods="1"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#{/literal}{$id}{literal}'});
		Y.one('#{/literal}{$id}{literal}').removeClass('hide');
		Y.one('.loadingdiv').remove();
	});
{/literal}
{/script}
