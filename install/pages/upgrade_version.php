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

$versions = array(	
	'2.0.0 Preview Release 2'=>'Gnarly Nebula',
	'2.0.0 Beta 1'=>'Efficient Entropy',
	'2.0.0 Beta 1.1'=>'Extremely Efficient Entropy',
);

?>

<h1><?php echo gt('Select Version to Upgrade From'); ?></h1>

<form method="post" action="index.php">
<input type="hidden" name="page" value="upgrade" />
	<div class="control">
		<select name="from_version" value="<?php echo EXPONENT; ?>">
		<?php
			foreach ($versions as $version=>$release) {
				echo '<option value="'.$version.'">';
				echo $version . ' ' . $release;
				if ($version == EXPONENT) {
					echo ' - '.gt('Previous Release');
				}
				echo '</option>';
				
				if ($version == EXPONENT) {
					break;
				}
			}
		?>
		</select>
	</div>
	<p>
		<?php echo gt('Select the version of Exponent that you are upgrading from.'); ?>
	</p>
	<p>
		<?php echo gt('Make sure that you choose the correct version.  Picking an incorrect version can destroy the data in your database.'); ?>
	</p>
	<button class="awesome large green"><?php echo gt('Upgrade'); ?></button>
</form>
