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
 * This is the class expUtil
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expUtil {

    /**
     * function to return the right side of a string
     *
     * @param $string
     * @param $chars
     *
     * @return string
     */
    static function right($string,$chars)
    {
        $vright = substr($string, strlen($string)-$chars,$chars);
        return $vright;
    }

    /**
     * function to compare float numbers for equality
     *
     * @param     $firstnumber
     * @param     $secondnumber
     * @param int $precision
     *
     * @return bool
     */
    static function isNumberEqualTo($firstnumber,$secondnumber,$precision = 10) // are 2 floats equal
    {
        $e = pow(10,$precision);
        $i1 = intval($firstnumber * $e);
        $i2 = intval($secondnumber * $e);
        $i1 = (float)(string)$firstnumber;
        $i2 = (float)(string)$secondnumber;
        return ($i1 == $i2);
    }

    /**
     * function to compare float numbers for greater than
     *
     * @param     $firstnumber
     * @param     $secondnumber
     * @param int $precision
     *
     * @return bool
     */
    static function isNumberGreaterThan($firstnumber,$secondnumber,$precision = 10) // is one float bigger than another
    {
        $e = pow(10,$precision);
        $ibig = intval($firstnumber * $e);
        $ismall = intval($secondnumber * $e);
        $ibig = (float)(string)$firstnumber;
        $ismall = (float)(string)$secondnumber;
        return ($ibig > $ismall);
    }

    /**
     * function to compare float numbers for greater than or equality
     *
     * @param     $firstnumber
     * @param     $secondnumber
     * @param int $precision
     *
     * @return bool
     */
    static function isNumberGreaterThanOrEqualTo($firstnumber,$secondnumber,$precision = 10) // is on float bigger or equal to another
    {
        $e = pow(10,$precision);
        $ibig = intval($firstnumber * $e);
        $ismall = intval($secondnumber * $e);
        $ibig = (float)(string)$firstnumber;
        $ismall = (float)(string)$secondnumber;
        return ($ibig >= $ismall);
    }

    /**
     * function to compare float numbers for less than
     *
     * @param     $firstnumber
     * @param     $secondnumber
     * @param int $precision
     *
     * @return bool
     */
    static function isNumberLessThan($firstnumber,$secondnumber,$precision = 10) // is one float bigger than another
    {
        $e = pow(10,$precision);
        $ibig = intval($firstnumber * $e);
        $ismall = intval($secondnumber * $e);
        $ibig = (float)(string)$firstnumber;
        $ismall = (float)(string)$secondnumber;
        return ($ibig < $ismall);
    }

    /**
     * function to compare float numbers for less than or equality
     *
     * @param     $firstnumber
     * @param     $secondnumber
     * @param int $precision
     *
     * @return bool
     */
    static function isNumberLessThanOrEqualTo($firstnumber,$secondnumber,$precision = 10) // is on float bigger or equal to another
    {
        $e = pow(10,$precision);
        $ibig = intval($firstnumber * $e);
        $ismall = intval($secondnumber * $e);
        $ibig = (float)(string)$firstnumber;
        $ismall = (float)(string)$secondnumber;
        return ($ibig <= $ismall);
	}

	/**
	 * isReallyWritable is an alternate implementation of is_writable that should work on
	 * a windows platform as well as Linux.
     *
	 * @param $file
     *
	 * @return bool
	 */
	static function isReallyWritable($file) {
		// Check the operating system.  isReallyWritable needs to be defined
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

			// At this point, isReallyWritable simply becomes a wrapper
			// for the standard is_writable call.
			// see http://php.net/is_writable
			return is_writable($file);
		}
        return false;
	}

    /**
     * returns original referrer url from ticket
     *
     * @param null $ticket
     *
     * @return null
     */
    public static function getOrigReferrer($ticket=null) {
		global $db;

		// if they didn't pass in a specific ticket
		if (empty($ticket)) {
			// see if there is a ticket in the session to use
			if (isset($_SESSION[SYS_SESSION_KEY]['ticket'])) {
				$ticket = $_SESSION[SYS_SESSION_KEY]['ticket'];
			} else {
				return null;
			}
		}

		return $db->selectValue('sessionticket', 'referrer', "ticket='".$ticket."'" );
	}

}

?>