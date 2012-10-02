<?php
##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * This is the class expSorter
 *
 * @package Subsystems
 * @subpackage Subsystems
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
            $dir = 'sortDESC';
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
        if (strstr($order," ")) {
            $orderby = explode(" ",$order);
            $sortby = $orderby[0];
            $order = $orderby[1];
        }
        $ic = empty($params['ignore_case']) ? NULL : $params['ignore_case'];
        $type = empty($params['type']) ? NULL : $params['type'];
        $sorter = new expSorter(array('array'=>$params['array'],'sortby'=>$sortby,'order'=>$order, 'ignore_case'=>$ic,'type'=>$type));
        return $sorter->sort_array;
    }

    /**
     * Sort an array of objects.
     *
     * You can pass in one or more properties on which to sort.  If a
     * string is supplied as the sole property, or if you specify a
     * property without a sort order then the sorting will be ascending.
     *
     * If the key of an array is an array, then it will sorted down to that
     * level of node.
     *
     * Example usages:
     *
     * osort($items, 'size');
     * osort($items, array('size', array('time' => SORT_DESC, 'user' => SORT_ASC));
     * osort($items, array('size', array('user', 'forname'))
     *
     * @param array $array
     * @param string|array $properties
     
     * @param $array
     * @param $props
     * @internal param $properties
     */
    public static function osort(&$array, $props) {

        $properties = null;

        if (!function_exists('collapse')) {function collapse($node, $props) {
            if (is_array($props)) {
                foreach ($props as $prop) {
                    $node = (!isset($node->$prop)) ? null : $node->$prop;
                }
                return $node;
            } else {
                return (!isset($node->$props)) ? null : $node->$props;
            }
        }}
        if (!function_exists('oasort')) {function oasort($a, $b) {
            global $properties;
            foreach($properties as $k => $v) {
                if (is_int($k)) {
                    $k = $v;
                    $v = SORT_ASC;
                }
                $aProp = collapse($a, $k);
                $bProp = collapse($b, $k);
                if ($aProp != $bProp) {
                    return ($v == SORT_ASC) ? strnatcasecmp($aProp, $bProp) : strnatcasecmp($bProp, $aProp);
                }
            }
            return 0;
        }}

        global $properties;
        if (is_string($props)) {
            unset($properties);
            $properties = array($props => SORT_ASC);
        } else {
            $properties = $props;
        }
        uasort($array,'oasort');
    }
}

?>