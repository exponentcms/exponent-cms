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

$i18n = exponent_lang_loadFile('install/pages/setlang.php');

?>
<h2 id="subtitle"><?php echo $i18n['title']; ?></h2>
<?php echo $i18n['guide']; ?>
<br /><br />

<form method="post" action="index.php">
	<!-- send us to the next page -->
	<input type="hidden" name="page" value="welcome" />
	<select name="lang">
		<?PHP foreach(exponent_lang_list() as $currid=>$currlang) {?>
			<?php if ($currid == "eng_US") { ?>
			<option value="<?PHP echo $currid?>" selected><?PHP echo $currlang?></option>
			<?php } else { ?>
			<option value="<?PHP echo $currid?>"><?PHP echo $currlang?></option>
			<?php } ?>
		<?PHP }?>
	</select>
	<input type="submit" value="<?php echo $i18n['setlang']; ?>" class="text" />
</form>