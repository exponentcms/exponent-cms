{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{if $shipping->pricelist|is_array == true}
<div id="shipping-method-options">
    <img class="shippingmethodimg" src="{$shipping->calculator->icon}">
    <div class="sm-info">
        <strong class="selected-info">{$shipping->shippingmethod->option_title}
            <em>{currency_symbol}{$shipping->shippingmethod->shipping_cost|number_format:2}</em></strong>
        {if $shipping->pricelist|@count >1}
        <h4>{"Available Options"|gettext}</h4>
        <div class="bd">
            {form name="shpmthdopts" controller=shipping action=selectShippingOption}
            {foreach from=$shipping->pricelist item=option}
                {if $option.id == $shipping->shippingmethod->option}{assign var=selected value=true}{else}{assign var=selected value=false}{/if}
                {assign var=oc value=$option.cost|number_format:2}
                {control type=radio name="option" value=$option.id label="`$option.title` - $`$oc`" checked=$selected}
            {/foreach}
            <button type="submit" class="awesome small blue">{"Update Shipping Option"|gettext}</button>
            {/form}
        </div>
        {/if}
    </div>
</div>
<hr>
{else}
<div id="shipping-error" class="error">
    {$shipping->pricelist}
</div>
{/if}
