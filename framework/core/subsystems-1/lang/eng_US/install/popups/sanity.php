<?php

return array(
	'title'=>'System Requirements Explained',
	'header'=>'The sanity checks are in place to ensure that problems with the server environment (file permissions, PHP extensions, etc) are suitable for installing Exponent.  This page explains each of the sanity checks, why it is performed, and how to reconfigure your web server if the check fails.<br /><br />Note: In all of the solutions, <span class="var">WEBUSER</span> is used for the username of the user running the web server, and <span class="var">EXPONENT</span> is used as the full path to the Exponent directory.',
	
	'filedir_tests'=>'File and Directory Permission Tests',
	
	'rw_server'=>'Must be readable and writable by web server',
	'unix_solution'=>'UNIX Solution',
	
	'config.php'=>'The conf/config.php file stores the active configuration for the site, including database connection settings and choice of theme.',
	'profiles'=>'The conf/profiles directory stores the saved configurations for the site.  Even if you do not use more than one profile, the web server must be able to create files in this directory.',
	'overrides.php'=>'The overrides.php file is used to override constants that are automagically detected by Exponent.  If the installer finds a problem with some auto-detected values, it will write the correct values out to this file before completing the installation.  After you have installed Exponent, this file only needs to be readable by the web server.',
	'install'=>'The install directory contains all of the files for the Exponent Install Wizard.  Once you have gone through the wizard once, it disables itself automatically (by removing the install/not_configured file).  To do this, it needs write permission on the install directory.  After installation, this directory isn\'t even needed, so you can remove it or set the permissions such that the web server cannot read it.',
	'modules'=>'Exponent runs a few checks against the installed modules to make sure that nothing strange is encountered.  If this test fails, please post a Support Request on the SourceForge project page for Exponent (<a href="http://www.sourceforge.net/projects/exponent/" target="_blank">http://www.sourceforge.net/projects/exponent/</a>).',
	'views_c'=>'Exponent uses Smarty to separate its data processing logic from its display logic.  Smarty templates are compiled from Smarty syntax into raw PHP for speed, and the compiled templates all go in the views_c directory, which must be writable by the web server.',
	'extensionuploads'=>'When you use the Upload Extension feature of the Administrator Control Panel, the uploaded archive is placed in extensionuploads directory temporarily.  Therefore, the web server needs full access to this.',
	'files'=>'All uploaded content files (resources, importer data, images, etc.) are stored in the site files/ directory, which the web server needs fill read and write access to.  If this test is failing and you think it shouldn\'t be, remember that you must recursively assign read and write permissions to the web server user.',
	'tmp'=>'The tmp directory is used by various parts of Exponent for temporary files.',
	
	'other_tests'=>'Other Tests',
	'db_backend'=>'Database Backend',
	'db_backend_desc'=>'Exponent stores all the content for your website in a relational database.  For portability reasons, a custom database abstraction layer is used.  Currently, this abstraction layer only supports MySQL.  If this test fails, then PHP support for these database engines was not detected.',
	'gd'=>'GD Graphics Library',
	'gd_desc'=>'Various parts of Exponent utilize the GD Graphics library for imaging functions.  Exponent can operate without GD, but you will lose such features as Captcha tests and automatic thumbnails.  A version of GD that is 2.0.x compatible will give you sharper and crisper thumbnails.',
	'php_desc'=>'Because of some of the functions that Exponent uses, versions of PHP prior to 5.2.1 are not suitable.  Most functions that are supported in later versions have workarounds, but there are a few major bugs, and functions that can\'t be re-implemented in PHP prior to 5.2.1.',
	'zlib'=>'ZLib Support',
	'zlib_desc'=>'ZLib is used for archive support, which Exponent uses for uncompressing Tar and Zip archives.',
	'xml'=>'XML (Expat Library)',
	'xml_desc'=>'The web services extensions for Exponent require the Expat Library.  If you are not using web services or module that are dependent on web services, this is a safe warning to ignore.',
	'safemode'=>'Safe Mode Not Enabled',
	'safemode_desc'=>'Safe Mode is a security measure present in some Shared Hosting Environments.  It limits a PHP scripts from including or modifying files that are not owned by that script\'s owner.  This can cause serious and subtle problems that look like bugs if Exponent\'s files are not properly set up.<br /><br />If you decide to ignore this warning, make sure that ALL files included in the Exponent package are owned by the same system user.',
	'safemode_req'=>'Exponent works best when Safe Mode is disabled',
	'basedir'=>'Open BaseDir Not Enabled',
	'basedir_req'=>'Exponent works best when Open BaseDir is disabled',
	'basedir_desc'=>'The open_basedir restriction is a security measure present in some Shared Hosting Environments.  It limits PHP scripts from dealing with files outside of a given directory.  This may cause some problems with some of Exponent\'s file operations, including the Multi-Site manager.  Ignore this error at your own risk.',
	'upload'=>'File Uploads Enabled',
	'upload_desc'=>'Server administrators have the option of disabling PHP uploads.  Additionally, misconfigured servers may have problems processing uploaded files.  Without the ability to upload files, your experience with Exponent will be severely limited, since you will be unable to upload new code, patches, or images and resources.',
	'tempfile'=>'Temporary File Creation',
	'tempfile_desc'=>'Various parts of Exponent have to create temporary files to accomplish a given task.  Usually, this error is related to the "tmp/" file and directory permission test, above.',
);

?>