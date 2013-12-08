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
 * elFinder volume driver for the Exponent CMS expFile filesystem.
 *
 * @author     Dave Leffler
 * @package    Connectors
 * @subpackage Connectors
 */
class elFinderVolumeExponent extends elFinderVolumeLocalFileSystem
{

    /*********************** expFile operations *********************/

    /**
     * Get expFile Owner
     *
     * @param string $target
     * @param string $newowner
     *
     * @return string
     */
    public function owner($target, $newowner = null)
    {
        $path = $this->decode($target);
        $file = self::_get_expFile($path);
        $user = user::getUserById($file->poster);
        return user::getUserAttribution($user->id);
    }

    /**
     * Get/Set expFile shared status
     *
     * @param string $target
     * @param string $newshared
     *
     * @return array/null
     */
    public function shared($target, $newshared = null)
    {
        $path = $this->decode($target);
        $file = self::_get_expFile($path);
        $newshared = mb_strtoupper(trim($newshared)) === mb_strtoupper("true") ? true : false;
        $shared = !empty($file->shared);
        if ($newshared != $shared) {
            $file->update(array('shared' => $newshared));
            $this->clearcache();
        }
        return $this->stat($path);
    }

    /**
     * Get/Set expFile Title
     *
     * @param string $target
     * @param string $newtitle
     *
     * @return array/null
     */
    public function title($target, $newtitle = null)
    {
        $path = $this->decode($target);
        $file = self::_get_expFile($path);
        $title = $file->title;
        if ($newtitle != $title) {
            $file->update(array('title' => $newtitle));
            $this->clearcache();
        }
        return $this->stat($path);
    }

    /**
     * Get/Set expFile Alt
     *
     * @param string $target
     * @param string $newalt
     *
     * @return array/null
     */
    public function alt($target, $newalt = null)
    {
        $path = $this->decode($target);
        $file = self::_get_expFile($path);
        $alt = $file->alt;
        if ($newalt != $alt) {
            $file->update(array('alt' => $newalt));
            $this->clearcache();
        }
        return $this->stat($path);
    }

    /**
     * Return the expFile for the given path or create new expFile
     *
     * @param string $path
     *
     * @return \expFile
     * @author Dave Leffler
     */
    protected static function _get_expFile($path)
    {
        $efile = new expFile();
        $path = str_replace(BASE, '', $path);
        $path = str_replace('\\', '/', $path);
        $thefile = $efile->find(
            'first',
            'directory="' . dirname($path) . '/' . '" AND filename="' . basename($path) . '"'
        );
        if (empty($thefile->id)) {
            $thefile = new expFile(array('directory' => dirname($path) . '/', 'filename' => basename($path)));
            $thefile->posted = $thefile->last_accessed = filemtime(BASE . $path);
            $thefile->save();
        }
        return $thefile;
    }

    /**
     * Move an expFile to the given path
     *
     * @param string $oldpath source file hash or path
     * @param string $newpath dest dir hash or path
     *
     * @return \expFile
     * @author Dave Leffler
     */
    protected function _move_expFile($oldpath, $newpath)
    {
        $opath = $this->decode($oldpath);
        if (empty($opath)) {
            $opath = $oldpath;
        }
        $opath = str_replace(BASE, '', $opath);
        $opath = str_replace('\\', '/', $opath);

        $npath = $this->decode($newpath);
        if (empty($npath)) {
            $npath = $newpath;
        }
        $npath = str_replace(BASE, '', $npath);
        $npath = str_replace('\\', '/', $npath);

        $efile = new expFile();
        $thefile = $efile->find(
            'first',
            'directory="' . dirname($opath) . '/' . '" AND filename="' . basename($opath) . '"'
        );
        if (empty($thefile->id)) {
            $thefile = $efile->find(
                'first',
                'directory="' . dirname($npath) . '/' . '" AND filename="' . basename($npath) . '"'
            );
        }
        if (!is_dir(BASE. $npath)) {
            $n1path = dirname($npath);
            $fname = basename($npath);
        } else {
            $n1path = $npath;
            $fname = basename($opath);
        }
        if (!empty($thefile->id)) {
            $thefile->update(
                array(
                    'directory'     => $n1path . '/',
                    'filename'      => $fname,
                    'last_accessed' => @filemtime(BASE . $n1path . $fname)
                )
            );
        }
        return $thefile;
    }

    /**
     * Delete an expFile record based on the given path
     *
     * @param string $oldpath source file hash
     *
     * @return \expFile
     * @author   Dave Leffler
     */
    protected function _remove_expFile($oldpath)
    {
        $opath = $this->decode($oldpath);
        $opath = str_replace(BASE, '', $opath);
        $opath = str_replace('\\', '/', $opath);

        $efile = new expFile();
        $thefile = $efile->find(
            'first',
            'directory="' . dirname($opath) . '/' . '" AND filename="' . basename($opath) . '"'
        );
        if (!empty($thefile->id)) {
            $thefile->delete();
        }
    }

