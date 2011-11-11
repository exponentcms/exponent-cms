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

?>
<div id="hd">
   <h1><?php echo gt('System Requirements Explained'); ?></h1>
</div>
<div id="bd">
<!--<div id="leftcol">-->
<p><?php echo gt('The sanity checks are in place to ensure that problems with the server environment (file permissions, PHP extensions, etc) are suitable for installing Exponent.').
    gt('This page explains each of the sanity checks, why it is performed, and how to reconfigure your web server if the check fails.').'<br /><br />'.
    gt('Note: In all of the solutions').', <span class="var">WEBUSER</span> '.gt('is used for the username of the user running the web server, and').
    ' <span class="var">EXPONENT</span> '.gt('is used as the full path to the Exponent directory.'); ?></p>
<table cellspacing="0" cellpadding="3" rules="all" border="0" style="border:1px solid grey;" width="100%" class="exp-skin-table">
<tr><td colspan="2" style="background-color: lightgrey;"><b><?php echo gt('File and Directory Permission Tests'); ?></b></td></tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_conf-configphp" />conf/config.php</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('The conf/config.php file stores the active configuration for the site, including database connection settings and choice of theme.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/conf/<br />
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/conf/config.php
		</div>
	</td>
</tr>

<!--<tr>-->
<!--	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_conf-profiles" />conf/profiles</td>-->
<!--	<td class="bodytext" valign="top">-->
<!--		<div class="sanity_req">--><?php //echo gt('Must be readable and writable by web server'); ?><!--</div>-->
<!--		<br />-->
<!--		--><?php //echo gt('The conf/profiles directory stores the saved configurations for the site.  Even if you do not use more than one profile, the web server must be able to create files in this directory.'); ?>
<!--		<br />-->
<!--		<br />-->
<!--		<b>--><?php //echo gt('UNIX Solution'); ?><!--:</b>-->
<!--		<div class="sanity_shell">-->
<!--			chown -R <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/conf/profiles/-->
<!--		</div>-->
<!--	</td>-->
<!--</tr>-->
<!--<tr>-->
<!--	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_overridesphp" />overrides.php</td>-->
<!--	<td class="bodytext" valign="top">-->
<!--		<div class="sanity_req">--><?php //echo gt('Must be readable and writable by web server'); ?><!--</div>-->
<!--		<br />-->
<!--		--><?php //echo gt('The overrides.php file is used to override constants that are automagically detected by Exponent.  If the installer finds a problem with some auto-detected values, it will write the correct values out to this file before completing the installation.  After you have installed Exponent, this file only needs to be readable by the web server.'); ?>
<!--		<br />-->
<!--		<br />-->
<!--		<b>--><?php //echo gt('UNIX Solution'); ?><!--:</b>-->
<!--		<div class="sanity_shell">-->
<!--			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/overrides.php-->
<!--		</div>-->
<!--	</td>-->
<!--</tr>-->
<!--	-->

<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_files" />files/</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('All uploaded content files (resources, importer data, images, etc.) are stored in the site files/ directory, which the web server needs fill read and write access to.  If this test is failing and you think it shouldn\'t be, remember that you must recursively assign read and write permissions to the web server user.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/files
		</div>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_install" />install/</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('The install directory contains all of the files for the Exponent Install Wizard.  Once you have gone through the wizard once, it disables itself automatically (by removing the install/not_configured file).  To do this, it needs write permission on the install directory.  After installation, this directory isn\'t even needed, so you can remove it or set the permissions such that the web server cannot read it.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/install
		</div>
	</td>
</tr>

<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_modules" />framework/modules/</td>
	<td class="bodytext" valign="top">
		<?php echo gt('Exponent runs a few checks against the installed modules to make sure that nothing strange is encountered.  If this test fails, please Create a Ticket on the').' <a href="http://exponentcms.lighthouseapp.com/projects/61783-exponent-cms/tickets/new" target="_blank">'.gt('Exponent Lighthouse page').'</a>.'; ?>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_modules1" />framework/modules-1/</td>
	<td class="bodytext" valign="top">
		<?php echo gt('Exponent runs a few checks against the installed (old-school) modules to make sure that nothing strange is encountered.  If this test fails, please Create a Ticket on the').' <a href="http://exponentcms.lighthouseapp.com/projects/61783-exponent-cms/tickets/new" target="_blank">'.gt('Exponent Lighthouse page').'</a>.'; ?>
	</td>
</tr>

<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_tmp" />tmp/</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('The tmp directory is used by various parts of Exponent for temporary files.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/tmp
		</div>
	</td>
</tr>

