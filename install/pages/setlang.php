<?php

##################################################
#
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

if (!defined('EXPONENT')) exit('');

?>
<h1 id="subtitle"><?php echo gt('Please select a language'); ?></h1>
<p>
<?php echo gt('This will set the default Language for the installation process as well as your new Exponent website.'); ?>
</p>

<form method="post" action="index.php">
	<!-- send us to the next page -->
	<input type="hidden" name="page" value="welcome" />
	<div class="control">
	<select name="lang">
		<?PHP foreach(exponent_lang_list() as $currid=>$currlang) {?>
			<?php if ($currid == "eng_US") { ?>
			<option value="<?PHP echo $currid?>" selected><?PHP echo $currlang?></option>
			<?php } else { ?>
			<option value="<?PHP echo $currid?>"><?PHP echo $currlang?></option>
			<?php } ?>
		<?PHP }?>
	</select> 
	</div>
	<button class="awesome large green" /><?php echo gt('Set Language'); ?></button>
</form>