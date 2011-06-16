{css unique="general-ecom" link="`$asset_path`css/creditcard-form.css"}

{/css}


<div class="billing-method authorized creditcard-form">
    <h4>Pay By Credit Card</h4>
    {form name="ccinfoform" id="ccinfoform" controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=$key}
        {$billing->form}
        <button id="continue-checkout" class="awesome blue large">Continue Checkout</button>  
    {/form}
</div>

{script unique="continue-checkout"}
{literal}
    YUI({ base:EXPONENT.URL_FULL+'external/yui3/build/',loadOptional: true}).use('node', function(Y) {
        //Y.one('#cont-checkout').setStyle('display','none');
        Y.one('#continue-checkout').on('click',function(e){
            e.halt();
            Y.one('#ccinfoform').submit();
        });
    });
{/literal}
{/script}