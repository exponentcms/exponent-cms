<div class="billing-method">
    <h4>Pay with Worldpay</h4>
    
    {form controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=7}
		<input type="submit" value="Worldpay Checkout" name="submit" />
    {/form}
</div>