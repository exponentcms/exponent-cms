{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

<div class="module blog showall-dates">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>
        {rss_link}
        {$moduletitle}
    </{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {foreach from=$dates item=ydate key=year}
        <div class="row">
            <h3 class="col-12 text-center">
                <a href="{link action=showall_by_date year=$year}" title='{"View all posts from"|gettext} {$year}'>{$year}</a>
            </h3>
            {foreach from=$ydate item=mdate key=month}
                <div class="col-xs-4 col-4 box-border">
                    {if $mdate->count}
                        <a href="{link action=showall_by_date month=$month year=$year}" title='{"View all posts from"|gettext} {$mdate->name} {$year}'><i class="fas fa-calendar-alt"> </i> {$mdate->name} ({$mdate->count})</a>
                    {else}
                        {$mdate->name}
                    {/if}
                </div>
            {/foreach}
        </div>
    {/foreach}
</div>
