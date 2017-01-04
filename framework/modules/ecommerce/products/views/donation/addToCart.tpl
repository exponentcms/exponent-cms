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

<div class="module cart module addToCart">
    <h1>{$moduletitle|default:"Online Donation - Select Amount"|gettext}</h1>
    <h3>{$product->title}</h3>
    <blockquote>{'Minimum donation amount is'|gettext} {$product->base_price|currency}.</blockquote>
    {form name=donationamt controller=cart action="addItem"}
        {control type="hidden" name="product_type" value=$params.product_type}
        {control type="hidden" name="product_id" value=$params.product_id}
        {control type="hidden" name="options_shown" value=$params.product_id}
        {control type="hidden" name="quick" value=1}
        {control type="text" name="dollar_amount" label="Dollar Amount:"|gettext value=$product->base_price size=7 filter=money}
        {*control type="buttongroup" name="add2cart" submit="Pay now"*}
    {/form}
    {*<a id="paynow" class="add-to-cart-btn {button_style} rc-link" href="">{'Donate now'|gettext}<span></span></a> or*}
    {icon id="paynow" class="add-to-cart-btn rc-link" button=true size=large color=blue action=scriptaction title='Donate'|gettext text=expCore::getCurrencySymbol()|cat:' '|cat:'Donate now'|gettext}<span></span></a> {'OR'|gettext}
    {*<a id="continue" class="add-to-cart-btn {button_style} rc-link" href="{link controller=cart action=addItem}">{'Add to cart and continue shopping'|gettext}<span></span></a>*}
    {icon id="continue" class="add-to-cart-btn rc-link" button=true size=large color=blue controller=cart action=addItem title='Donate'|gettext text='Add to cart and continue shopping'|gettext}<span></span>
</div>

{*FIXME convert to yui3*}
{*script unique="a2cgc" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event', function(Y) {
    var YAHOO=Y.YUI2;
    YAHOO.util.Event.onDOMReady(function(){
        var links = YAHOO.util.Dom.getElementsByClassName('rc-link', 'a');

        YAHOO.util.Event.on(links, 'click', function (e) {
            YAHOO.util.Event.stopEvent(e);
            var targ = YAHOO.util.Event.getTarget(e);
            if (targ.id === 'continue') {
                YAHOO.util.Dom.get('quick').value = 0;
            }
            YAHOO.util.Dom.get('donationamt').submit();
        });

        var bp = {/literal}{$product->base_price};{literal}
        var da = YAHOO.util.Dom.get('dollar_amount');
        YAHOO.util.Event.on(da, 'blur', function(e,o){
            //Y.log(this.value);
            var newint = parseInt(this.value.replace('$',"").replace(',',""));
            if (newint < bp) {
                this.value = '{/literal}{currency_symbol}{literal}'+bp+".00";
            }
        }, da, true);
    });
});
{/literal}
{/script*}

{script unique="a2cgc" yui3mods="node"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var links = Y.all('a.rc-link');

    links.on('click', function (e) {
        e.halt();
        var targ = e.target;
        if (targ.get('id') === 'continue') {
            Y.one('#quick').value = 0;
        }
        Y.one('#donationamt').submit();
    });

    var bp = {/literal}{$product->base_price};{literal}
    var da = Y.one('#dollar_amount');
    da.on('blur', function(e,o){
        var newint = parseInt(e.target.get('value').replace('$',"").replace(',',""));
        if (newint < bp) {
            e.target.set('value','{/literal}{currency_symbol}{literal}'+bp+'.00');
        }
    });
});
{/literal}
{/script}
