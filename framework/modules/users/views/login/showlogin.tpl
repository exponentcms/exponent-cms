{css unique="login" link="../../assets/css/login.css"}

{/css}

<div class="login default">
{if $loggedin == false || $smarty.const.PREVIEW_READONLY == 1}
	<div class="box login-form one">
	    {if $smarty.const.USER_REGISTRATION_USE_EMAIL || $smarty.const.ECOM}
            {assign var=usertype value="Customers"}
            {assign var=label value="Email Address:"}
	    {else}
            {assign var=usertype value="Users"}
            {assign var=label value="Username:"}
	    {/if}
	    
		<h2>{"Existing"|gettext} {$usertype}</h2>
		<!--p>If you are an existing customer please log-in below to continue in the checkout process.</p-->
		{form action=login}
			{control type="text" name="username" label=$label size=25}
			{control type="password" name="password" label="Password:" size=25}
			{control type="buttongroup" submit="Log In"}
		{/form}
            {if $smarty.const.SITE_ALLOW_REGISTRATION == 1}
                {br}<a href="{link controller=users action=reset_password}">Forgot Your Password?</a><br />
            {/if}
	</div>

	{if $smarty.const.SITE_ALLOW_REGISTRATION || $smarty.const.ECOM}
	    <div class="box new-user two">
		    <h2>{"New"|gettext} {$usertype}</h2>
		    <p>
		        {if $smarty.const.ECOM}
                    {if $oicount>0}
    			        {"If you are a new customer, select this option to continue with the checkout process."|gettext}{br}{br}
                        {"We will gather billing and shipping information, and you will have the option to create an account so can track your order status."|gettext}{br}{br}
                        <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link module=cart action=customerSignup}">{"Continue Checking Out"|gettext}</a>
                    {else}
    			        {"If you are a new customer, add an item to your cart to continue with the checkout process."|gettext}{br}{br}
                        <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{backlink}">{"Keep Shopping"|gettext}</a>
                    {/if}               
			    {else}
                    {"Create a new account here."|gettext}{br}{br}
                    <a class="awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE}" href="{link controller=users action=create}">{"Create an Account"|gettext}</a>
		        {/if}			    
		    </p>
	    </div>
	{/if}
{/if}
</div>
