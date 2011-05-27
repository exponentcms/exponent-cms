<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
# Written and Designed by James Hunt
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

if (!defined('EXPONENT'))
    exit('');

$i18n = exponent_lang_loadFile('install/pages/admin_user.php');

?>
<h1><?php
echo gt('Create an Administrator');
?></h1>

<span style="color: red; font-weight: bold; padding-top: 8px;" id="errorMessage">
<?php echo isset($_GET['erremail']) == 'true' ? 'You must supply a valid email address.' : ''; ?>
</span>
<script>
function checkPassword(f){	
	if (f.password.value != f.password2.value) {
		//alert('<?php echo $i18n['bad_password_message']; ?>');
		document.getElementById("errorMessage").innerHTML = "<?php echo $i18n['bad_password_message']; ?>";
		return false;
	}else{
		f.submit();
		return true;
	}
}
</script>
<form method="post" onsubmit="return checkPassword(this);">
<input type="hidden" name="page" value="install-7" />
<div class="form_section">
	<div class="control">
		<span class="label"><?php echo $i18n['username']; ?>: </span>
		<input class="text" type="text" name="username" value="<?php echo $i18n['username_default']; ?>" />
		<div class="control_help">
			<?php echo $i18n['username_desc']; ?>
		</div>
	</div>
	<div class="control">
		<span class="label"><?php echo $i18n['password']; ?>: </span>
		<input class="text" type="password" name="password" value="" />
		<div class="control_help">
			<?php echo $i18n['password_desc']; ?>
		</div>
	</div>
		<div class="control">
		<span class="label"><?php echo $i18n['password2']; ?>: </span>
		<input class="text" type="password" name="password2" value="" />
		<div class="control_help">
			<?php echo $i18n['password_desc2']; ?>
		</div>
	</div>
	<div class="control">
		<span class="label"><?php echo $i18n['firstname']; ?>: </span>
		<input class="text" type="text" name="firstname" value="<?php echo $i18n['firstname_default']; ?>" />
	</div>
	<div class="control">
		<span class="label"><?php echo $i18n['lastname']; ?>: </span>
		<input class="text" type="text" name="lastname" value="<?php echo $i18n['lastname_default']; ?>" />
	</div>
	<div class="control">
		<span class="label"><?php echo $i18n['email']; ?>: </span>
		<input class="text" type="text" name="email" value="" />
	</div>
</div>
<button class="awesome large green"><?php echo gt('Create Administrator'); ?></button>
</form>
