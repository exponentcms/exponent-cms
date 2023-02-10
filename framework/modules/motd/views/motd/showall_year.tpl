{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{uniqueid prepend="cal" assign="name"}

{css unique="cal" link="`$smarty.const.PATH_RELATIVE`framework/modules/events/assets/css/calendar.css"}
{if !bs()}
{literal}
    .col-xs-6 {
        width: 24%;
        display: inline-flex;
    }
{/literal}
{/if}
{literal}
    .mini-cal {
        width: auto;
    }
    table.mini-cal {
        height: 18rem;
    }
    .mini-cal ul li {
        list-style: none;
    }
{/literal}
{/css}

<div class="module motd events mini-cal annual ">
    <div class="row">
        <{$config.heading_level|default:'h1'}>
           {ical_link}
           {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}{/if}
        </{$config.heading_level|default:'h1'}>
        {if $config.moduledescription != ""}
            {$config.moduledescription}
        {/if}
        {permissions}
            {if $permissions.create}
                <div class="module-actions">
                    {icon class=add action=edit text="Add a tip"|gettext}
                </div>
            {/if}
        {/permissions}
    </div>
    <div class="row">
        {foreach $year as $monthid=>$monthly}
            {$now = $monthly.timefirst}
            <div id="mini-{$name}" class="col-6 col-xs-6 col-sm-4 col-md-3">
                {exp_include file='minical.tpl'}
            </div>
        {/foreach}
    </div>
    <div class="row">
        {$monthly = $days}
        {$now = null}
        <div id="mini-{$name}1" class="col-6 col-xs-6 col-sm-4 col-md-3">
            {exp_include file='minical.tpl'}
        </div>
    </div>
    {permissions}
        {if $permissions.manage}
            <div class="module-actions">
                {icon class=view action=showall text="View Tips as List"|gettext}
            </div>
        {/if}
    {/permissions}
</div>
