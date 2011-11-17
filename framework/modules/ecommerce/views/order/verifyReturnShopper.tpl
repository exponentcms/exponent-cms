{css unique="verifyshopper" link="`$asset_path`css/verifyshopper.css" corecss="button"}

{/css}

<div class="module order verifyReturnShopper">
    <div class="top">
        <h1>Welcome back {$firstname}!</h1>
        <p>
            We see that you have shopped with us before. You can either restore your shopping cart and pick up where you left off, or start your shopping experience over with a fresh cart.
        </p>
    </div>

    <div class="col one">
        <h3>Verify the following information from your previous session to restore your shopping cart:{br}</h3>
    
        {form name="verifyAndRestoreCartForm" controller="order" action="verifyAndRestoreCart"}
           {control type="text" name="lastname" id="lastname" label="Last Name:"|gettext}
           {control type="text" name="email" id="email" label="Email Address:"|gettext}
           {control type="text" name="zip_code" id="zip_code" label="Zip Code:"|gettext}
           {control type="buttongroup" submit="Verify"|gettext}
        {/form}
    </div>

    <div class="col two">
        <h3>I am not {$firstname}, or I would like to start over with a fresh shopping cart.</h3>
        <p>
            <a class="awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE}" href="{link controller='order' action='clearCart' id=$order->id}">Start a New Shopping Cart</a>
        </p>
    </div>    
        
</div>

{script unique="verify-submit-form"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
    //alert("HERE");
    Y.one('#submit-verify').on('click',function(e){
    //alert("Here");
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
