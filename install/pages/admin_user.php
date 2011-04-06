<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc., Maxim Mueller
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

if (!defined('EXPONENT')) exit('');

$i18n = exponent_lang_loadFile('install/pages/admin_user.php');

?>
<h2 id="subtitle"><?php echo $i18n['subtitle']; ?></h2>
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
<input type="hidden" name="page" value="save_admin" />
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
<?php
//FJD - this isn't working well and it's unnecessary

//		Written by Michael Bernd, adapted by Maxim Mueller for eXp
//		Public Domain
//
//		letzte �nderung: Michael Berndt - Berlin
//		Montag der 09. Mai. 2005
//		08:01:53
//
// What is a secure password?
//
// This password has a minimum number of
// - 8 characters
// - at least 1 number
// - at least 1 special character
// - at least 1 mutation
// - at least 1 small alphabetic character
// - at least 1 capital alphabetic character

/*function Berndt ($passwd_length){
	$s[0]=array('0','1','2','3','4','5','6','7','8','9',);
	$s[1]=array('!','"','�','%','/','(',')','=','?','`',
			'#','+','*','~',';','.','-','|','<','>','^','�');
	$s[2]=array('A','B','C','D','E','F','G','H','I','J','K',
			'L','M','N','O','P','Q','R','S','T','U','V',
			'W','X','Y','Z');
	$s[3]=array('a','b','c','d','e','f','g','h','i','j','k',
			'l','m','n','o','p','q','r','s','t','u','v',
			'w','x','y','z');
	$s[4]=array('�','�','�','�','�','�','�');

	$l=count($s);

	$a=array_merge($s[0],$s[1],$s[2],$s[3],$s[4]);
	shuffle($a);
	for($i=0;$i< $passwd_length;$i++){
		if($i>=$l){
			$p[]=$a[$i];
		}else{
			shuffle($s[$i]);
			$p[]=$s[$i][0];
		}
	}
	shuffle($p);
	
	return implode('',$p);
}

	echo Berndt(8);
*/
?>
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
<input type="submit" value="<?php echo $i18n['continue']; ?>" class="text" />
</form>
