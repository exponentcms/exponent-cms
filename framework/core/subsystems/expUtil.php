<?php
/**
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @package    Framework
 * @subpackage Subsystems
 * @author     Adam Kessler <adam@oicgroup.net>
 * @copyright  2004-2009 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @version    Release: @package_version@
 * @link       http://www.exponent-docs.org/api/package/PackageName
 */

class expUtil {
          
    static function right($string,$chars)
    {
        $vright = substr($string, strlen($string)-$chars,$chars);
        return $vright;
    }
    
    static function isNumberEqualTo($firstnumber,$secondnumber,$precision = 10) // are 2 floats equal
    {
        $e = pow(10,$precision);
        $i1 = intval($firstnumber * $e);
        $i2 = intval($secondnumber * $e);
        return ($i1 == $i2);
    }

    static function isNumberGreaterThan($firstnumber,$secondnumber,$precision = 10) // is one float bigger than another
    {
        $e = pow(10,$precision);
        $ibig = intval($firstnumber * $e);
        $ismall = intval($secondnumber * $e);
        return ($ibig > $ismall);
    }

    static function isNumberGreaterThanOrEqualTo($firstnumber,$secondnumber,$precision = 10) // is on float bigger or equal to another
    {
        $e = pow(10,$precision);
        $ibig = intval($firstnumber * $e);
        $ismall = intval($secondnumber * $e);
        return ($ibig >= $ismall);
    }
    
    static function isNumberLessThan($firstnumber,$secondnumber,$precision = 10) // is one float bigger than another
    {
        $e = pow(10,$precision);
        $ibig = intval($firstnumber * $e);
        $ismall = intval($secondnumber * $e);
        return ($ibig < $ismall);
    }

    static function isNumberLessThanOrEqualTo($firstnumber,$secondnumber,$precision = 10) // is on float bigger or equal to another
    {
        $e = pow(10,$precision);
        $ibig = intval($firstnumber * $e);
        $ismall = intval($secondnumber * $e);
        return ($ibig <= $ismall);
	}

	# reallyWritable is an alternate implementation of is_writable that should work on
	# a windows platform as well as Linux.
	static function reallyWritable($file) {
		// Check the operating system.  reallyWritable needs to be defined
		// specifically for Windows, but the overhead is pointless otherwise.
		if (strtolower(substr(PHP_OS,0,3)) == 'win') {
			// If we are not on a linux platform, we can assume nothing,
			// Windows, for instance, has a really screwy permissions system
			// that PHP doesn't seem to understand fully.

			// For a full understanding of how this function is
			// implemented, refer to the sdk/testing/is_writable.php
			// testing file, which tests PHP's behavior in known
			// circumstances which may vary from OS to OS.

			if (!file_exists($file)) {
				// If the file does not exist, is_writable will return... False
				return false;
			}

			if (is_file($file)) {
				// Try to open the file in write mode (binary for good measure)
				// We have to supress error output.
				$tmpfh = @fopen($file,'ab');
				if ($tmpfh == false) {
					// If the fopen call returned false, we can't write to the file
					// Just return false.  No need to close the invalid handle.
					return false;
				} else {
					// If the fopen call didn't return false, we can write to the file
					// So, close the handle (since it is valid) and return true.
					fclose($tmpfh);
					return true;
				}
			} else if (is_dir($file)) {
				// Try to create a new file in the directory.
				// Need a sufficiently uniq name.  In the future,
				// we may find it useful to loop until we find
				// a nonexistent file, but this works for now.
				$tmpnam = time().md5(uniqid('iswritable'));
				if (touch($file.'/'.$tmpnam)) {
					// If we can touch (create) the file, then we can write to the directory.
					// So, remove the temporary file and return true.
					unlink($file.'/'.$tmpnam);
					return true;
				} else {
					// If touch returns false, we can't write to the directory.
					// No file to delete, just return false.
					return false;
				}
			}
		} else {
			// If we are on a linux platform, then we don't need to do anything
			// special -- Linux has a sane permissions system that PHP
			// understands.

			// At this point, reallyWritable simply becomes a wrapper
			// for the standard is_writable call.
			// see http://php.net/is_writable
			return is_writable($file);
		}
	}

}
