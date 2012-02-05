{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module portfolio showall-tabbed">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add a Portfolio Piece"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {icon class="manage" controller=expTag action=manage text="Manage Tags"|gettext}
            {/if}
			{if $permissions.manage == 1 && $rank == 1}
				{ddrerank items=$page->records model="portfolio" label="Portfolio Pieces"|gettext}
			{/if}
        </div>
    {/permissions}
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
                        <div class="item">
                            <h3><a href="{link action=show title=$record->sef_url}" title="{$record->title|escape:"htmlall"}">{$record->title}</a></h3>
                            {permissions}
                                <div class="item-actions">
                                    {if $permissions.edit == 1}
                                        {icon action=edit record=$record title="Edit `$record->title`"}
                                    {/if}
                                    {if $permissions.delete == 1}
                                        {icon action=delete record=$record title="Delete `$record->title`"}
                                    {/if}
                                </div>
                            {/permissions}
                            {if $record->expTag|@count>0}
                                <div class="tag">
                                    {'Tags'|gettext}:
                                    {foreach from=$record->expTag item=tag name=tags}
                                        <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
                                    {/foreach}
                                </div>
                            {/if}
                            <div class="bodycopy">
                                {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record is_listing=1}

                                {if $config.usebody==1}
                                    <p>{$record->body|summarize:"html":"paralinks"}</p>
                                {elseif $config.usebody==2}
                                {else}
                                    {$record->body}
                                {/if}
                            </div>
                            {permissions}
                                {if $permissions.create == 1}
                                    {icon class="add addhere" action=edit rank=$record->rank+1 title="Add another here"|gettext  text="Add a portfolio piece here"|gettext}
                                {/if}
                            {/permissions}
                            <div style="clear:both"></div>
                        </div>
                    {/foreach}

                </div>
            {/foreach}
        </div>
    </div>
    <div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{script unique="`$id`" yui3mods="1"}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
	    var tabview = new Y.TabView({srcNode:'#{/literal}{$id}{literal}'});
	    tabview.render();
		Y.one('#{/literal}{$id}{literal}').removeClass('hide');
		Y.one('.loadingdiv').remove();
	});
{/literal}
{/script}
