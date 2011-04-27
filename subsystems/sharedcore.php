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

#
# Shared Core Library File
#
# NOTE : Shared Core extension to Exponent only works on UNIX servers
#   because Windows platforms do not support symlinks.
#
function __symlink($src_file,$dest_file) {
	if (EXPONENT_SERVER_OS == 'Windows' || EXPONENT_SERVER_OS == 'Macintosh') {
		copy($src_file,$dest_file);
	} else {
		symlink($src_file,$dest_file);
	}
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SYS_SHAREDCORE",1);

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SHAREDCORE_ERR_OK",			0);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SHAREDCORE_ERR_LINKDEST_EXISTS",	1);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SHAREDCORE_ERR_LINKDEST_NOTWRITABLE",	2);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SHAREDCORE_ERR_LINKSRC_NOTEXISTS",	3);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SHAREDCORE_ERR_LINKSRC_NOTREADABLE",	4);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SHAREDCORE_ERR_BADFILETYPE",		5);

//way to check if Apache uses symlinks?

function exponent_sharedcore_link($core,$site,$extensions = null) {
	if ($extensions == null) {
		$extensions = array(
			CORE_EXT_MODULE=>array(),
			CORE_EXT_THEME=>array(),
			CORE_EXT_SUBSYSTEM=>array(),
		);
	}
	
	$deps = exponent_core_resolveDependencies('',CORE_EXT_SYSTEM,$core->path);
	
	$prefixes = array(
		CORE_EXT_MODULE=>'m_',
		CORE_EXT_THEME=>'t_',
		CORE_EXT_SUBSYSTEM=>'s_'
	);
	
	foreach ($extensions as $type=>$extens) {
		$prefix = $prefixes[$type];
		foreach ($extens as $e) {
			$deps = array_merge($deps,exponent_core_resolveDependencies($e,$type,$core->path));
			$deps[$prefix.$e] = array(
				'name'=>$e,
				'type'=>$type,
				'comment'=>'',
			);
		}
	}
	
	krsort($deps); // t,s,m

	$linksrc = $core->path;
	$linkdest = $site->path;
	
	foreach ($deps as $info) {
		exponent_sharedcore_linkExtension($info['type'],$info['name'],$core->path,$site->path);
	}
	
	foreach (include($linksrc."manifest.php") as $file=>$linkit) {
		if ($linkit !== 0 && file_exists($linksrc.$file)) {
			symlink($linksrc.$file,$linkdest.$file);
		}
	}
	// Cleanup -- essentially, exponent.php needs to be a real file, so that __realpath(__FILE__) calls work properly
	unlink($linkdest.'exponent.php');
	copy($linksrc.'exponent.php',$linkdest.'exponent.php');
	
	return SHAREDCORE_ERR_OK;
}

function exponent_sharedcore_setup($core,$site) {
	$linksrc = $core->path;
	$linkdest = $site->path;
	if (!file_exists($linksrc)) {
		return SHAREDCORE_ERR_LINKSRC_NOTEXISTS;
	}
	if (!is_readable($linksrc)) {
		return SHAREDCORE_ERR_LINKSRC_NOTREADABLE;
	}
	if (!is_really_writable($linkdest)) {
		return SHAREDCORE_ERR_LINKDEST_NOTWRITABLE;
	}
	
	if (!defined("SYS_FILES")) include_once(BASE."subsystems/files.php");
	$exclude = array(
		"external",
		"modules",
		"subsystems",
		"themes",
		"views_c"
	);
	
	$fh = fopen($linkdest."overrides.php","w");
	fwrite($fh,"<?php\n\ndefine(\"BASE\",\"$linkdest\");\ndefine(\"PATH_RELATIVE\",\"".$site->relpath."\");\n\n?>\n");
	fclose($fh);
	
	exponent_files_copyDirectoryStructure($linksrc,$linkdest,$exclude);
	
	mkdir($linkdest."views_c",fileperms($linksrc."views_c"));
	mkdir($linkdest."modules",fileperms($linksrc."modules"));
	mkdir($linkdest."themes",fileperms($linksrc."themes"));
	mkdir($linkdest."subsystems",fileperms($linksrc."subsystems"));
	symlink($linksrc."external",$linkdest."external");
	
	return SHAREDCORE_ERR_OK;
}

/* exdoc
 * This is a specific function for linking the files and directories
 * from one core distro to a linked site.  Returns one of the SHARED_CORE_ERR_* constants
 *
 * @param string $typdir The directory name for the type of extension to link,
 *   One of either 'modules', 'subsystems' or 'themes'
 * @param string $name The name of the extension to link.
 * @param string $source The source path (to the root of Exponent)
 * @param string $destination The destination path (to the root of Exponent)
 * @param Constant $type The type of linking to perform.  One of either SHAREDCORE_LINK_NONE,
 *   SHAREDCORE_LINK_SHALLOW, SHAREDCORE_LINK_HALFDEEP or
 *   SHAREDCORE_LINK_FULLDEEP
 * @node Subsystems:SharedCore
 */
