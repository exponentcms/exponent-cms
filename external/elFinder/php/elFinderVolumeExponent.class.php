<?php
##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

/**
 * This is the class elFinderVolumeExponent
 * elFinder driver for Exponent CMS expFile filesystem.
 *
 * @author Dave Leffler
 *
 * @package Connectors
 * @subpackage Connectors
 */

class elFinderVolumeExponent extends elFinderVolumeLocalFileSystem {

    /*********************** file stat *********************/

    /**
   	 * Return fileinfo, also check if in expFiles table
   	 *
   	 * @param  string  $path  file cache
   	 * @return array
   	 * @author Dave Leffler
   	 **/
   	protected function stat($path) {
        $result = parent::stat($path);

        if ($result && $result['mime'] != 'directory') $newfile = self::_get_expFile($path);

        return $result;
    }

    /********************  file/dir manipulations *************************/

    /**
   	 * Create file and return it's path or false on failed
   	 *
   	 * @param  string  $path  parent dir path
   	 * @param string  $name  new file name
   	 * @return string|bool
   	 * @author Dave Leffler
   	 **/
   	protected function _mkfile($path, $name) {
        $result = parent::_mkfile($path, $name);

        if ($result) $newfile = self::_get_expFile($path);

   		return $result;
   	}

    /**
   	 * Copy file into another file
   	 *
   	 * @param  string  $source     source file path
   	 * @param  string  $targetDir  target directory path
   	 * @param  string  $name       new file name
   	 * @return bool
   	 * @author Dave Leffler
   	 **/
   	protected function _copy($source, $targetDir, $name) {
        $result = parent::_copy($source, $targetDir, $name);

        if ($result) {
            $target = $targetDir.DIRECTORY_SEPARATOR.$name;
            $oldfile = self::_get_expFile($source);  // hack to create old file if not existing
            $newfile = self::_get_expFile($target);
        }

   		return $result;
   	}

    /**
     * Move file into another parent dir.
     * Return new file path or false.
     *
     * @param  string $source source file path
     * @param         $targetDir
     * @param  string $name   file name
     *
     * @internal param string $target target dir path
     * @return string|bool
     * @author Dave Leffler
     */
   	protected function _move($source, $targetDir, $name) {
        $result = parent::_move($source, $targetDir, $name);

        if ($result) {
            $target = $targetDir.DIRECTORY_SEPARATOR.$name;
            $movefile = self::_get_expFile($source);
            $movefile->update(array('directory'=>dirname($target).DIRECTORY_SEPARATOR,'filename'=>basename($target)));
        }

   		return $result;
   	}

    /**
   	 * Remove file
   	 *
   	 * @param  string  $path  file path
   	 * @return bool
   	 * @author Dave Leffler
   	 **/
   	protected function _unlink($path) {
        global $user;

        $delfile = self::_get_expFile($path);
        if ($user->id==$delfile->poster || $user->isAdmin()) {
            $result = parent::_unlink($path);
            if ($result) $delfile->delete();
        } else $result = false;

   		return $result;
   	}

    /**
   	 * Create new file and write into it from file pointer.
   	 * Return new file path or false on error.
   	 *
   	 * @param  resource  $fp   file pointer
   	 * @param  string    $dir  target dir path
   	 * @param  string    $name file name
   	 * @param  array     $stat file stat (required by some virtual fs)
   	 * @return bool|string
   	 * @author Dave Leffler
   	 **/
   	protected function _save($fp, $dir, $name, $stat) {
        $path = parent::_save($fp, $dir, $name, $stat);

//   		$path = $dir.DIRECTORY_SEPARATOR.$name;
//        $newfile = new expFile(array('directory'=>dirname($path).DIRECTORY_SEPARATOR,'filename'=>basename($path)));
//        $newfile->posted = $newfile->last_accessed = filemtime($path);
//        $newfile->save();
        $newfile = self::_get_expFile($path);

   		return $path;
   	}

    /**
     * Return the expFile for the given path or create new expFile
     *
     * @param $path
     *
     * @return array|\expFile
     * @author Dave Leffler
     */
    protected static function _get_expFile($path) {
        $efile = new expFile();
        $path = str_replace(BASE, '', $path);
        $path = str_replace('\\', '/', $path);
        $thefile = $efile->find('first','directory="'.dirname($path).'/'.'" AND filename="'.basename($path).'"');
        if (empty($thefile->id)) {
            $thefile = new expFile(array('directory'=>dirname($path).'/','filename'=>basename($path)));
            $thefile->posted = $thefile->last_accessed = filemtime(BASE.$path);
            $thefile->save();
        }
        return $thefile;
    }

}