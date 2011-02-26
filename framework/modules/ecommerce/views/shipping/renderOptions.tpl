{if $shipping->pricelist|is_array == true}
<div id="shipping-method-options">
    <img class="shippingmethodimg" src="{$shipping->calculator->icon}">
    <div class="sm-info">
        <strong class="selected-info">{$shipping->shippingmethod->option_title}
            <em>${$shipping->shippingmethod->shipping_cost|number_format:2}</em></strong>
        <h4>{"Avalable Options"|gettext}</h4>
        <div class="bd">
            {form name="shpmthdopts" controller=shipping action=selectShippingOption}
            {foreach from=$shipping->pricelist item=option}
                {if $option.id == $shipping->shippingmethod->option}{assign var=selected value=true}{else}{assign var=selected value=false}{/if}
                {assign var=oc value=$option.cost|number_format:2}
                {control type=radio name="option" value=$option.id label="`$option.title` - $`$oc`" checked=$selected}
            {/foreach}
            <button type="submit" class="awesome small blue">{"Change Shipping Option"|gettext}</button>
            {/form}
        </div>
    </div>
</div>
{else}
<div id="shipping-error" class="error">
    {$shipping->pricelist}
</div>
{/if}
