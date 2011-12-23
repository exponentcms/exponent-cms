{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
    <p>{'Gift Card amounts must be purchased in'|gettext} ${$product->base_price}.00 {'increments'|gettext}.</p>
    {form action="addItem"}
        {control type="hidden" name="product_type" value=$params.product_type}
        {control type="hidden" name="product_id" value=$params.product_id}
        {control type="text" id="dollar_amount" name="dollar_amount" label="Dollar Amount:"|gettext value=$record->dollar_amount size=7 filter=money}
        {control type="text" name="to" label="To:"|gettext value=$record->to}
        {control type="text" name="from" label="From:"|gettext value=$record->from}
        {control type="textarea" name="msg" label="Message:"|gettext rows=3 value=$record->msg}
        {control type="buttongroup" name="add2cart" submit="Add to cart"|gettext}
    {/form}
    
</div>
{script unique="a2cgc"}
{literal}
YAHOO.util.Event.onDOMReady(function(){
    var bp = {/literal}{$product->base_price};{literal}
    var da = YAHOO.util.Dom.get('dollar_amount');
    YAHOO.util.Event.on(da, 'blur', function(e,o){
        var newint = parseInt(this.value.replace('$',""));
        this.value = "$"+Math.ceil(newint/bp)*bp+".00";
    }, da, true);
    
    YAHOO.util.Event.on(['to','from'], 'keyup', function(e){
        var targ = YAHOO.util.Event.getTarget(e);
        var junk = [':',')','-','!','@','#','$','%','^','&','*','(',')','_','+','=','-','`','~','{','}','|','[',']','\\',':','"',';','\'','<','>','?',',','.','/'];
        for (var jk in junk ) {
            targ.value = targ.value.replace(junk[jk],"");
        }
        //console.debug(targ);
    });
    
});
{/literal}
{/script}
