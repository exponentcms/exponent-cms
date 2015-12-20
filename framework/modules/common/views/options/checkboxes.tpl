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

<div id="aoptions{$group->id}" class="options checkboxes{if $group->required == true} required{/if}">
    <span class="{if bs3()||bs2()}control-label{else}label{/if}">{if $group->required == true}<span class="required" title="{'This entry is required'|gettext}">*&#160;</span>{/if}{$group->title}</span>
    {$gid=$group->id}
    {foreach from=$options item=option key=id}
        {if is_array($selected.$gid) && in_array($id, $selected.$gid)}  
            {control type="checkbox" name="options[`$group->id`][]" label=$option value=$id checked=true}
        {else}
            {control type="checkbox" name="options[`$group->id`][]" label=$option value=$id}
        {/if}
    {/foreach}
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

{script unique="aoptions`$group->id`" jquery=1}
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

    var checkbox = $('#aoptions{/literal}{$group->id}{literal}');
    if (checkbox.hasClass('required')) {
        $(checkbox.closest('form')[0]).on('submit', function(e) {
            if (!$('#aoptions{/literal}{$group->id}{literal} :checked').length) {
                alert('{/literal}{'You must select an option from the'|gettext} {$group->title} {'options before you can add this to your cart.'|gettext}{literal}');
                e.preventDefault();
            }
        });
    }
{/literal}
{/script}