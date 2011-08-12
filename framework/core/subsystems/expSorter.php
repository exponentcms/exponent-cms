<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expSorter class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expSorter
 *
 * @subpackage Core-Subsytems
 * @package Framework
 */
 
class expSorter {
    public $sort_array = array();
    public $sort_object_property = null;
    public $sort_order = 'ASC';
    public $ignore_case = false;
	public $sort_type = '';  // ''=usort, 'a'=uasort, 'k'=uksort

    
    public function __construct($params) {
        if (isset($params['array'])) $this->sort_array = $params['array']; 
        if (isset($params['sortby'])) $this->sort_object_property = $params['sortby'];
        if (isset($params['order'])) $this->sort_order = $params['order'];
        if (isset($params['ignore_case'])) $this->ignore_case = $params['ignore_case'];   
        if (isset($params['type'])) $this->sort_type = $params['type'];

        if ($this->sort_order == 'ASC') {
            $dir = 'sortASC';
        } else {
            $dir ='sortDESC';
        }
        if ($this->sort_type == 'a') {
            uasort($this->sort_array, array($this,$dir));
        } elseif ($this->sort_type == 'k') {
            uksort($this->sort_array, array($this,$dir));
        } else {
	        usort($this->sort_array, array($this,$dir));
        }

    }
    
    public function sortASC($a,$b) {
        $col = $this->sort_object_property;
        if (is_string($a->$col) && $this->ignore_case) {
            $aval = strtolower($a->$col);
        } else {
            $aval = $a->$col;
        }
        
        if (is_string($b->$col) && $this->ignore_case) {
            $bval = strtolower($b->$col);
        } else {
            $bval = $b->$col;
        }
        return ($aval < $bval ? -1 : 1);
    }
    
    public function sortDESC($a,$b) {
        $col = $this->sort_object_property;
        
        if (is_string($a->$col) && $this->ignore_case) {
            $aval = strtolower($a->$col);
        } else {
            $aval = $a->$col;
        }
        
        if (is_string($b->$col) && $this->ignore_case) {
            $bval = strtolower($b->$col);
        } else {
            $bval = $b->$col;
        }
        
        return ($aval > $bval ? -1 : 1);
    }

	/**
	 * Main static function to sort arrays based on parameters
	 *
	 * @param $params
	 * @return array
	 */
	public static function sort($params) {
        if (empty($params['array'])) return array();
        $sortby = empty($params['sortby']) ? NULL : $params['sortby']; 
        $order = empty($params['order']) ? NULL : $params['order'];
        $ic = empty($params['ignore_case']) ? NULL : $params['ignore_case'];
        $type = empty($params['type']) ? NULL : $params['type'];
        $sorter = new expSorter(array('array'=>$params['array'],'sortby'=>$sortby,'order'=>$order, 'ignore_case'=>$ic,'type'=>$type));
        return $sorter->sort_array;
    }
}

?>
