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

<div class="module cart giftcard addToCart">
    <h1>{$moduletitle|default:"Gift Card - Add to Cart"|gettext}</h1>
    {if empty($product->base_price)}
        {$price = 1}
    {else}
        {$price = $product->base_price}
    {/if}
    <blockquote>{'Gift Card amounts must be purchased in'|gettext} {currency_symbol}{$price}.00 {'increments'|gettext}.</blockquote>
    {form action="addItem"}
        {control type="hidden" name="controller" value=cart}
        {control type="hidden" name="product_type" value=$params.product_type}
        {control type="hidden" name="product_id" value=$params.product_id}
        {control type="hidden" name="options_shown" value=$product->id}
        {control type="text" id="dollar_amount" name="card_amount_txt" label="Dollar Amount:"|gettext value=$price size=7 filter=money}
        <h4>{'The \'To\' and \'From\' name may be added at no additional charge.'|gettext}</h4>
        {control type="text" name="toname" label="To:"|gettext value=$record->to}
        {control type="text" name="fromname" label="From:"|gettext value=$record->from}
        {if !empty($config.custom_message_product)}<h4><em>{'Adding a custom message will add'|gettext} {$config.custom_message_product|currency} {'to the price of your gift card.'|gettext}</em></h4>{/if}
        {control type="textarea" name="msg" label="Message:"|gettext rows=3 value=$record->msg}
        {control type="buttongroup" name="add2cart" size=large color=blue submit="Add to cart"|gettext}
    {/form}
</div>

{*FIXME convert to yui3*}
{*script unique="a2cgc" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event', function(Y) {
        var YAHOO=Y.YUI2;
        YAHOO.util.Event.onDOMReady(function(){
            var bp = {/literal}{$price}{literal};
            var da = YAHOO.util.Dom.get('dollar_amount');
            YAHOO.util.Event.on(da, 'blur', function(e,o){
                var newint = parseInt(this.value.replace('$',""));
                this.value = '{/literal}{currency_symbol}{literal}'+Math.ceil(newint/bp)*bp+".00";
            }, da, true);

            YAHOO.util.Event.on(['toname','fromname'], 'keyup', function(e){
                var targ = YAHOO.util.Event.getTarget(e);
                var junk = [':',')','-','!','@','#','$','%','^','&','*','(',')','_','+','=','-','`','~','{','}','|','[',']','\\',':','"',';','\'','<','>','?',',','.','/'];
                for (var jk in junk ) {
                    targ.value = targ.value.replace(junk[jk],"");
                }
                //Y.log(targ);
            });
        });
    });
{/literal}
{/script*}

{script unique="a2cgc" yui3mods="node"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        var bp = {/literal}{$price}{literal};
        var da = Y.one('#dollar_amount');
        da.on('blur', function(e,o){
            var newint = parseInt(e.target.get('value').replace('$',""));
            e.target.set('value','{/literal}{currency_symbol}{literal}'+Math.ceil(newint/bp)*bp+'.00');
        });

        Y.all('#toname','#fromname').on('keyup', function(e){
            var targ = e.target;
            var junk = [':',')','-','!','@','#','$','%','^','&','*','(',')','_','+','=','-','`','~','{','}','|','[',']','\\',':','"',';','\'','<','>','?',',','.','/'];
            for (var jk in junk ) {
                targ.set('value', targ.get('value').replace(junk[jk],""));
            }
        });
    });
{/literal}
{/script}
