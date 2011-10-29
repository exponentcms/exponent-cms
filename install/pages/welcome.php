<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

?>
<h1><?php echo gt('Welcome to Exponent CMS'); ?></h1>
<p>
    <?php echo gt('The Exponent Development Team would like to thank you for downloading and installing the Exponent Content Management System. ').
		gt('We fervently hope that you will enjoy the power, flexibility, and ease-of-use that Exponent has to offer.') ?>
</p>

<!--<a class="awesome large green" href="?page=install-1">--><?php //echo gt('Begin Installation'); ?><!--</a>-->

<h1 id="subtitle"><?php echo gt('Please select a language'); ?></h1>
<p>
<?php echo gt('This will set the default Language for the installation process as well as your new Exponent website.'); ?>
</p>

<?php $lang = (defined('LANGUAGE')) ? "&lang='".LANGUAGE."'" : ''; ?>

<form method="post" action="index.php">
<!--	 send us to the next page -->
	<input type="hidden" name="page" value="install-1" />
	<div class="control">
		<select name="lang" onChange="Refresh(this.value)">
			<?PHP foreach(expLang::langList() as $currid=>$currlang) {?>
<!--				--><?php //if ($currid == "English - US") { ?>
				<?php if (isset($_REQUEST['lang']) && $currid == trim($_REQUEST['lang'])) { ?>
				<option value="<?PHP echo $currid?>" selected><?PHP echo $currlang?></option>
				<?php } else { ?>
				<option value="<?PHP echo $currid?>"><?PHP echo $currlang?></option>
				<?php } ?>
			<?PHP }?>
		</select>
	</div>
	<br />
	<button class="awesome large green" /><?php echo gt('Begin Installation in Selected Language'); ?></button>
</form>

<script type="text/javascript">
	function Refresh(id){
		location.href="index.php?lang=" + id
	}
</script>