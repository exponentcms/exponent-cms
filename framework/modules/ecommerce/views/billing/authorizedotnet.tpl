{css unique="general-ecom" link="`$asset_path`css/creditcard-form.css"}

{/css}


<div class="billing-method authorized creditcard-form">
    <h4>{'Pay By Credit Card'|gettext}</h4>
    {form name="ccinfoform" id="ccinfoform" controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=$calcid}
        {$billing->form.$calcid}
        <button id="continue-checkout" type="submit" class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}">{'Continue Checkout'|gettext}</button>
    {/form}
</div>

{*script unique="continue-checkout"}
{literal}
    
    YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
        //Y.one('#cont-checkout').setStyle('display','none');
        Y.one('#continue-checkout').on('click',function(e){
            e.halt();
            Y.one('#ccinfoform').submit();
        });
    });
{/literal}
{/script*}