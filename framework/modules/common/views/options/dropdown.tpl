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

<div class="options dropdown"> 
    {$gid=$group->id}
    {if isset($selected.$gid)}
        {control id="options`$group->id`" type=dropdown name="options[`$group->id`][]" label=$group->title items=$options includeblank=$includeblank value=$selected.$gid.0 required=$required}
    {else}    
        {control id="options`$group->id`" type=dropdown name="options[`$group->id`][]" label=$group->title items=$options includeblank=$includeblank value=$default required=$required}
    {/if}
</div>

{script unique="optionselector" yui3mods="node,node-event-simulate"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        var pricediv = document.getElementById("item-price");
        if (pricediv != null) {
            var baseprice = pricediv.innerHTML.replace("$", "");
            var dropsprice = 0;
            var checksprice = 0;
            var drops = Y.all('#addtocart{/literal}{$product->id}{literal} select');
            if (drops.size() == 0) {
                drops = Y.all('#child-products-form select');
            }
            var calcdropprices = function(e){
                var tmpprice = 0;
                drops.each(function(k){
                    var e_opt = document.getElementById(k.get("id"));
                    var strUser = e_opt.options[e_opt.selectedIndex].value;

                    var sv = strUser;
                    if (typeof(EXPONENT.optPrices[sv])==="undefined") { EXPONENT.optPrices[sv] = 0 }
                    if (typeof(EXPONENT.optModifyer[sv])==="undefined") { EXPONENT.optModifyer[sv] = 0 }
                    var tmpval = parseFloat(EXPONENT.optPrices[sv]);
                    tmpprice += parseFloat(EXPONENT.optModifyer[sv]+tmpval);

                    //k.set("amount",EXPONENT.optPrices[EXPONENT.forms.getSelectValue(k.get("id"))]);
                });
                dropsprice = tmpprice;
                var total = parseFloat(tmpprice) + parseFloat(checksprice) + parseFloat(baseprice);
                pricediv.innerHTML = '$'+(total.toFixed(2));
            };
            drops.on("change",calcdropprices);
            calcdropprices();

            var checks = Y.all('#addtocart{/literal}{$product->id}{literal} .options.checkboxes input[type=checkbox]');
            if (checks.size() == 0) {
                checks = Y.all('#child-products-form .options.checkboxes input[type=checkbox]');
            }
            var calccheckprices = function(e){
                var tmpprice = 0;
                checks.each(function(k){
                    var sv  = k.get("value");

                    if (typeof(EXPONENT.optPrices[sv])==="undefined") { EXPONENT.optPrices[sv] = 0 }
                    if (typeof(EXPONENT.optModifyer[sv])==="undefined") { EXPONENT.optModifyer[sv] = 0 }

                    if(k.get("checked")) {
                        var tmpval = parseFloat(EXPONENT.optPrices[sv]);
                        tmpprice += parseFloat(EXPONENT.optModifyer[sv]+tmpval);
                    }
                    //k.set("amount",EXPONENT.optPrices[EXPONENT.forms.getSelectValue(k.get("id"))]);
                });
                checksprice = tmpprice;
                var total = parseFloat(tmpprice) + parseFloat(dropsprice) + parseFloat(baseprice);
                pricediv.innerHTML = '$'+(total.toFixed(2));
            };
            checks.on("change",calccheckprices);
            calccheckprices();
        }
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
{/script}
