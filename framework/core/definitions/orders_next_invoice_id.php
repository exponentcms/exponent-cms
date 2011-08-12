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
 * @copyright  2004-2009 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @link       http://www.exponent-docs.org/
 */
 
if (!defined('EXPONENT')) exit('');

return array(
    "id"=>array(
        DB_FIELD_TYPE=>DB_DEF_ID,
        DB_PRIMARY=>true),
    "next_invoice_id"=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
);

?>
