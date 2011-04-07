{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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
<div class="options dropdown"> 
    {assign var=gid value=$group->id} 
    {if isset($selected.$gid)}
        {control id="options`$group->id`" type=dropdown name="options[`$group->id`][]" label=$group->title items=$options includeblank=$includeblank value=$selected.$gid.0 required=$required}
    {else}    
        {control id="options`$group->id`" type=dropdown name="options[`$group->id`][]" label=$group->title items=$options includeblank=$includeblank value=$default required=$required}
    {/if}
</div>


{*script unique="optionselector"}
{literal}
YUI({base:EXPONENT.URL_FULL+'external/lissa/3.0.0/build/'}).use('*',function(Y) {
    
    Y.on('domready',function(){
    
        EXPONENT.pricediv = Y.one(".item-price");
        EXPONENT.baseprice = parseFloat(EXPONENT.pricediv.get('innerHTML'));

        var drops = Y.all('#prod{/literal}{$product->id}{literal} select');
    
        drops.on("change",function(e){
            var tmpprice = null;
            drops.each(function(k){
                var sv = EXPONENT.forms.getSelectValue(k.get("id"));
                if (typeof(EXPONENT.optPrices[sv])==="undefined") { EXPONENT.optPrices[sv] = 0 }
                if (typeof(EXPONENT.optModifyer[sv])==="undefined") { EXPONENT.optModifyer[sv] = 0 }
                tmpprice += parseInt(EXPONENT.optModifyer[sv]+parseFloat(EXPONENT.optPrices[sv]));
                
                //k.set("amount",EXPONENT.optPrices[EXPONENT.forms.getSelectValue(k.get("id"))]);
            });
            var total = tmpprice+EXPONENT.baseprice;
            EXPONENT.pricediv.set('innerHTML',total.toFixed(2));
        });
            
    })
});
{/literal}
{/script}

{script unique="aoptions`$group->id`"}
{literal}
if (typeof(EXPONENT.optPrices)=="undefined"){
    EXPONENT.optPrices = {};
}
if (typeof(EXPONENT.optModifyer)=="undefined"){
    EXPONENT.optModifyer = {};
}
var gID = {/literal}{$group->id}{literal};    
var options = {/literal}{obj2json obj=$group->option}{literal};
for (var i=0; i< options.length; i++) {
    EXPONENT.optPrices[options[i].id] = options[i].amount;
    EXPONENT.optModifyer[options[i].id] = options[i].updown;
}

{/literal}
{/script*}

