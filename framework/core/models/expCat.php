<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expTag class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Jonathan Worent <jonathan@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expCategory
 *
 * @subpackage Core-Datatypes
 * @package Framework
 */

class expCat extends expRecord {
	public $table = 'expCats';
	public $attachable_table = 'content_expCats';
    protected $attachable_item_types = array(
        //'content_expFiles'=>'expFile', 
        //'content_expTags'=>'expTag', 
        //'content_expComments'=>'expComment',
        //'content_expSimpleNote'=>'expSimpleNote',
    );

}
?>
