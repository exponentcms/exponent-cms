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
 * @subpackage Models
 * @package Core
 */
class storeCategory extends expNestedNode {
	public $table = 'storeCategories';
	public $attachable_table = 'content_storeCategories';     
                                                            
    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile', 
    ); 
	
	public function __construct($params=null, $get_assoc=true, $get_attached=true) {
		global $db;
		parent::__construct($params, $get_assoc, $get_attached);

		// if this is an empty category object we'll set the lft to min & rgt to the max
		// so they encompass the all of the categories, in essence creating an
		// unnamed  parent category to everything.
		if (empty($this->id)) {
			$this->lft = $db->min($this->table,'lft');
			$this->rgt = $db->max($this->table,'rgt');
		}
	}
	
	public function getEcomSubcategories() {
		global $db;
//		$subcats = array();
		if (empty($this->id)) {
			$children = $this->getTopLevel();
		} else {
			$children = $db->selectNestedNodeChildren($this->table, $this->id);
		}
		
		for($i=0; $i<count($children); $i++) {
			$sql  = 'SELECT count(DISTINCT p.id) as count FROM '.DB_TABLE_PREFIX.'_product p JOIN '.DB_TABLE_PREFIX.'_product_storeCategories sc ';
          	$sql .= 'ON p.id = sc.product_id WHERE sc.storecategories_id IN (';
          	$sql .= 'SELECT id FROM '.DB_TABLE_PREFIX.'_storeCategories WHERE rgt BETWEEN '.$children[$i]->lft.' AND '.$children[$i]->rgt.")";

            //TODO: Category count update
          	//$count = $db->selectObjectBySql($sql);  //FIXME we need a count
          	$children[$i]->product_count = 0;//$count->count;
		}

		return $children;
	}
	
	public function getCategoryImage($id = '') {
		global $db;
		
		if(!empty($id)) {
			$image = $db->selectObject("expFiles", "id ={$id}");
			$file = $image->directory . $image->filename;
			if(file_exists($file)) {
				return $id;
			} else {
				return $this->getFirstImageId();
			}
		} else {
			return $this->getFirstImageId();
			
		}
	}
   
    public function getFirstImageId() {
        global $db;
        //$sql = 'SELECT DISTINCT p.* FROM '.DB_TABLE_PREFIX.'_product p ';   
        $sql = 'SELECT DISTINCT cf.expfiles_id, exp.directory, exp.filename FROM '.DB_TABLE_PREFIX.'_product as p '; 
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_content_expFiles cf ON p.id = cf.content_id ';           
		$sql .= 'JOIN '.DB_TABLE_PREFIX.'_expFiles exp ON cf.expfiles_id = exp.id ';       
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_product_storeCategories psc ON p.id = psc.product_id ';
        $sql .= 'WHERE psc.storecategories_id IN (';
        $sql .= 'SELECT id FROM '.DB_TABLE_PREFIX.'_storeCategories WHERE rgt BETWEEN '.$this->lft.' AND '.$this->rgt.')';         
        /*$sql  = 'SELECT cf.id FROM '.DB_TABLE_PREFIX.'_storeCategories sc JOIN '.DB_TABLE_PREFIX.'_content_expFiles as cf ';
        $sql .= 'ON cf.content_id = psc.product_id ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_product_storeCategories as psc ON psc.storecategories_id = sc.id ';
        $sql .= 'WHERE sc.storecategories_id=' . $this->id . " AND cf.subtype='mainimage'";*/
        $idObjs = $db->selectObjectsBySql($sql);
		foreach($idObjs as $item) {
			$file = $item->directory . $item->filename;
			if(file_exists($file)) {
				return $item->expfiles_id;
			} 
		}
    }    
    
}

?>