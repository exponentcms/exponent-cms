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
        <h1>{'Current Order Stats'|gettext}</h1>
        <table>
            <thead>
                <tr>
                    <th>
                        {"Open Orders"|gettext}
                    </th>
                    <th>
                        {"New Orders"|gettext}
                    </th>
                    <th>
                        {"Orders Processing"|gettext}
                    </th>
                    <th>
                        {"Active Carts"|gettext}
                    </th>
                    <th>
                        {"Online Visitors"|gettext}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="{cycle values="even,odd"}" style="font-weight:bold; font-size:120%;">
                    <td style="text-align:center;">
                        <a href="{link controller=order action=showall}" title="{'View Orders'|gettext}">{order::getOrdersCount('open')}</a>
                    </td>
                    <td style="text-align:center;">
                        <a href="{link controller=order action=showall}" title="{'View Orders'|gettext}">{order::getOrdersCount('new')}</a>
                    </td>
                    <td style="text-align:center;">
                        <a href="{link controller=order action=showall}" title="{'View Orders'|gettext}">{order::getOrdersCount('processing')}</a>
                    </td>
                    <td style="text-align:center;">
                        <a href="{link action=current_carts}" title="{'View Carts'|gettext}">{$active_carts}</a>
                    </td>
                    <td style="text-align:center;">
                        <a href="{link controller=users action=manage}" title="{'View Customers'|gettext}">{$online}</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="rightcol exp-ecom-table">

        <h3>{'Last Five Orders'|gettext}</h3>
        <table>
            <thead>
            <tr class="{cycle values="even,odd"}" style="font-weight:bold; font-size:120%">
                <th>{'Customer'|gettext}</th>
                <th>{'Status'|gettext}</th>
                <th>{'Date'|gettext}</th>
                <th>{'Items'|gettext}</th>
                <th style="text-align:right;">{'Total'|gettext}</th>
                <th>&#160;</th>
            </tr>
            </thead>
            <tbody>
                {foreach from=$recent item=order}
                    <tr class="{cycle values="even,odd"}" style="color:grey;">
                        <td><a href="{link controller=users action=show id=$order->user_id}" title="{'View Customer'|gettext}">{$order->user->id|username:'system'}</a></td>
                        <td><span class="badge alert-{if $order->order_status_id == order::getDefaultOrderStatus()}success{else}default{/if}">{$order->order_status->title}</span></td>
                        <td>{$order->purchased|format_date}</td>
                        <td style="text-align:center;">{$order->orderitem|count}</td>
                        <td style="text-align:right;"><span class="badge {if $order->billingmethod.0->transaction_state|lower == 'complete' || $order->billingmethod.0->transaction_state|lower == 'paid'}alert-success{/if}" title="{if $order->billingmethod.0->transaction_state|lower == 'complete' ||  $order->billingmethod.0->transaction_state|lower == 'paid'}{'Paid'|gettext}{else}{'Payment Due'|gettext}{/if}">{$order->grand_total|currency}</span></td>
                        <td>{icon class=view controller=order action=show id=$order->id text="" title='View this order'|gettext}</td>
                    </tr>
                {foreachelse}
                    <tr><td colspan=6>{message text='No Orders Found!'|gettext}</td></tr>
                {/foreach}
            </tbody>
        </table>

    </div>
    {clear}
</div>
