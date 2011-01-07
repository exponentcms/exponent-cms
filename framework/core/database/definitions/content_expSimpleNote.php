<?php
/**
 * The DB Defination for the content_expSimpleNote Table which is a part of the SimpleNote Attachable Items
 * 
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
 * @subpackage Datatypes Definations
 * @author     Jonathan Worent <jonathan@oicgroup.net>
 * @copyright  2004-2009 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @version    Release: @package_version@
 * @link       http://www.exponent-docs.org/api/package/PackageName
 */

if (!defined('EXPONENT')) exit('');

return array(
    'expsimplenote_id'=>array(
        DB_FIELD_TYPE=>DB_DEF_ID,
        DB_PRIMARY=>true,
        DB_INCREMENT=>false),
    'content_id'=>array(
        DB_FIELD_TYPE=>DB_DEF_ID,
        DB_PRIMARY=>true,
        DB_INCREMENT=>false),
    'content_type'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>50,
        DB_PRIMARY=>true,
        DB_INCREMENT=>false),
    'subtype'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>50,
        DB_PRIMARY=>true,
        DB_INCREMENT=>false),
);

?>
