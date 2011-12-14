{css unique="login" link="`$asset_path`css/flyout.css"}

{/css}

{script unique="flyout" type="text/javascript" yui3mods="1"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
 Y.on('domready', function() {
	Y.one('.triggerlogin').on('click', function() {
		Y.one('.panel').toggleView();
		Y.one(this).toggleClass('active');
		return false;
	});
 });
});
{/literal}
{/script}

<div class="login flyout panel" style="display: none;">
	{if $loggedin == false || $smarty.const.PREVIEW_READONLY == 1}
		<div class="box login-form one">
			{if $smarty.const.USER_REGISTRATION_USE_EMAIL || $smarty.const.ECOM}
				{assign var=usertype value="Customers"|gettext}
				{assign var=label value="Email Address"|gettext|cat:":"}
			{else}
				{assign var=usertype value="Users"|gettext}
				{assign var=label value="Username"|gettext|cat:":"}
			{/if}

			<h2>{"Existing"|gettext} {$usertype}</h2>
			<!--p>If you are an existing customer please log-in below to continue in the checkout process.</p-->
			{form action=login}
				{control type="text" name="username" label=$label size=25}
				{control type="password" name="password" label="Password"|gettext|cat:":" size=25}
				{control type="buttongroup" submit="Log In"|gettext}
			{/form}
			{if $smarty.const.SITE_ALLOW_REGISTRATION == 1}
				{br}<a href="{link controller=users action=reset_password}">{'Forgot Your Password'|gettext}?</a><br />
			{/if}
			{br}
		</div>

		{if $smarty.const.SITE_ALLOW_REGISTRATION || $smarty.const.ECOM}
			<div class="box new-user two">
				<h2>{"New"|gettext} {$usertype}</h2>
				<p>
					{if $smarty.const.ECOM}
						{if $oicount>0}
							{"If you are a new customer, select this option  <br />to continue with the checkout process."|gettext}{br}{br}
							{"We will gather billing and shipping information, <br />and you will have the option to create an account  <br />so can track your order status."|gettext}{br}{br}
							<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link module=cart action=customerSignup}">{"Continue Checking Out"|gettext}</a>
						{else}
							{"If you are a new customer,add an item to your cart  <br />to continue with the checkout process."|gettext}
						{/if}
					{else}
						{"Create a new account here."|gettext}{br}{br}
						<a class="awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE}" href="{link controller=users action=create}">{"Create an Account"|gettext}</a>
					{/if}
				</p>
			</div>
		{/if}
		</div>
		<a class="triggerlogin" href="#">Login</a>
	{else}
		<div class="box login-form one">
			<b>{'Welcome'|gettext|cat:', %s'|sprintf:$displayname}</b>{br}{br}
			<a class="profile" href="{link controller=users action=edituser id=$user->id}">{'Edit Profile'|gettext}</a>{br}
			{if $is_group_admin}
				<a class="groups" href="{link controller=users action=manage_group_memberships}">{'My Groups'|gettext}</a>{br}
			{/if}
			<a class="password" href="{link controller=users action=change_password ud=$user->id}">{'Change Password'|gettext}</a>{br}
			<a class="logout" href="{link action=logout}">{'Logout'|gettext}</a>{br}
			<a class="{$previewclass}" href="{link controller=administration action=toggle_preview}">{$previewtext}</a>{br}
		</div>
		</div>
		<a class="triggerlogin" href="#">&nbsp;&nbsp;&nbsp;{$displayname}</a>
	{/if}
