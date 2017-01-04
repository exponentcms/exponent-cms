{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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

{css unique="ecom-dashboard1" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/dashboard.css"}

{/css}

{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css"}

{/css}
    <div class="leftcol">

        <div id="quickstats" class="panel">
            <div class="hd"><h2>{'Quick Stats'|gettext}</h2><a href="#" class="collapse">{'Collapse'|gettext}</a></div>
            <div class="bd {if $smarty.cookies.quickstats=='collapsed'}collapsed{/if}">
                <ul>
                    <li>
                        <strong><a href="{link action=dashboard}">{'Recent Order Stats'|gettext}</a></strong>
                        {'View summary of recent orders.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link controller=order action=showall}">{'Manage Orders'|gettext}</a></strong>
                        {'View and manage all new and existing orders.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link controller=store action=manage}">{'Manage Products'|gettext}</a></strong>
                        {'List all products in your store and makes it easy to manage them.'|gettext}
                    </li>
                </ul>
            </div>
        </div>

        <div id="cartstats" class="panel">
            <div class="hd"><h2>{'Cart Stats'|gettext}</h2><a href="#" class="collapse">{'Collapse'|gettext}</a></div>
            <div class="bd {if $smarty.cookies.cartstats=='collapsed'}collapsed{/if}">
                <ul>
                    {*<li>*}
                        {*<strong><a href="{link action=cart_summary}">{'Cart Summary Stats'|gettext}</a></strong>*}
                        {*{'Quick statistics on carts vs. orders.'|gettext}*}
                    {*</li>*}
                    <li>
                        <strong><a href="{link action=current_carts}">{'Current Carts'|gettext}</a></strong>
                        {'View and manage current carts.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link action=abandoned_carts}">{'Abandoned Carts'|gettext}</a></strong>
                        {'View and manage abandoned carts.'|gettext}
                    </li>
                </ul>
            </div>
        </div>

        <div id="orders" class="panel">
            <div class="hd"><h2>{'Orders'|gettext}</h2><a href="#" class="collapse">{'Collapse'|gettext}</a></div>
            <div class="bd {if $smarty.cookies.orders=='collapsed'}collapsed{/if}">
                <ul>
                    <li>
                        <strong><a href="{link controller=order action=showall}">{'Manage Orders'|gettext}</a></strong>
                        {'View and manage all new and existing orders.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link controller=order action=create_new_order}">{'Add an Order'|gettext}</a></strong>
                        {'Create a new order.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link action=order_report}">{'Create a Report'|gettext}</a></strong>
                        {'Create reports based on orders.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link controller=order_status action=manage}">{'Manage Status Codes'|gettext}</a></strong>
                        {'Manage the labeling of each phase an  Order is processed through.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link controller=order_status action=manage_messages}">{'Manage Status Messages'|gettext}</a></strong>
                        {'Create, edit, and delete Order Status Messages.'|gettext}
                    </li>
                </ul>
            </div>
        </div>

        <div id="products" class="panel">
            <div class="hd"><h2>{'Products'|gettext}</h2><a href="#" class="collapse">{'Collapse'|gettext}</a></div>
            <div class="bd {if $smarty.cookies.products=='collapsed'}collapsed{/if}">
                <ul>
                    <li>
                        <strong><a href="{link controller=store action=picktype}">{'Add a Product'|gettext}</a></strong>
                        {'Add a'|gettext} <a
                                href="{link controller=store action=edit product_type=product}">{'Product'|gettext}</a>,
                        <a href="{link controller=store action=edit product_type=donation}">{'Donation'|gettext}</a>,
                        <a href="{link controller=store action=edit product_type=giftcard}">{'Gift Card'|gettext}</a>,
                        {'or'|gettext} <a
                                href="{link controller=store action=edit product_type=eventregistration}">{'Event Registration'|gettext}</a>
                        {'to your store.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link controller=store action=manage}">{'Manage Products'|gettext}</a></strong>
                        {'List all products in your store and makes it easy to manage them.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link action=product_report}">{'Create a Report'|gettext}</a></strong>
                        {'Create reports based on products.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link controller=ecomconfig action=options}">{'Manage Product Options'|gettext}</a></strong>
                        {'Create options for your products, like \'large\', \'small\', \'red\', \'green\', \'blue\', etc.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link controller=storeCategory action=manage}">{'Manage Store Categories'|gettext}</a></strong>
                        {'Create and manage Categories in a hierarchical fashion.'|gettext}
                    </li>
                </ul>
            </div>
        </div>

        <div id="configuration" class="panel">
            <div class="hd"><h2>{'Store Settings'|gettext}</h2><a href="#" class="collapse">{'Collapse'|gettext}</a>
            </div>
            <div class="bd {if $smarty.cookies.configuration=='collapsed'}collapsed{/if}">
                <ul>
                    <li>
                        <strong><a href="{link controller=ecomconfig action=configure}">{'Configure Store Settings'|gettext}</a></strong>
                        {'Set up how your site will look and act, and notify you.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link controller=billing action=manage}">{'Configure Billing Settings'|gettext}</a></strong>
                        {'Set up your store to use PayPal, Authorize.net, and other billing methods.'|gettext}
                    </li>
                    <li>
                        <strong><a href="{link controller=shipping action=manage}">{'Configure Shipping Settings'|gettext}</a></strong>
                        {'Set up your site to use UPS, FedEx, in-store pickup and other shipping methods.'|gettext}
                    </li>
                </ul>
            </div>
        </div>
    </div>

{script unique="expand-panels" yui3mods="node,cookie,anim"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        var panels = Y.all(".dashboard .leftcol .panel");
        var expandHeight = [];
        var action = function(e){
            e.halt();

            var pBody = e.target.ancestor('.panel').one('.bd');
            var pID = e.target.ancestor('.panel').getAttribute('id');
            var cfg = {
                node: pBody,
                duration: 0.5,
                easing: Y.Easing.easeOut
            }

            if (e.target.getAttribute("class")=="collapse") {
                cfg.to = { height: 0 };
                cfg.from = { height: expandHeight[pID] };
                pBody.setStyle('height',expandHeight[pID]+"px");
                pBody.replaceClass('expanded','collapsed');
                e.target.replaceClass('collapse','expand');
                Y.Cookie.set(pID, "collapsed");
            } else {
                pBody.setStyle('height',0);
                cfg.from = { height: 0 };
                cfg.to = { height: expandHeight[pID] };
                pBody.replaceClass('collapsed','expanded');
                e.target.replaceClass('expand','collapse');
                Y.Cookie.set(pID, "expanded");
            }
            var anim = new Y.Anim(cfg);

            anim.run();
        }
        panels.each(function(n,k){
            n.delegate('click',action,'.hd a');
            if (Y.Cookie.get(n.get('id'))==="collapsed") {
                n.one('.hd a').replaceClass('collapse','expand');
                n.one('.bd').addClass('collapsed');
            };
            expandHeight[n.get('id')] = n.one('.bd ul').get('offsetHeight');
        });
    });
{/literal}
{/script}