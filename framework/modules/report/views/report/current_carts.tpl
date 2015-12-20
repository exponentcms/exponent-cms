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

{css unique="carts_accordion" link="`$asset_path`css/current_carts.css" corecss="tables,accordion"}

{/css}

<div class="module report dashboard showall-accordion">
    {exp_include file='menu.tpl'}

    <div class="rightcol">
        <div class="module report current_carts">
            {*{br}*}
            <div class="exp-skin-table exp-ecom-table">
                <table border="0" cellspacing="0" cellpadding="0" width="50%">
                    <thead>
                        <tr>
                            <th colspan="2">
                                <h2 style="text-align: center;">{"Current Cart Summary"|gettext}</h2>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="odd">
                            <td>{"Total No. of Carts"|gettext}</td>
                            <td>{$summary.totalcarts}</td>
                        </tr>
                        <tr class="even">
                            <td>{"Value of Products in the Carts"|gettext}</td>
                            <td>{$summary.valueproducts|currency:0}</td>
                        </tr>
                        <tr class="odd">
                        <tr class="odd">
                            <td>{"Carts w/o Products"|gettext}</td>
                            <td>{$cartsWithoutItems.count} ({$summary.cartsWithoutItems})</td>
                        </tr>
                        <tr class="even">
                            <td>{"Carts w/ Products"|gettext}</td>
                            <td>{$cartsWithItems.count} ({$summary.cartsWithItems})</td>
                        </tr>
                        <tr class="odd">
                            <td>{"Carts w/ Products and User Info"|gettext}</td>
                            <td>{$cartsWithItemsAndInfo.count} ({$summary.cartsWithItemsAndInfo})</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {if $cartsWithoutItems|@count gt 1}
                {*{br}*}
                <div class="exp-skin-table exp-ecom-table">
                    <div id="empty-carts" class="panel">
                        <div class="hd">
                            <h2>{"Active Carts w/out Products and User Information"|gettext}</h2>
                            <a href="#" class="expand" title="{'Collapse/Expand'|gettext}">{'The List'|gettext}</a>
                        </div>
                        <div class="bd" id="yuievtautoid-0" style="height: 0px;">
                            <table border="0" cellspacing="0" cellpadding="0" width="50%">
                                <thead>
                                    <tr>
                                        <th>{'Length of Time'|gettext}</th>
                                        <th>{'IP/location'|gettext}</th>
                                        <th>{'Referring URL'|gettext}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                {foreach from=$cartsWithoutItems item=item}
                                    {if is_object($item)}
                                    <tr class="{cycle values="odd,even"}">
                                        <td>{$item->length_of_time}</td>
                                        <td>{$item->ip_address}</td>
                                        <td>{$item->referrer}</td>
                                    </tr>
                                    {/if}
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            {/if}

            {if $cartsWithItems|@count gt 1}
                {*{br}*}
                <div class="exp-skin-table exp-ecom-table">
                    <div id="full-carts" class="panel">
                        <div class="hd">
                            <h2>{"Active Carts w/ Products"|gettext}</h2>
                            <a href="#" class="expand" title="{'Collapse/Expand'|gettext}">{'The List'|gettext}</a>
                        </div>
                        <div class="bd" id="yuievtautoid-0" style="height: 0px;">
                            <table border="0" cellspacing="0" cellpadding="0" width="50%">
                                <thead>
                                    <tr>
                                        <th>{'Length of Time'|gettext}</th>
                                        <th>{'IP/location'|gettext}</th>
                                        <th>{'Referring URL'|gettext}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                {foreach from=$cartsWithItems item=item}
                                    {if is_array($item)}
                                    {cycle values="odd,even" assign=row}
                                    <tr class="{$row}">
                                        <td>{$item.length_of_time}</td>
                                        <td>{$item.ip_address}</td>
                                        <td>{$item.referrer}</td>
                                    </tr>
                                    <tr class="{$row}">
                                        <td colspan="3">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <td colspan="3"><h3 style="margin:0; padding: 0;">{'Products'|gettext}</h3></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{'Product Title'|gettext}</strong></td>
                                                    <td><strong>{'Quantity'|gettext}</strong></td>
                                                    <td><strong>{'Price'|gettext}</strong></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            {foreach from=$item item=item2}
                                                {if isset($item2->products_name)}
                                                    <tr>
                                                        <td>{$item2->products_name}</td>
                                                        <td>{$item2->quantity}</td>
                                                        <td>{$item2->products_price_adjusted|currency}</td>
                                                    </tr>
                                                {/if}
                                            {/foreach}
                                            </tbody>
                                        </table>
                                        </td>
                                    </tr>
                                    {/if}
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            {/if}

            {if $cartsWithItemsAndInfo|@count gt 1}
                {*{br}*}
                <div class="exp-skin-table exp-ecom-table">
                    <div id="active-carts" class="panel">
                        <div class="hd">
                            <h2>{"Active Carts w/ Products and User Information"|gettext}</h2>
                            <a href="#" class="expand" title="{'Collapse/Expand'|gettext}">{'The List'|gettext}</a>
                        </div>
                        <div class="bd" id="yuievtautoid-0" style="height: 0px;">
                            <table border="0" cellspacing="0" cellpadding="0" width="50%">
                                <thead>
                                    <tr>
                                        <th>{'Name'|gettext}</th>
                                        <th>{'Email'|gettext}</th>
                                        <th>{'Length of Time'|gettext}</th>
                                        <th>{'IP/location'|gettext}</th>
                                        <th>{'Referring URL'|gettext}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$cartsWithItemsAndInfo item=item}
                                        {if is_array($item)}
                                        {cycle values="odd,even" assign=row}
                                        <tr class="{$row}">
                                            <td>{$item.name}</td>
                                            <td>{$item.email}</td>
                                            <td>{$item.length_of_time}</td>
                                            <td>{$item.ip_address}</td>
                                            <td>{$item.referrer}</td>
                                        </tr>
                                        <tr class="{$row}">
                                            <td colspan="5">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <td colspan="3"><h3 style="margin:0; padding: 0;">{'Products'|gettext}</h3></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{'Product Title'|gettext}</strong></td>
                                                        <td><strong>{'Quantity'|gettext}</strong></td>
                                                        <td><strong>{'Price'|gettext}</strong></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                {foreach from=$item item=item2}
                                                    {if isset($item2->products_name)}
                                                        <tr>
                                                            <td>{$item2->products_name}</td>
                                                            <td>{$item2->quantity}</td>
                                                            <td>{$item2->products_price_adjusted|currency}</td>
                                                        </tr>
                                                    {/if}
                                                {/foreach}
                                                </tbody>
                                            </table>
                                            </td>
                                        </tr>
                                        {/if}
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
    {clear}
