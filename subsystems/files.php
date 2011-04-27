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

/* exdoc
 * The definition of this constant lets other parts
 * of the system know that the Files Subsystem
 * has been included for use.
 * @node Subsystems:Files
 */
define("SYS_FILES",1);

/* exdoc
 * Filesystem Error Response: Success
 * @node Subsystems:Files
 */
define("SYS_FILES_SUCCESS",		0);

/* exdoc
 * Filesystem Error Response: Found File at Destination
 * @node Subsystems:Files
 */
define("SYS_FILES_FOUNDFILE",	1);

/* exdoc
 * Filesystem Error Response: Found Directory at Destination
 * @node Subsystems:Files
 */
define("SYS_FILES_FOUNDDIR",	2);

/* exdoc
 * Filesystem Error Response: Destination not writable
 * @node Subsystems:Files
 */
define("SYS_FILES_NOTWRITABLE",	3);

/* exdoc
 * Filesystem Error Response: Destination not readable
 * @node Subsystems:Files
 */
define("SYS_FILES_NOTREADABLE",	4);

/* exdoc
 * Filesystem Error Response: Destination not deletable
 * @node Subsystems:Files
 */
define("SYS_FILES_NOTDELETABLE",	5);

/* exdoc
 * This method creates a directory and all of its parent directories, if they do not exist,
 * emulating the behavior of the -p option to mkdir on UNIX systems.  Returns
 * a SYS_FILES_* constant, indicating its status.
 *
 * @param string $dir The directory to create.  This path must be relative to BASE
 * @node Subsystems:Files
 */
function exponent_files_makeDirectory($dir,$mode=null,$is_full=false) {
	$__oldumask = umask(0);
	$parentdir = ($is_full ? "/" : BASE); // we will add to parentdir with each directory
	foreach (explode("/",$dir) as $part) {
		if ($part != "" && !is_dir($parentdir.$part)) {
			// No parent directory.  Create it.
			if (is_file($parentdir.$part)) return SYS_FILES_FOUNDFILE;
			if (is_really_writable($parentdir)) {
				if ($mode == null) $mode = DIR_DEFAULT_MODE+0;
				mkdir($parentdir.$part,$mode);
				chmod($parentdir.$part,$mode);
			} else return SYS_FILES_NOTWRITABLE;
		}
		$parentdir .= $part."/";
	}
	umask($__oldumask);
	return SYS_FILES_SUCCESS;
}

/* exdoc
 * Recursively removes the given directory, and all
 * of the files and directories underneath it.
 *
 * @param string $dir The path of the directory to remove
 * @node Subsystems:Files
 */
function exponent_files_removeDirectory($dir) {
	if (strpos($dir,BASE) != 0) $dir = BASE.$dir;
	$dh = opendir($dir);
	if ($dh) {
		while (($file = readdir($dh)) !== false) {
			if ($file != "." && $file != ".." && is_dir("$dir/$file")) {
				if (exponent_files_removeDirectory("$dir/$file") == SYS_FILES_NOTDELETABLE) return SYS_FILES_NOTDELETABLE;
			} else if (is_file("$dir/$file") || is_link(is_file("$dir/$file"))) {
				unlink("$dir/$file");
				if (file_exists("$dir/$file")) {
					return SYS_FILES_NOTDELETABLE;
				}
			}
			else if ($file != "." && $file != "..") {
				echo "BAD STUFF HAPPENED<br />";
				echo "--------Don't know what to do with $dir/$file<br />";
				echo "<xmp>";
				print_r(stat("$dir/$file"));
				echo filetype("$dir/$file");
				echo "</xmp>";
			}
		}
	}
	rmdir($dir);
}

function exponent_files_fixName($name) {
	return preg_replace('/[^A-Za-z0-9\.]/','_',$name);
}

/* exdoc
 * Checks to see if the upload destination file exists.  This is to prevent
 * accidentally uploading over the top of another file.
 * Returns true if the file already exists, and false if it does not.
 *
 * @param string $dir The directory to contain the existing directory.
 * @param string $name The name of the file control used to upload the
 *  file.  The files subsystem will look to the $_FILES array
 *  to get the filename of the uploaded file.
 * @node Subsystems:Files
 */
