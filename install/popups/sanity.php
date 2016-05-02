<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
    gt('This page explains each of the sanity checks, why it is performed, and how to reconfigure your web server if the check fails.').'<br /><br />',
    gt('Note: In all of the solutions'),', <span class="var">WEBUSER</span> ',gt('is used for the username of the user running the web server,'),
	' <span class="var">WEBGROUP</span> ',gt('is used for the groupname of the group running the web server, and'),
    ' <span class="var">EXPONENT</span> ',gt('is used as the full path to the Exponent directory.'); ?></p>
<table cellspacing="0" cellpadding="3" rules="all" border="0" style="border:1px solid grey;" width="100%" class="exp-skin-table">
<tr><td colspan="2" style="background-color: lightgrey;"><strong><?php echo gt('File and Directory Permission Tests'); ?></strong></td></tr>
<tr>
	<td id="fdp_conf-configphp" class="bodytext" style="font-weight: bold;" valign="top">framework/conf/config.php <br /> framework/conf/profiles <br /> overrides.php
		<br /> files/ <br /> files/avatars/ <br /> files/uploads/ <br /> install/ <br /> tmp/ <br /> tmp/cache <br /> tmp/css <br /> tmp/elfinder <br />
			tmp/extensionuploads <br /> tmp/img_cache <br /> tmp/minify <br /> tmp/pixidou <br /> tmp/rsscache <br /> tmp/views_c</td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Must be readable and writable by web server'); ?></div>
		<br />
		<?php echo gt('These directories and files are used by various parts of Exponent and for temporary.'); ?>
		<br />
		<br />
		<strong><?php echo gt('UNIX Solution'); ?>:</strong>
		<div class="sanity_shell">
			chown -R <span class="var">WEBUSER</span> <span class="var">EXPONENT</span><br />
			find <span class="var">EXPONENT</span> -type d -exec chmod 755 {} \;<br />
			find <span class="var">EXPONENT</span> -type f -exec chmod 644 {} \;
		</div>
		<br />
		<strong><?php echo gt('If that doesn\'t work, you\'ll need to open up group write access'); ?>:</strong>
		<div class="sanity_shell">
			chown -R <span class="var">WEBUSER</span>:<span class="var">WEBGROUP</span> <span class="var">EXPONENT</span><br />
			find <span class="var">EXPONENT</span> -type d -exec chmod 775 {} \;<br />
			find <span class="var">EXPONENT</span> -type f -exec chmod 664 {} \;
		</div>
	</td>
</tr>
</table>

<br />