    /**
     * Paste files
     *
     * @param  Object $volume source volume
     * @param         $src
     * @param  string $dst    destination dir hash
     * @param  bool   $rmSrc  remove source after copy?
     *
     * @return array|false
     * @author Dave Leffler
     */
    public function paste($volume, $src, $dst, $rmSrc = false)
    {
        $this->_move_expFile($src, $dst);
        $result = parent::paste($volume, $src, $dst, $rmSrc);
        $this->_remove_expFile($src); // remove the duplicate expFile record pointing to old location

        //FIXME move recursively through new folder location looking for old expFiles and updating them using _move_expFile
        $opath = $this->decode($src);
        $opath = str_replace(BASE, '', $opath);
        $opath = str_replace('\\', '/', $opath);

        $npath = $this->decode($dst);
//        $npath = str_replace(BASE, '', $npath);
        $npath = str_replace('\\', '/', $npath);

        $this->scan_folder($npath, $opath);

        return $result;
    }

    function scan_folder($npath, $opath) {
        if (is_dir($npath)) {
            $dir = opendir($npath);
            while(false !== ( $file = readdir($dir)) ) {
                if ($file != "." && $file != ".." && is_dir("$npath/$file")) {
                    $this->scan_folder("$npath/$file", "$opath");
                } elseif (substr($file, 0, 1) != '.') {
                    if (file_exists($npath . '/' . $file)) $this->_move_expFile(BASE . $opath . "/" . $file, $npath);
                }
            }
            closedir($dir);
        }
    }

    /*********************** file stat *********************/

    /**
     * Return fileinfo, also check if in expFiles table and add if missing
     *
     * @param  string $path file cache
     *
     * @return array
     * @author Dave Leffler
     **/
    protected function stat($path)
    {
        global $user;

        $result = parent::stat($path);
        // we don't include directories nor dot files in expFiles
        if ($result && !empty($result['mime']) && $result['mime'] != 'directory' && substr(
                $result['name'],
                0,
                1
            ) != '.'
        ) {
            $file = self::_get_expFile($path);
            if (!$user->isAdmin() && !$file->shared && $file->poster != $user->id) {
                $result['locked'] = true;
                $result['hidden'] = true;
            }
            $fileuser = user::getUserById($file->poster);
            $result['owner'] = user::getUserAttribution($fileuser->id);
            $result['title'] = $file->title;
            $result['alt'] = $file->alt;
            $result['shared'] = !empty($file->shared);
            if ($file->is_image) {
                $result['width'] = $file->image_width;
                $result['height'] = $file->image_height;
            }
        }
        return $result;
    }

    /********************  file/dir manipulations *************************/

    /**
     * Create file and return it's path or false on failed
     *
     * @param  string $path parent dir path
     * @param string  $name new file name
     *
     * @return string|bool
     * @author Dave Leffler
     **/
    protected function _mkfile($path, $name)
    {
        $result = parent::_mkfile($path, $name);
        if ($result) {
            self::_get_expFile($path);
        }
        return $result;
    }

    /**
     * Copy file into another file
     *
     * @param  string $source    source file path
     * @param  string $targetDir target directory path
     * @param  string $name      new file name
     *
     * @return bool
     * @author Dave Leffler
     **/
    protected function _copy($source, $targetDir, $name)
    {
        $result = parent::_copy($source, $targetDir, $name);
        if ($result) {
            $target = $targetDir . DIRECTORY_SEPARATOR . $name;
            self::_get_expFile($source); // hack to create old file if not existing
            self::_get_expFile($target);
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
     * @author   Dave Leffler
     */
    protected function _move($source, $targetDir, $name)
    {
        $target = $targetDir . DIRECTORY_SEPARATOR . $name;
//        $src = (is_file($source)) ? dirname($source) : $source;
        $this->_move_expFile($source, $target);
        $result = parent::_move($source, $targetDir, $name);
//        if ($result && !is_dir($result)) {
//            $movefile = self::_get_expFile($source);
//            if ($movefile) $movefile->update(
//                array('directory' => dirname($target) . DIRECTORY_SEPARATOR, 'filename' => basename($target))
//            );
//        }
        return $result;
    }

    /**
     * Remove file
     *
     * @param  string $path file path
     *
     * @return bool
     * @author Dave Leffler
     **/
    protected function _unlink($path)
    {
        global $user;

        $delfile = self::_get_expFile($path);
        if ($user->id == $delfile->poster || $user->isAdmin()) {
            $result = parent::_unlink($path);
            if ($result) {
                $delfile->delete();
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Create new file and write into it from file pointer.
     * Return new file path or false on error.
     *
     * @param  resource $fp   file pointer
     * @param  string   $dir  target dir path
     * @param  string   $name file name
     * @param  array    $stat file stat (required by some virtual fs)
     *
     * @return bool|string
     * @author Dave Leffler
     **/
    protected function _save($fp, $dir, $name, $stat)
    {
        $path = parent::_save($fp, $dir, $name, $stat);
        self::_get_expFile($path);
        return $path;
    }

}