function exponent_sharedcore_linkExtension($type,$name,$source,$destination) {
	$typedir = '';
	$manifest = '';
	$auto_manidfest = '';
	switch ($type) {
		case CORE_EXT_MODULE:
			$typedir = 'modules';
			$manifest = 'modules/'.$name.'/manifest.php';
			$auto_manifest = 'modules/'.$name.'/auto.manifest.php';
			break;
		case CORE_EXT_SUBSYSTEM:
			$typedir = 'subsystems';
			$manifest = 'subsystems/'.$name.'.manifest.php';
			$auto_manifest = 'subsystems/'.$name.'.auto.manifest.php';
			break;
		case CORE_EXT_THEME:
			$typedir = 'themes';
			$manifest = 'themes/'.$name.'/manifest.php';
			$auto_manifest = 'themes/'.$name.'/auto.manifest.php';
			break;
	}
	
	if (substr($source,-1,1) == "/") $source = substr($source,0,-1);
	if (substr($destination,-1,1) == "/") $destination = substr($destination,0,-1);
	
	$linksrc = "$source/$typedir/$name";
	$linkdest = "$destination/$typedir/$name";
	
	if (is_dir($linksrc)) {
		exponent_files_copyDirectoryStructure($linksrc,$linkdest);
	}
		
	if (!defined('SYS_FILES')) include_once(BASE.'subsystems/files.php');
	if (!defined('SYS_INFO')) include_once(BASE.'subsystems/info.php');
	
	$files = ($manifest == '' ? null : array());
	if ($files !== null) {
		if (is_readable($source.'/'.$auto_manifest)) {
			$files = include($source.'/'.$auto_manifest);
		} else if (is_readable($source.'/'.$manifest)) {
			$files = include($source.'/'.$manifest);
		} else {
			$files = null;
		}
	}
	if ($files !== null) {
		exponent_sharedcore_linkFiles($source.'/',$destination.'/',$files);
	}
	return SHAREDCORE_ERR_OK;
}

function exponent_sharedcore_linkFiles($source,$destination,$files) {
	foreach ($files as $file=>$linkit) {
		if ($linkit !== 0 && is_readable($source.$file) && !file_exists($destination.$file)) {
			symlink($source.$file,$destination.$file);
		}
	}
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_sharedcore_clear($linked_root,$full_delete = false) {
	# Remove all links.  If $full_delete is false, leave the files/ and conf/
	# directories intact (because we are going to relink).  Otherwise, delete
	# absolutely everything.
	if (substr($linked_root,-1,1) != "/") $linked_root .= "/";
	
	if (!defined("SYS_FILES")) require_once(BASE."subsystems/files.php");
	
	$dh = opendir($linked_root);
	while (($file = readdir($dh)) !== false) {
		if ($file != "." && $file != "..") {
			if (is_file("$linked_root$file") || is_link("$linked_root$file")) {
				unlink("$linked_root$file");
			} else {
				if (!$full_delete && ($file == "conf" || $file == "files" || $file == 'tmp')) {
					// Do nothing
				} else {
					exponent_files_removeDirectory("$linked_root$file");
				}
			}
		}
	}
	exponent_files_removeDirectory($linked_root.'conf/extensions');
	exponent_files_removeDirectory($linked_root.'conf/data');
}

/* exdoc
 * Unlink a previously linked extension
 *
 * @param string $typdir The directory name for the type of extension to link,
 *   One of either 'modules', 'subsystems' or 'themes'
 * @param string $name The name of the extension to link.
 * @param string $dir The root of the linked site
 * @node Subsystems:SharedCore
 */
 # This may be deprecated
function exponent_sharedcore_unlinkExtension($typedir,$name,$dir) {
	if (!defined("SYS_FILES")) require_once(BASE."subsystems/files.php");
	exponent_files_removeDirectory("$dir/$typedir/$name");
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_sharedcore_listCores($dir) {
	$arr = array();
	if (!is_readable($dir)) return $arr;
	$dh = opendir($dir);
	while (($file = readdir($dh)) !== false) {
		if (is_dir("$dir/$file") && substr($file,0,1) != ".") {
			if (file_exists("$dir/$file/exponent_version.php") && !is_link("$dir/$file/exponent_version.php")) {
				$arr[] = "$dir/$file";
			}
			$arr = array_merge($arr,exponent_sharedcore_listCores("$dir/$file"));
		}
	}
	return $arr;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_sharedcore_listLinkedSites($dir,$core = null) {
	$arr = array();
	if (!is_readable($dir)) return $arr;
	$dh = opendir($dir);
	while (($file = readdir($dh)) !== false) {
		if (is_dir("$dir/$file") && substr($file,0,1) != ".") {
			if (file_exists("$dir/$file/exponent_version.php") && is_link("$dir/$file/exponent_version.php")) {
				if ($core == null || dirname(readlink("$dir/$file/exponent_version.php")) == $core) {
					$arr[] = "$dir/$file";
				}
			}
			$arr = array_merge($arr,exponent_sharedcore_listLinkedSites("$dir/$file"));
		}
	}
	return $arr;
}

?>