function exponent_files_uploadDestinationFileExists($dir,$name) {
	return (file_exists(BASE.$dir."/".exponent_files_fixName($_FILES[$name]['name'])));
}

/* exdoc
* Move an uploaded temporary file to a more permanent home inside of the Exponent files/ directory.
* This function takes into account the default file modes specified in the site configuration.
 * @param string $tmp_name The temporary path of the uploaded file.
 * @param string $dest The full path to the destination file (including the destination filename).
 * @node Subsystems:Files
 */
function exponent_files_moveUploadedFile($tmp_name,$dest) {
	move_uploaded_file($tmp_name,$dest);
	if (file_exists($dest)) {
		$__oldumask = umask(0);
		chmod($dest,FILE_DEFAULT_MODE);
		umask($__oldumask);
	}
}

/* exdoc
 * Lists files and directories under a given parent directory, returning a
 * revursive array. The key is the file or directory name.  In the case of files,
 * the value if the file name.  In the case of directories, the value if an array
 * of the files / directories in that directory.
 *
 * @param string $dir The path of the directory to look at.
 * @param boolean $recurse A boolean dictating whether to descend into subdirectories
 * 	recursviely, and list files and subdirectories.
 * @param string $ext An optional file extension.  If specified, only files ending with
 * 	that file extension will show up in the list.  Directories are not affected.
 * @param array $exclude_dirs An array of directory names to exclude.  These names are
 * 	path-independent.  Specifying "dir" will ignore all directories and
 * 	sub-directories named "dir", regardless of their parent.
 * @node Subsystems:Files
 */
function exponent_files_list($dir, $recurse = false, $ext=null, $exclude_dirs = array()) {
	$files = array();
	if (is_readable($dir)) {
		$dh = opendir($dir);
		while (($file = readdir($dh)) !== false) {
			if (is_dir("$dir/$file") && !in_array($file,$exclude_dirs) && $recurse && $file != "." && $file != ".." && $file != "CVS") {
				$files[$file] = exponent_files_list("$dir/$file",$recurse,$ext,$exclude_dirs);
			}
			if (is_file("$dir/$file") && ($ext == null || substr($file,-1*strlen($ext),strlen($ext)) == $ext)) {
				$files[$file] = $file;
			}
		}
	}
	return $files;
}

/* exdoc
 * Lists files and directories under a given parent directory. Returns an
 * associative, flat array of files and directories.  The key is the full file
 * or directory name, and the value is the file or directory name.
 *
 * @param string $dir The path of the directory to look at.
 * @param boolean $recurse A boolean dictating whether to descend into subdirectories
 * 	recursviely, and list files and subdirectories.
 * @param string $ext An optional file extension.  If specified, only files ending with
 * 	that file extension will show up in the list.  Directories are not affected.
 * @param array $exclude_dirs An array of directory names to exclude.  These names are
 * 	path-independent.  Specifying "dir" will ignore all directories and
 * 	sub-directories named "dir", regardless of their parent.
 * @node Subsystems:Files
 */
function exponent_files_listFlat($dir, $recurse = false, $ext=null, $exclude_dirs = array(), $relative = "") {
	$files = array();
	if (is_readable($dir)) {
		$dh = opendir($dir);
		while (($file = readdir($dh)) !== false) {
			if (is_dir("$dir/$file") && !in_array($file,$exclude_dirs) && $recurse && $file != "." && $file != ".." && $file != "CVS") {
				$files = array_merge($files,exponent_files_listFlat("$dir/$file",$recurse,$ext,$exclude_dirs,$relative));
			}
			if (is_file("$dir/$file") && ($ext == null || substr($file,-1*strlen($ext),strlen($ext)) == $ext)) {
				$files[str_replace($relative,"","$dir/$file")] = $file;
			}
		}
	}
	return $files;
}

