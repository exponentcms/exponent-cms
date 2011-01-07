<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

$versions = array(	
	'1.99.0'=>'Origin',
	'1.99.1'=>'Secondsies',
	'1.99.2'=>'Hopscotch',
);

$i18n = exponent_lang_loadFile('install/pages/upgrade_version.php');

?>

<form method="post" action="index.php">
<input type="hidden" name="page" value="upgrade" />

<div class="form_section_header"><?php echo $i18n['select_ver']; ?></div>
<div class="form_section">
	<div class="control">
		<select name="from_version" value="<?php echo EXPONENT; ?>">
		<?php
			foreach ($versions as $version=>$release) {
				echo '<option value="'.$version.'">';
				echo $version . ' ' . $release;
				if ($version == EXPONENT) {
					echo ' - '.$i18n['prev_rel'];
				}
				echo '</option>';
				
				if ($version == EXPONENT) {
					break;
				}
			}
		?>
		</select>
		<div class="control_help">
			<?php echo $i18n['select_version']; ?>
			<br /><br />
			<div class="important_message">
				<?php echo $i18n['choose_correct']; ?>
			</div>
		</div>
		<input type="submit" value="<?php echo $i18n['upgrade']; ?>" class="text" />
	</div>
</div>
</form>
