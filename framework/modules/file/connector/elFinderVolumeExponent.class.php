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
     * @param      $target
     * @param null $newowner
     *
     * @return null
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
     * @param      $target
     * @param null $newshared
     *
     * @return null
     */
    public function shared($target, $newshared = null)
    {
        $path = $this->decode($target);
        $file = self::_get_expFile($path);
        $shared = $file->shared;
        if ($newshared != null) {
            $file->update(array('shared' => $newshared));
        } else {
            return $shared;
        }
        return $newshared;
    }

    /**
     * Get/Set expFile Title
     *
     * @param      $target
     * @param null $newtitle
     *
     * @return null
     */
    public function title($target, $newtitle = null)
    {
        $path = $this->decode($target);
        $file = self::_get_expFile($path);
        $title = $file->title;
        if ($newtitle != null) {
            $file->update(array('title' => $newtitle));
        } else {
            return $title;
        }
        return $newtitle;
    }

    /**
     * Get/Set expFile Alt
     *
     * @param      $target
     * @param null $newalt
     *
     * @return null
     */
    public function alt($target, $newalt = null)
    {
        $path = $this->decode($target);
        $file = self::_get_expFile($path);
        $alt = $file->alt;
        if ($newalt != null) {
            $file->update(array('alt' => $newalt));
        } else {
            return $alt;
        }
        return $newalt;
    }

    /**
     * Return the expFile for the given path or create new expFile
     *
     * @param $path
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
        if ($result && $result['mime'] != 'directory' && substr($result['name'], 0) != '.') {
            $file = self::_get_expFile($path);
            if (!$user->isAdmin() && !$file->shared && $file->poster != $user->id) {
                $result['locked'] = true;
                $result['hidden'] = true;
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
        $result = parent::_move($source, $targetDir, $name);
        if ($result) {
            $target = $targetDir . DIRECTORY_SEPARATOR . $name;
            $movefile = self::_get_expFile($source);
            $movefile->update(
                array('directory' => dirname($target) . DIRECTORY_SEPARATOR, 'filename' => basename($target))
            );
        }
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
