{css unique="general-ecom" link="`$asset_path`css/creditcard-form.css"}

{/css}

<div class="billing-method">
    <h4>Passthru Billing</h4>
    {* eDebug var=$default_order_type}
    {eDebug var=$order_types *}
    {form name="passthruform" controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=6}
        {* control type=radiogroup columns=1 name="passthru_order_type" label="Select Order Type" items="Standard Order (your user),Phone Order (creates new user),Save as Quote (creates new user)" values="0,1,2" default=0 *}        
        <table><tr><td width="150" style="vertical-align:top;">
            {control type=radiogroup columns=1 name="order_type" label="Select Order Type" items=$order_types default=$default_order_type flip=false}        
            </td><td style="vertical-align:top;">
            {control type=radiogroup columns=1 name="order_status" label="Select Order Status" items=$order_statuses default=$default_order_status flip=false}        
            </td><td style="vertical-align:top;">
            {control type=radiogroup columns=1 name="sales_rep_1_id" label="Select Sales Rep 1" items=$sales_reps flip=false}                    
            {control type=radiogroup columns=1 name="sales_rep_2_id" label="Select Sales Rep 2" items=$sales_reps flip=false}                    
            {control type=radiogroup columns=1 name="sales_rep_3_id" label="Select Sales Rep 3" items=$sales_reps flip=false}        
        </td></tr></table>
        <a href="#" id="continue-passthru-checkout" class="exp-ecom-link"><strong><em>Continue To Last Step</em></strong></a>        
    {/form}
    <div style="clear:both;"></div>
</div>

{script unique="continue-passthru-checkout"}
{literal}
    YUI({ base:EXPONENT.URL_FULL+'external/yui3/build/',loadOptional: true}).use('node', function(Y) {
        //Y.one('#cont-checkout').setStyle('display','none');
        Y.one('#continue-passthru-checkout').on('click',function(e){
            e.halt();
            Y.one('#passthruform').submit();
        });
    });
{/literal}
{/script}