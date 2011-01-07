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

<div class="module cart select-shipping-methods">
    <h1>{$moduletitle|default:"Select Shipping Methods"}</h1>    
    
    {form action=saveShippingMethods}
    {foreach from=$shipping_items item=shippingitem}
        <h2>Choose shipping</h2>     
        <table width=100% class="exp-skin-table" border="0" cellspacing="5" cellpadding="5">
            <thead>
            <tr>
                <th>
                    {$shippingitem->method->firstname}&nbsp;{$shippingitem->method->middlename}&nbsp;{$shippingitem->method->lastname},&nbsp
                    {$shippingitem->method->address1},&nbsp;{if $shippingitem->method->address2}{$shippingitem->method->address2},&nbsp;{/if}{$shippingitem->method->city},&nbsp
                    {$shippingitem->method->state|statename:abbv},&nbsp;{$shippingitem->method->zip}&nbsp

                    <ul class="items">
                    {foreach from=$shippingitem->orderitem item=product}
                        <li>{$product->products_name} - {currency_symbol}{$product->products_price}
                            {if $product->opts[0]}
                		    <h4>Selected Options</h4>
                		    <ul style="padding:0 0 0 15px;margin:0 0 5px 0;">
                		        {foreach from=$product->opts item=options}
                		            <li>{$options[1]}</li>
                		        {/foreach}
                		    </ul>
                		    {/if}
                        </li>
                    {/foreach}
                    </ul>
                    {control type="dropdown" name="calcs[`$shippingitem->method->id`]" class="smc`$shippingitem->method->id`" label="Shipping Service" items=$shipping->selectable_calculators includeblank="-- Select a Shipping Service --"}
                </th>
            </tr>
            </thead>
            <tbody id="tb-{$shippingitem->method->id}">
            {foreach from=$shippingitem->prices item=pricelist key=calcid}
                {foreach from=$pricelist item=option}
                <tr class="{cycle values="odd,even"} opts opt{$calcid}">
                    <td>
                    <div>                            
                        {control type=hidden name="cost[`$option.id`]" value=$option.cost}
                        {control type=hidden name="title[`$option.id`]" value=$option.title}
                        {control type="radio" name="methods[`$shippingitem->method->id`]" label="`$option.title` ($`$option.cost`)" value=$option.id}
                    </div>
                    
                    </td?
                </tr>
                {/foreach}
            {/foreach}                
            </tbody>
        </table>   
        
        {script unique="shippingopts`$shippingitem->method->id`"}
        {literal}
        YAHOO.util.Event.onDOMReady(function(){
            var smc = YAHOO.util.Dom.getElementsByClassName('{/literal}smc{$shippingitem->method->id}{literal}', 'select');
            YAHOO.util.Event.on(smc, 'change', function(e){
                var targ = YAHOO.util.Event.getTarget(e);
                var vals = YAHOO.util.Dom.getElementsByClassName('opt'+targ.value, 'tr', 'tb-{/literal}{$shippingitem->method->id}{literal}');
                var hvals = YAHOO.util.Dom.getElementsByClassName('opts', 'tr', 'tb-{/literal}{$shippingitem->method->id}{literal}');
                YAHOO.util.Dom.setStyle(hvals, 'display', 'none');
                if (YAHOO.env.ua.ie > 0) {
                    YAHOO.util.Dom.setStyle(vals, 'display', 'block');
                } else {
                    YAHOO.util.Dom.setStyle(vals, 'display', 'table-row');
                }
                
            });
        });
        {/literal}
        {/script}


    {/foreach}    
    {control type="buttongroup" submit="Continue"}
    {/form}
</div>


{script unique="shippingopts"}
{literal}
YAHOO.util.Event.onDOMReady(function(){
    var hvals = YAHOO.util.Dom.getElementsByClassName('opts', 'tr');
    var vals = YAHOO.util.Dom.getElementsByClassName('opt1', 'tr');
    
    YAHOO.util.Dom.setStyle(hvals, 'display', 'none');
    
    if (YAHOO.env.ua.ie > 0) {
        YAHOO.util.Dom.setStyle(vals, 'display', 'block');
    } else {
        YAHOO.util.Dom.setStyle(vals, 'display', 'table-row');
    }
    //YAHOO.util.Dom.setStyle(vals, 'display', 'block');
    
});
{/literal}
{/script}



