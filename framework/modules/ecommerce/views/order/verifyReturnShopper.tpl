<div class="module order verifyReturnShopper" style="width: 600px; margin:auto;">
    <div style="vertical-align: top;">
        <div>
        <h1>Welcome back {$firstname}!</h1>
        We see that you have shopped with us before. {br}{br}Would you like to restore your shopping cart and pick up where you left off, or start over?
        {br}
        </div>
        {br}
        <div style="width: 280px; height:290px; border:2px solid #7C6F4A; float: right; padding: 5px;">
        <h3>I am not {$firstname}, or I would like to start over with a fresh shopping cart.</h3>{br}{br}
        <a class="exp-ecom-link" href="{link controller='order' action='clearCart' id=$order->id}"><strong><em>Click Here To Start a New Shopping Cart</em></strong></a>
        </div>    
        <div style="width: 280px; height:290px; border:2px solid #7C6F4A; padding: 5px;">
        <h3>Verify the following information from your previous session to restore your shopping cart:{br}</h3>
        
        {form name="verifyAndRestoreCartForm" controller="order" action="verifyAndRestoreCart"}
               {control type="text" name="lastname" id="lastname" label="Last Name:"}
               {control type="text" name="email" id="email" label="Email Address:"}
               {control type="text" name="zip_code" id="zip_code" label="Zip Code:"}
               {control type="submit" name="submit" value="Veriy"}
        {/form}
        {br}
        <a id="submit-verify" class="exp-ecom-link" href="javascript:{ldelim}{rdelim}" rel="nofollow"><strong><em>Verify and Restore My Shopping Cart</em></strong></a>
        
        <!--a id="submit-chiprods" href="javascript:{ldelim}{rdelim}" class="addtocart exp-ecom-link" rel="nofollow"><strong><em>Add selected items to cart</em></strong></a-->
        {script unique="verify-submit-form"}
        {literal}
        YUI({ base:EXPONENT.URL_FULL+'external/yui3/build/',loadOptional: true}).use('node', function(Y) {
            alert("HERE");
            Y.one('#submit-verify').on('click',function(e){
            alert("Here");
                e.halt();
                var frm = Y.one('#verifyAndRestoreCartForm');  
                var ln = Y.one('#lastname');
                var em = Y.one('#email');
                var zc = Y.one('#zip_code');                
                
                if(ln.get('value) == '')
                {
                    alert("Please verify your Last Name to continue.");
                    return false;
                }
                if(em.get('value) == '')
                {
                    alert("Please verify your Email to continue.");
                    return false;
                }
                if(zc.get('value) == '')
                {
                    alert("Please verify your Zip Code to continue.");
                    return false;
                }
                frm.submit();                
            });                        
            
        });
        {/literal}
        {/script}
        {br}
        </div>
        
    </div>
</div>
