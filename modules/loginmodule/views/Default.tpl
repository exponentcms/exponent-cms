{css unique="login" link="`$smarty.const.PATH_RELATIVE`modules/loginmodule/assets/css/login.css" corecss="forms"}

{/css}

<div class="loginmodule default">
{if $loggedin == false || $smarty.const.PREVIEW_READONLY == 1}
	<div class="box login-form">
	    {if $smarty.const.USER_REGISTRATION_USE_EMAIL == 1}
            {assign var=usertype value="Customers"}
            {assign var=label value="Email Address:"}
	    {else}
            {assign var=usertype value="Users"}
            {assign var=label value="Username:"}
	    {/if}
	    
	    
	    
		<h2>Existing {$usertype}</h2>
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
	{if $smarty.const.SITE_ALLOW_REGISTRATION == 1}
	<div class="box new-user">
		<h2>New Customers</h2>
		<p>
			If you are a new customer please create an account to continue in the checkout process.{br}
			Creating an account will allow you to save your billing and shipping information and track your order status.{br}{br}
		</p>
			<a href="{link module=users action=create}">- Create your account here -</a><br />
	</div>
	{/if}
{/if}
</div>
