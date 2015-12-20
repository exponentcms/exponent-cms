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

/**
 * This is the class elFinderVolumeExponent
 * elFinder volume driver for the Exponent CMS expFile filesystem.
 *
 * @author     Dave Leffler
 * @package    elFinder
 * @subpackage Connectors
 */
class elFinderVolumeExponent extends elFinderVolumeLocalFileSystem
{

    /**
   	 * Return debug info for client
   	 *
   	 * @return array
   	 * @author Dmitry (dio) Levashov
   	 **/
   	public function debug() {
        $debug = parent::debug();
        $debug['tmbPath'] = $this->tmbPath;
        $debug['tmbPathDir'] = is_dir($this->tmbPath);
        $debug['tmbPathRead'] = is_readable($this->tmbPath);
        $debug['tmbPathWrite'] = $this->tmbPathWritable;
   		return $debug;
   	}

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
        $path = str_replace('\\', '/', $path);
        $path = str_replace(BASE, '', $path);
        $thefile = $efile->find(
            'first',
            'directory="' . dirname($path) . '/' . '" AND filename="' . basename($path) . '"'
        );
        if (empty($thefile->id)) {
            $thefile = new expFile(array('directory' => dirname($path) . '/', 'filename' => basename($path)));
            if (file_exists(BASE . $path)) {
                $thefile->posted = $thefile->last_accessed = filemtime(BASE . $path);
            } else {
                $thefile->posted = $thefile->last_accessed = 0;
            }
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

        $opath = $this->decode($src);
        $opath = str_replace(BASE, '', $opath);
        $opath = str_replace('\\', '/', $opath);

        $npath = $this->decode($dst);
//        $npath = str_replace(BASE, '', $npath);
        $npath = str_replace('\\', '/', $npath);

        $this->scan_folder($npath, $opath);

        return $result;
    }

