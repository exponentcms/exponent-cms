<div class="billing-method">
    <h4>{'Pay with Worldpay'|gettext}</h4>
    
    {form controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=7}
		<input type="image" name="submit" value="1" src="{$smarty.const.PATH_RELATIVE|cat:'framework/modules/ecommerce/assets/images/worldpay.gif'}">
    {/form}
</div>