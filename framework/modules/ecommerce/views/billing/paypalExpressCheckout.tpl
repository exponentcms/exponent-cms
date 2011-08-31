<div class="billing-method">
    <h4>Pay with Paypal</h4>
    
    {form controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=$calcid}
        <input type="image" name="submit" value="1" src="https://cms.paypal.com/cms_content/US/en_US/images/developer/US_AU_btn.gif">
    {/form}
</div>