    /**
     * Recursive method to move expFile record directories
     *
     * @param $npath
     * @param $opath
     */
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
        if ($result && !empty($result['mime'])) {
            if ($result['mime'] != 'directory' && substr(
                    $result['name'],
                    0,
                    1
                ) != '.'
            ) {
                $file = self::_get_expFile($path);
                if (!$user->isAdmin() && $file->poster != $user->id) {
                    $result['locked'] = true;
                    if (!$file->shared) {
                        $result['hidden'] = true;
                    }
                }
                $fileuser = user::getUserById($file->poster);
                if (!empty($fileuser->id)) {
                    $result['owner'] = user::getUserAttribution($fileuser->id);
                } else {
                    $result['owner'] = gt('Unknown');
                }
                $result['id'] = $file->id;
                $result['title'] = $file->title;
                $result['alt'] = $file->alt;
                $result['shared'] = !empty($file->shared);
                if ($file->is_image) {
                    $result['width'] = $file->image_width;
                    $result['height'] = $file->image_height;
                }
                if ((strpos($path, substr(UPLOAD_DIRECTORY, 0, -1) . DIRECTORY_SEPARATOR . 'avatars') !== false || strpos($path, substr(UPLOAD_DIRECTORY, 0, -1) . DIRECTORY_SEPARATOR . 'uploads') !== false) && !$user->isSuperAdmin()) {
                    $result['write'] = false;
                    $result['locked'] = true;
                }
            } elseif($result['mime'] == 'directory') {
                if ((strtolower($result['name']) == 'avatars' || strtolower($result['name']) == 'uploads')) {
                     // only admins can see the avatars and uploads subfolders and their contents
                    $result['locked'] = true;
                    if (!$user->isSuperAdmin()) {
                        $result['write'] = false;
                    }
                    if (!$user->isAdmin()) {
                        $result['hidden'] = true;
                    }
                } else {
                    if (!$user->isAdmin()) {
//                        $result['write'] = false;
                        $result['locked'] = true;
                    }
                }
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

    /********************  archivers *************************/

    /**
   	 * Detect available archivers  NOTE: only inserted to fix 7za command line switches
   	 *
   	 * @return void
   	 **/
   	protected function _checkArchivers() {
   		$this->archivers = $this->getArchivers();
   		return;
   	}

    /**
   	 * Get server side available archivers  NOTE: only inserted to fix 7za command line switches
   	 *
   	 * @param bool $use_cache
   	 * @return array
   	 */
   	protected function getArchivers($use_cache = true) {

   		$arcs = array(
   				'create'  => array(),
   				'extract' => array()
   		);

   		if (!function_exists('proc_open')) {
   			return $arcs;
   		}

   		if ($use_cache && isset($_SESSION['ELFINDER_ARCHIVERS_CACHE']) && is_array($_SESSION['ELFINDER_ARCHIVERS_CACHE'])) {
   			return $_SESSION['ELFINDER_ARCHIVERS_CACHE'];
   		}

   		$this->procExec('tar --version', $o, $ctar);

   		if ($ctar == 0) {
   			$arcs['create']['application/x-tar']  = array('cmd' => 'tar', 'argc' => '-cf', 'ext' => 'tar');
   			$arcs['extract']['application/x-tar'] = array('cmd' => 'tar', 'argc' => '-xf', 'ext' => 'tar');
   			unset($o);
   			$test = $this->procExec('gzip --version', $o, $c);

   			if ($c == 0) {
   				$arcs['create']['application/x-gzip']  = array('cmd' => 'tar', 'argc' => '-czf', 'ext' => 'tgz');
   				$arcs['extract']['application/x-gzip'] = array('cmd' => 'tar', 'argc' => '-xzf', 'ext' => 'tgz');
   			}
   			unset($o);
   			$test = $this->procExec('bzip2 --version', $o, $c);
   			if ($c == 0) {
   				$arcs['create']['application/x-bzip2']  = array('cmd' => 'tar', 'argc' => '-cjf', 'ext' => 'tbz');
   				$arcs['extract']['application/x-bzip2'] = array('cmd' => 'tar', 'argc' => '-xjf', 'ext' => 'tbz');
   			}
   		}
   		unset($o);
   		$this->procExec('zip -v', $o, $c);
   		if ($c == 0) {
   			$arcs['create']['application/zip']  = array('cmd' => 'zip', 'argc' => '-r9', 'ext' => 'zip');
   		}
   		unset($o);
   		$this->procExec('unzip --help', $o, $c);
   		if ($c == 0) {
   			$arcs['extract']['application/zip'] = array('cmd' => 'unzip', 'argc' => '',  'ext' => 'zip');
   		}
   		unset($o);
   		$this->procExec('rar --version', $o, $c);
   		if ($c == 0 || $c == 7) {
   			$arcs['create']['application/x-rar']  = array('cmd' => 'rar', 'argc' => 'a -inul', 'ext' => 'rar');
   			$arcs['extract']['application/x-rar'] = array('cmd' => 'rar', 'argc' => 'x -y',    'ext' => 'rar');
   		} else {
   			unset($o);
   			$test = $this->procExec('unrar', $o, $c);
   			if ($c==0 || $c == 7) {
   				$arcs['extract']['application/x-rar'] = array('cmd' => 'unrar', 'argc' => 'x -y', 'ext' => 'rar');
   			}
   		}
   		unset($o);
   		$this->procExec('7za --help', $o, $c);
   		if ($c == 0) {
   			$arcs['create']['application/x-7z-compressed']  = array('cmd' => '7za', 'argc' => 'a', 'ext' => '7z');
   			$arcs['extract']['application/x-7z-compressed'] = array('cmd' => '7za', 'argc' => 'e -y', 'ext' => '7z');

   			if (empty($arcs['create']['application/x-gzip'])) {
   				$arcs['create']['application/x-gzip'] = array('cmd' => '7za', 'argc' => 'a -tgzip', 'ext' => 'tar.gz');
   			}
   			if (empty($arcs['extract']['application/x-gzip'])) {
   				$arcs['extract']['application/x-gzip'] = array('cmd' => '7za', 'argc' => 'e -tgzip -y', 'ext' => 'tar.gz');
   			}
   			if (empty($arcs['create']['application/x-bzip2'])) {
   				$arcs['create']['application/x-bzip2'] = array('cmd' => '7za', 'argc' => 'a -tbzip2', 'ext' => 'tar.bz');
   			}
   			if (empty($arcs['extract']['application/x-bzip2'])) {
   				$arcs['extract']['application/x-bzip2'] = array('cmd' => '7za', 'argc' => 'a -tbzip2 -y', 'ext' => 'tar.bz');
   			}
   			if (empty($arcs['create']['application/zip'])) {
   				$arcs['create']['application/zip'] = array('cmd' => '7za', 'argc' => 'a -tzip', 'ext' => 'zip');
   			}
   			if (empty($arcs['extract']['application/zip'])) {
   				$arcs['extract']['application/zip'] = array('cmd' => '7za', 'argc' => 'e -tzip -y', 'ext' => 'zip');
   			}
   			if (empty($arcs['create']['application/x-tar'])) {
   				$arcs['create']['application/x-tar'] = array('cmd' => '7za', 'argc' => 'a -ttar', 'ext' => 'tar');
   			}
   			if (empty($arcs['extract']['application/x-tar'])) {
   				$arcs['extract']['application/x-tar'] = array('cmd' => '7za', 'argc' => 'e -ttar -y', 'ext' => 'tar');
   			}
   		} else if (substr(PHP_OS,0,3) === 'WIN') {
   			// check `7z` for Windows server.
   			unset($o);
   			$this->procExec('7z', $o, $c);
   			if ($c == 0) {
   				$arcs['create']['application/x-7z-compressed']  = array('cmd' => '7z', 'argc' => 'a -mx0', 'ext' => '7z');
   				$arcs['extract']['application/x-7z-compressed'] = array('cmd' => '7z', 'argc' => 'x -y', 'ext' => '7z');

   				if (empty($arcs['create']['application/x-gzip'])) {
   					$arcs['create']['application/x-gzip'] = array('cmd' => '7z', 'argc' => 'a -tgzip -mx0', 'ext' => 'tar.gz');
   				}
   				if (empty($arcs['extract']['application/x-gzip'])) {
   					$arcs['extract']['application/x-gzip'] = array('cmd' => '7z', 'argc' => 'x -tgzip -y', 'ext' => 'tar.gz');
   				}
   				if (empty($arcs['create']['application/x-bzip2'])) {
   					$arcs['create']['application/x-bzip2'] = array('cmd' => '7z', 'argc' => 'a -tbzip2 -mx0', 'ext' => 'tar.bz');
   				}
   				if (empty($arcs['extract']['application/x-bzip2'])) {
   					$arcs['extract']['application/x-bzip2'] = array('cmd' => '7z', 'argc' => 'x -tbzip2 -y', 'ext' => 'tar.bz');
   				}
   				if (empty($arcs['create']['application/zip'])) {
   					$arcs['create']['application/zip'] = array('cmd' => '7z', 'argc' => 'a -tzip -mx0', 'ext' => 'zip');
   				}
   				if (empty($arcs['extract']['application/zip'])) {
   					$arcs['extract']['application/zip'] = array('cmd' => '7z', 'argc' => 'x -tzip -y', 'ext' => 'zip');
   				}
   				if (empty($arcs['create']['application/x-tar'])) {
   					$arcs['create']['application/x-tar'] = array('cmd' => '7z', 'argc' => 'a -ttar -mx0', 'ext' => 'tar');
   				}
   				if (empty($arcs['extract']['application/x-tar'])) {
   					$arcs['extract']['application/x-tar'] = array('cmd' => '7z', 'argc' => 'x -ttar -y', 'ext' => 'tar');
   				}
   				if (empty($arcs['create']['application/x-rar'])) {
   					$arcs['create']['application/x-rar']  = array('cmd' => '7z', 'argc' => 'a -trar -mx0', 'ext' => 'rar');
   				}
   				if (empty($arcs['extract']['application/x-rar'])) {
   					$arcs['extract']['application/x-rar'] = array('cmd' => '7z', 'argc' => 'x -trar -y', 'ext' => 'rar');
   				}
   			}
   		}

   		$_SESSION['ELFINDER_ARCHIVERS_CACHE'] = $arcs;
   		return $arcs;
   	}

}
