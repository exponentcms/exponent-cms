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

{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css"}

{/css}
{css unique="report-builder" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/report-builder.css"}

{/css}
{css unique="calendar-edit1" link="`$smarty.const.YUI2_RELATIVE`assets/skins/sam/calendar.css"}

{/css}
{css unique="calendar-edit2" link="`$smarty.const.YUI2_RELATIVE`assets/skins/sam/container.css"}

{/css}
{css unique="calendar-edit3" link="`$smarty.const.YUI2_RELATIVE`assets/skins/sam/button.css"}

{/css}
<div class="module report dashboard">
    {exp_include file='menu.tpl'}

    <div class="rightcol exp-ecom-table">
        <h1>{'Order Stats for Requested Period'|gettext}</h1>

        <table>
            <tr>
                <td width="50%">
                    {form controller="report" action="stats" name="filter_dashboard" id="filter_dashboard"}
                        {"Quick Range Filter:"|gettext}
                        {control type="dropdown" name="quickrange" label="" items=$quickrange default=$quickrange_default onchange="this.form.submit();"}
                    {/form}
                </td>
                <td>
                    {form action="stats"}
                        {"Purchased Between"|gettext}:
                        {control type="calendar" name="starttime" label="" default_date=$prev_date default_hour=$prev_hour default_min=$prev_min default_ampm=$prev_ampm}
                        {"And"|gettext}
                        {control type="calendar" name="endtime" label="" default_date=$now_date default_hour=$now_hour default_min=$now_min default_ampm=$now_ampm}
                        {control type="buttongroup" submit="Apply Filter"|gettext}
                    {/form}
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="5">
                        <a href="#">{"New Customers"|gettext}</a>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr style="font-weight:bold; font-size:120%">
                    <td colspan="5">{$new}</td>
                </tr>
            </tbody>

            <thead>
                <tr>
                    <th>
                        <a href="#">{"Order Type"|gettext}</a>
                    </th>
                    <th>
                        <a href="#">{"Order Status"|gettext}</a>
                    </th>
                    <th>
                        <a href="#">{"# of Orders"|gettext}</a>
                    </th>
                    <th>
                        <a href="#">{"# of Items"|gettext}</a>
                    </th>
                    <th style="text-align:right;">
                        <a href="#">{"Total"|gettext}</a>
                    </th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$orders item=order key=tkey name=typeloop}
                    <tr class="{cycle values="even,odd"}" style="font-weight:bold; font-size:120%">
                        <td>{$tkey}</td>
                        <td>&#160;</td>
                        <td>{$order.num_orders}</td>
                        <td>{$order.num_items}</td>
                        <td style="text-align:right;">{$order.grand_total|currency}</td>
                    </tr>
                    {foreach from=$order item=stat key=skey name=typeloop}
                        {if $skey != 'num_orders' && $skey!= 'num_items' && $skey != 'grand_total'}
                            <tr class="{cycle values="even,odd"}" style="color:grey;">
                                <td>&#160;</td>
                                <td>{$skey}</td>
                                <td>{$stat.num_orders}</td>
                                <td>{$stat.num_items}</td>
                                <td style="text-align:right;">{$stat.grand_total|currency}</td>
                            </tr>
                        {/if}
                    {/foreach}
                {foreachelse}
                    <tr><td colspan=5>{message text='No Orders Found!'|gettext}</td></tr>
                {/foreach}
            </tbody>
        </table>

    </div>
    {clear}
</div>
