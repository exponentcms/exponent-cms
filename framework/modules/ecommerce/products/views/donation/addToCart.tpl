{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
    <p>{'Minimum donation amount is'|gettext} {$product->base_price|currency}.</p>
    {form name=donationamt controller=cart action="addItem"}
        {control type="hidden" name="product_type" value=$params.product_type}
        {control type="hidden" name="product_id" value=$params.product_id}
        {control type="hidden" name="options_shown" value=$params.product_id}
        {control type="hidden" name="quick" value=1}
        {control type="text" name="dollar_amount" label="Dollar Amount:"|gettext value=$record->dollar_amount size=7 filter=money}
        {*control type="buttongroup" name="add2cart" submit="Pay now"*}
    {/form}
    <a id="paynow" class="rc-link" href="">{'Pay now'|gettext}<span></span></a> or
    <a id="continue" class="rc-link" href="{link controller=cart action=addItem}">{'Add to cart and continue shopping'|gettext}<span></span></a>
</div>

{*FIXME convert to yui3*}
{script unique="a2cgc" yui3mods=1}
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
{/script}