/* exdoc
 * Copies just the directory structure (including subdirectories) of a given directory.
 * Any files in the source directory are ignore, and duplicate copies are made (no symlinks).
 *
 * @param string $src The directory to copy structure from.  This must be a full path.
 * @param string $dest The directory to create duplicate structure in.  If this directory is not empty,
 * 	you may run into some problems, because of file/directory conflicts.
 * @param $exclude_dirs An array of directory names to exclude.  These names are
 * 	path-independent.  Specifying "dir" will ignore all directories and
 * 	sub-directories named "dir", regardless of their parent.
 * @node Subsystems:Files
 */
function exponent_files_copyDirectoryStructure($src,$dest,$exclude_dirs = array()) {
	$__oldumask = umask(0);
	if (!file_exists($dest)) mkdir($dest,fileperms($src));
	$dh = opendir($src);
	while (($file = readdir($dh)) !== false) {
		if (is_dir("$src/$file") && !in_array($file,$exclude_dirs) && substr($file,0,1) != "." && $file != "CVS") {
			if (!file_exists("$dest/$file")) mkdir("$dest/$file",fileperms("$src/$file"));
			if (is_dir("$dest/$file")) {
				exponent_files_copyDirectoryStructure("$src/$file","$dest/$file");
			}
		}
	}
	umask($__oldumask);
}

/* exdoc
 * Looks at the filesystem strucutre surrounding the destination
 * and determines if the web server can create a new file there.
 * Returns one of the following:
 *	<br>SYS_FILES_NOTWRITABLE - unable to create files in destination
 *	<br>SYS_FILES_SUCCESS - A file or directory can be created in destination
 *	<br>SYS_FILES_FOUNDFILE - Found destination to be a file, not a directory
 *
 * @param string $dest Path to the directory to check
 * @node Subsystems:Files
 */
function exponent_files_canCreate($dest) {
	if (substr($dest,0,1) == '/') $dest = str_replace(BASE,'',$dest);
	$parts = explode('/',$dest);
	$working = BASE;
	for ($i = 0; $i < count($parts); $i++) {
		if ($parts[$i] != '') {
			if (!file_exists($working.$parts[$i])) {
				return (is_really_writable($working) ? SYS_FILES_SUCCESS : SYS_FILES_NOTWRITABLE);
			}
			$working .= $parts[$i].'/';
		}
	}
	// If we got this far, then the file we are asking about already exists.
	// Check to see if we can overrwrite this file.
	// First however, we need to strip off the '/' that was added a few lines up as the last part of the for loop.
	$working = substr($working,0,-1);
	
	if (!is_really_writable($working)) {
		return SYS_FILES_NOTWRITABLE;
	} else {
		if (is_file($working)) {
			return SYS_FILES_FOUNDFILE;
		} else {
			return SYS_FILES_FOUNDDIR;
		}
	}
}

function exponent_files_remove_files_in_directory($dir) {
        $files['removed'] = array();
        $files['not_removed'] = array();

        if (is_readable($dir)) {
                $dh = opendir($dir);
                while (($file = readdir($dh)) !== false) {
                        $filepath = $dir.'/'.$file;
                        if (substr($file,0,1) != '.') {
                                if (is_writeable($filepath) && !is_dir($filepath)) {
                                        unlink($filepath);
                                        $files['removed'][] = $filepath;
                                } else {
                                        $files['not_removed'][] = $filepath;
                                }
                        }
                }
        }

        return $files;
}

function exponent_files_bytesToHumanReadable($size) {
	if ($size >= 1024*1024*1024) { // Gigs
		$size_msg = round(($size / (1024*1024*1024)),2) . " GB";
	} else if ($size >= 1024*1024) { // Megs
		$size_msg = round(($size / (1024*1024)),2) . " MB";
	} else if ($size >= 1024) { // Kilo
		$size_msg = round(($size / 1024),2) . " kB";
	} else {
		$size_msg = $size . " bytes";
	}
	
	return $size_msg;
}

?>
