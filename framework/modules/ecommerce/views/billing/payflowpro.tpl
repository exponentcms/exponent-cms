{css unique="general-ecom" link="`$asset_path`css/creditcard-form.css"}

{/css}


<div class="billing-method payflowpro creditcard-form">
    <h4>{'Pay By Credit Card'|gettext}</h4>
    {form name="ccinfoform`$key`" controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=$calcid}
        {$billing->form.$calcid}
        <button id="continue-checkout{$key}" type="submit" class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}">{"Continue Checkout"|gettext}</button>
    {/form}
</div>

{script unique="continue-checkout"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {    
    Y.one('#continue-checkout{/literal}$key{literal}').on('click',function(e){
        e.halt();
        Y.one('#ccinfoform{/literal}$key{literal}').submit();
    });
});
{/literal}
{/script}  