<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_extensionuploads" />tmp/extensionuploads/</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('When you use the Upload Extension feature of the Administrator Control Panel, the uploaded archive is placed in tmp/extensionuploads directory temporarily.  Therefore, the web server needs full access to this.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/tmp/extensionuploads
		</div>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_views_c" />tmp/views_c/</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('Exponent uses Smarty to separate its data processing logic from its display logic.  Smarty templates are compiled from Smarty syntax into raw PHP for speed, and the compiled templates all go in the tmp/views_c directory, which must be writable by the web server.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/tmp/views_c
		</div>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_cache" />tmp/cache/</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('Exponent Smarty templates can be cached for speed, and the cached templates all go in the tmp/cache directory, which must be writable by the web server.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/tmp/cache
		</div>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_minify" />tmp/minify/ <br /> tmp/css/</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('Exponent can use \'Minification\' for speed by compressing and compiling stylesheets in the tmp/minfy directory, which must be writable by the web server.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/tmp/minfy<br>
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/tmp/css
		</div>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_rss" />tmp/rsscache/</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('Exponent can pull rss feeds into the news module.  These pulled feeds are cached in the tmp/rsscache directory, which must be writable by the web server.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/tmp/rsscache
		</div>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_image_cache" />tmp/image_cache/</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('Exponent creates image thumbnails \'on the fly\'.  These thumbnails are cached in the tmp/image_cache directory, which must be writable by the web server.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/tmp/image_cache
		</div>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="fdp_pixidou" />tmp/pixidou/</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('Exponent includes the Pixidou image editor which can be used from the included File Manager.  Pixidou uses the tmp/pixidou directory for editing files, which must be writable by the web server.'); ?>
		<br />
		<br />
		<b><?php echo gt('UNIX Solution'); ?>:</b>
		<div class="sanity_shell">
			chown <span class="var">WEBUSER</span> <span class="var">EXPONENT</span>/tmp/pixidou
		</div>
	</td>
</tr>
</table>

<br /><br />

<table cellspacing="0" cellpadding="3" rules="all" border="0" style="border:1px solid grey;" width="100%" class="exp-skin-table">
<tr><td colspan="2" style="background-color: lightgrey;"><b><?php echo gt('Other Tests'); ?></b></td></tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="o_db" /><?php echo gt('Database Backend'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('Exponent stores all the content for your website in a relational database.  For portability reasons, a custom database abstraction layer is used.  Currently, this abstraction layer only supports MySQL.  If this test fails, then PHP support for these database engines was not detected.'); ?>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="o_gd" /><?php echo gt('GD Graphics Library'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('Various parts of Exponent utilize the GD Graphics library for imaging functions.  Exponent can operate without GD, but you will lose such features as Captcha tests and automatic thumbnails.  A version of GD that is 2.0.x compatible will give you sharper and crisper thumbnails.'); ?>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="o_php" />PHP 5.2.1+</td>
	<td class="bodytext" valign="top">
		<?php echo gt('Because of some of the functions that Exponent uses, versions of PHP prior to 5.2.1 are not suitable.  Most functions that are supported in later versions have workarounds, but there are a few major bugs, and functions that can\'t be re-implemented in PHP prior to 5.2.1.'); ?>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="o_zlib" /><?php echo gt('ZLib Support'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('ZLib is used for archive support, which Exponent uses for uncompressing Tar and Zip archives.'); ?>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="o_xml" /><?php echo gt('XML (Expat) Library Support'); ?>)</td>
	<td class="bodytext" valign="top">
		<?php echo gt('The web services extensions for Exponent require the Expat Library.  If you are not using web services or module that are dependent on web services, this is a safe warning to ignore.'); ?>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="o_safemode" /><?php echo gt('Safe Mode Not Enabled'); ?></td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Safe Mode is a security measure present in some Shared Hosting Environments.  It limits a PHP scripts from including or modifying files that are not owned by that script\'s owner.  This can cause serious and subtle problems that look like bugs if Exponent\'s files are not properly set up.<br /><br />If you decide to ignore this warning, make sure that ALL files included in the Exponent package are owned by the same system user.'); ?></div>
		<br />
		<?php echo gt('Exponent works best when Safe Mode is disabled'); ?>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="o_openbasedir" /><?php echo gt('Open BaseDir Not Enabled'); ?></td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Exponent works best when Open BaseDir is disabled'); ?></div>
		<br />
		<?php echo gt('The open_basedir restriction is a security measure present in some Shared Hosting Environments.  It limits PHP scripts from dealing with files outside of a given directory.  This may cause some problems with some of Exponent\'s file operations, including the Multi-Site manager.  Ignore this error at your own risk.'); ?>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="o_upload" /><?php echo gt('File Uploads Enabled'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('Server administrators have the option of disabling PHP uploads.  Additionally, misconfigured servers may have problems processing uploaded files.  Without the ability to upload files, your experience with Exponent will be severely limited, since you will be unable to upload new code, patches, or images and resources.'); ?>
	</td>
</tr>
<tr>
	<td class="bodytext" style="font-weight: bold;" valign="top"><a name="o_tmpfile" /><?php echo gt('Temporary File Creation'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('Various parts of Exponent have to create temporary files to accomplish a given task.  Usually, this error is related to the \'tmp/\' file and directory permission test, above.'); ?>
	</td>
</tr>
</table>
<!--</div>-->
</div>