<table cellspacing="0" cellpadding="3" rules="all" border="0" style="border:1px solid grey;" width="100%" class="exp-skin-table">
<tr><td colspan="2" style="background-color: lightgrey;"><strong><?php echo gt('Other Tests'); ?></strong></td></tr>
<tr>
	<td id="o_db" class="bodytext" style="font-weight: bold;" valign="top" width="25%"><?php echo gt('Database Backend'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('Exponent stores all the content for your website in a relational database.  For portability reasons, a custom database abstraction layer is used.  Currently, this abstraction layer only supports MySQL.  If this test fails, then PHP support for these database engines was not detected.'); ?>
	</td>
</tr>
<tr>
	<td id="o_gd" class="bodytext" style="font-weight: bold;" valign="top"><?php echo gt('GD Graphics Library'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('Various parts of Exponent utilize the GD Graphics library for imaging functions.  Exponent can operate without GD, but you will lose such features as Captcha tests and automatic thumbnails.  A version of GD that is 2.0.x compatible will give you sharper and crisper thumbnails.'); ?>
	</td>
</tr>
<tr>
	<td id="o_php" class="bodytext" style="font-weight: bold;" valign="top">PHP 5.3.1+</td>
	<td class="bodytext" valign="top">
		<?php echo gt('Because of some of the functions that Exponent uses, versions of PHP prior to 5.3.1 are not suitable.  Most functions that are supported in later versions have workarounds, but there are a few major bugs, and functions that can\'t be re-implemented in PHP prior to 5.3.1.'); ?>
	</td>
</tr>
<tr>
	<td id="o_zlib" class="bodytext" style="font-weight: bold;" valign="top"><?php echo gt('ZLib Support'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('ZLib is used for archive support, which Exponent uses for uncompressing Tar and Zip archives.'); ?>
	</td>
</tr>
<tr>
    <td id="o_curl" class="bodytext" style="font-weight: bold;" valign="top"><?php echo gt('cURL Library Support'); ?></td>
    <td class="bodytext" valign="top">
        <?php echo gt('PHP cURL support is required for several features which pull data from external sources.'); ?>
    </td>
</tr>
<tr>
	<td id="o_xml" class="bodytext" style="font-weight: bold;" valign="top"><?php echo gt('XML (Expat) Library Support'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('The web services extensions for Exponent require the Expat Library.  If you are not using web services or module that are dependent on web services, this is a safe warning to ignore.'); ?>
	</td>
</tr>
<tr>
	<td id="o_safemode" class="bodytext" style="font-weight: bold;" valign="top"><?php echo gt('Safe Mode Not Enabled'); ?></td>
	<td class="bodytext" valign="top">
        <div class="sanity_req"><?php echo gt('Exponent works best when Safe Mode is disabled'); ?></div>
        <br />
		<?php echo gt('Safe Mode is a security measure present in some Shared Hosting Environments.  It limits a PHP scripts from including or modifying files that are not owned by that script\'s owner.  This can cause serious and subtle problems that look like bugs if Exponent\'s files are not properly set up.'); ?>
        <br /><br />
        <?php echo gt('If you decide to ignore this warning, make sure that ALL files included in the Exponent package are owned by the same system user.'); ?>
	</td>
</tr>
<tr>
	<td id="o_openbasedir" class="bodytext" style="font-weight: bold;" valign="top"><?php echo gt('Open BaseDir Not Enabled'); ?></td>
	<td class="bodytext" valign="top">
		<div class="sanity_req"><?php echo gt('Exponent works best when Open BaseDir is disabled'); ?></div>
		<br />
		<?php echo gt('The open_basedir restriction is a security measure present in some Shared Hosting Environments.  It limits PHP scripts from dealing with files outside of a given directory.  This may cause some problems with some of Exponent\'s file operations, including the Multi-Site manager.  Ignore this error at your own risk.'); ?>
	</td>
</tr>
<tr>
	<td id="o_curl" class="bodytext" style="font-weight: bold;" valign="top"><?php echo gt('FileInfo Support'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('PHP FileInfo support is required for several features which need server file information.'); ?>
	</td>
</tr>
<tr>
	<td id="o_curl" class="bodytext" style="font-weight: bold;" valign="top"><?php echo gt('File Upload Support'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('The php.ini \'post_max_size\' and \'upload_max_filesize\' settings may cause file upload issues if not set to same value.'); ?>
	</td>
</tr>
<tr>
	<td id="o_upload" class="bodytext" style="font-weight: bold;" valign="top"><?php echo gt('File Uploads Enabled'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('Server administrators have the option of disabling PHP uploads.  Additionally, misconfigured servers may have problems processing uploaded files.  Without the ability to upload files, your experience with Exponent will be severely limited, since you will be unable to upload new code, patches, or images and resources.'); ?>
	</td>
</tr>
<tr>
	<td id="o_tmpfile" class="bodytext" style="font-weight: bold;" valign="top"><?php echo gt('Temporary File Creation'); ?></td>
	<td class="bodytext" valign="top">
		<?php echo gt('Various parts of Exponent have to create temporary files to accomplish a given task.  Usually, this error is related to the \'tmp/\' file and directory permission test, above.'); ?>
	</td>
</tr>
</table>
<!--</div>-->
</div>