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

}