</div>

{script unique="expand-panels-`$id`" yui3mods="node,anim"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var panels = Y.all(".dashboard .rightcol .panel");
    var expandHeight = [];
    var exclusiveExp = false;
    var action = function(e){
        e.halt();
        var pBody = e.target.ancestor('.panel').one('.bd');
        var pID = e.target.ancestor('.panel').getAttribute('id');
        var savedState = e.target.ancestor('.panel').one('.hd a').getAttribute("class");
        var cfg = {
            node: pBody,
            duration: 0.5,
            easing: Y.Easing.easeOut
        }

        if (exclusiveExp) {
            panels.each(function(n,k){
                var cfg = {
                    node: n.one('.bd'),
                    duration: 0.5,
                    easing: Y.Easing.easeOut
                }
                n.one('.hd a').replaceClass('collapse','expand');
                n.one('.bd').replaceClass('expanded','collapsed');
                cfg.to = { height: 0 };
                var anim = new Y.Anim(cfg);
                anim.run();
            });
        }

        if (savedState=="collapse") {
            cfg.to = { height: 0 };
            cfg.from = { height: expandHeight[pID] };
            pBody.setStyle('height',expandHeight[pID]+"px");
            pBody.replaceClass('expanded','collapsed');
            e.target.ancestor('.panel').one('.hd a').replaceClass('collapse','expand');
        } else {
            pBody.setStyle('height',0);
            cfg.from = { height: 0 };
            cfg.to = { height: expandHeight[pID] };
            pBody.replaceClass('collapsed','expanded');
            e.target.ancestor('.panel').one('.hd a').replaceClass('expand','collapse');
        }
        var anim = new Y.Anim(cfg);
        anim.run();
    }
    panels.each(function(n,k){
        n.delegate('click',action,'.hd a');
//            n.one('.hd a').replaceClass('collapse','expand');
//            n.one('.bd').addClass('collapsed');
        expandHeight[n.get('id')] = n.one('.bd table').get('offsetHeight');
    });
    Y.Global.on('lazyload:cke', function() {
        panels.each(function(n,k){
            expandHeight[n.get('id')] = n.one('.bd table').get('offsetHeight');
        });
    });
});
{/literal}
{/script}
