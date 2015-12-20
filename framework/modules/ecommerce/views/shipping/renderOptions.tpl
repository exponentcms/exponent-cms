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
{*{edebug var=$shipping->pricelist}*}
{if $shipping->pricelist|is_array == true}
    <div id="shipping-method-options">
        {if !$shipping->calculator->multiple_carriers}
            <img class="shippingmethodimg" src="{$shipping->calculator->icon}">
            <div class="sm-info">
                <strong class="selected-info">{$shipping->shippingmethod->option_title}&#160;<em>{$shipping->shippingmethod->shipping_cost|currency}</em></strong>
                {if $shipping->pricelist|@count >1 && (!$order->forced_shipping || empty($shipping->shippingmethod->option))}
                    {pop id="change_shipping" text="Change Shipping Option"|gettext title="Shipping Options"|gettext buttons="Close"|gettext}
                        {group label="Available Options"|gettext}
                            <div class="bd">
                                {form name="shpmthdopts" controller=shipping action=selectShippingOption}
                                    {foreach from=$shipping->pricelist item=option}
                                        {if $option.id == $shipping->shippingmethod->option || $option.title == $shipping->shippingmethod->option_title}{$selected=true}{else}{$selected=false}{/if}
                                        {$oc=$option.cost|number_format:2}
                                        {control type=radio name="option" columns=1 value=$option.id label="`$oc|currency` - `$option.title`" checked=$selected}
                                    {/foreach}
                                    <button type="submit" class="{button_style color=blue size=small}">{"Update Shipping Option"|gettext}</button>
                                {/form}
                            </div>
                        {/group}
                    {/pop}
                {/if}
            </div>
        {else}
            {$car = explode(':', $shipping->shippingmethod->option)}
            {$car0 = $car.0}
            <img class="shippingmethodimg" src="{$shipping->calculator->icon.$car0}">
            <div class="sm-info">
                <strong class="selected-info">{$car.0} {$shipping->shippingmethod->option_title}&#160;<em>{$shipping->shippingmethod->shipping_cost|currency}</em></strong>
            </div>
            {pop id="change_shipping" type=form text="Change Shipping Option"|gettext title="Shipping Options"|gettext width="1024px"}
                {form name="shpmthdopts" controller=shipping action=selectShippingOption}
                    {$width = 100 / count($shipping->pricelist) - 1}
                    {foreach $shipping->pricelist as $carrier=>$carriers}
                        <span style="width: {$width}%;display: inline-block;vertical-align: top">
                            {if $carriers|@count >1 && (!$order->forced_shipping || empty($shipping->shippingmethod->option))}
                                <img class="" src="{$shipping->calculator->icon.$carrier}">{br}
                                <div class="">
                                    {group label="Available Options"|gettext}
                                        <div class="bd">
                                            {foreach from=$carriers item=option}
                                                {if $option.id == $shipping->shippingmethod->option || $option.title == $shipping->shippingmethod->option_title}{$selected=true}{else}{$selected=false}{/if}
                                                {$oc=$option.cost|number_format:2}
                                                {control type=radio name="option" columns=1 value=$option.id label="`$oc|currency` - `$option.title`" checked=$selected}
                                            {/foreach}
                                        </div>
                                    {/group}
                                </div>
                            {/if}
                        </span>
                    {/foreach}
                    <div>
                        <button type="submit" class="{button_style color=blue size=small}">{"Update Shipping Option"|gettext}</button>
                    </div>
                {/form}
            {/pop}
        {/if}
    </div>
    {*<hr>*}
{else}
    <div id="shipping-error" class="error">
        {$shipping->pricelist}
    </div>
{/if}
