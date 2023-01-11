<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * @package Modules
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

    public function beforeDelete() {
        global $db;

        // note sub categories are removed in parent nestedNode->delete()

        // delete product storeCategory connections for sub categories
        $subcats = $this->getBranch();
        foreach ($subcats as $cat) {
            // first delete all the products assigned to this sub category
//            $products = $db->selectObjects('product_storeCategories', 'storecategories_id=' . $cat->id);
//            foreach($products as $product) {
//                $prod = new product($product->product_id);
//                $prod->delete();
//            }

            // delete product storeCategory connections
            $db->delete('product_storeCategories', 'storecategories_id=' . $cat->id);
        }
    }

    public function afterDelete() {
        global $db;

        // note we've already deleted all sub categories in the parent nestedNode->delete() method

        // first delete all the products assigned to this category
//        $products = $db->selectObjects('product_storeCategories', 'storecategories_id=' . $this->id);
//        foreach($products as $product) {
//            $prod = new product($product->product_id);
//            $prod->delete();
//        }

        // delete product storeCategory connections
        $db->delete('product_storeCategories', 'storecategories_id=' . $this->id);
    }

    /**
     * Get count of sub-categories in this category
     *
     * @return int
     */
    public function getSubCatsCount() {
        global $db;

//        $children = $db->selectNestedNodeChildren($this->table, $this->id);
        return  $db->countObjects($this->table,'parent_id=' . $this->id);
    }

    /**
     * Return all sub-categories in this category
     *
     * @return array
     */
	public function getSubCats() {
		global $db;

//		$subcats = array();
		if (empty($this->id)) {
			$children = $this->getTopLevel();
		} else {
			$children = $db->selectNestedNodeChildren($this->table, $this->id);
		}

		for ($i = 0, $iMax = count($children); $i < $iMax; $i++) {
			$sql  = 'SELECT count(DISTINCT p.id) as c FROM ' . $db->tableStmt('product') . ' p JOIN ' . $db->tableStmt('product_storeCategories') . ' sc ';
          	$sql .= 'ON p.id = sc.product_id WHERE sc.storecategories_id IN (';
          	$sql .= 'SELECT id FROM ' . $db->tableStmt('storeCategories') . ' WHERE rgt BETWEEN '.$children[$i]->lft.' AND '.$children[$i]->rgt.")";

          	$count = $db->selectObjectBySql($sql);
          	$children[$i]->product_count = $count->c;
		}

		return $children;
	}

    /**
     * Return all products assigned to this category
     *
     * @return array
     */
    public function getCatProducts() {
        global $user, $db;

        //if(!empty($order)) $order = " ORDER BY " . $order;
        $order = " ORDER BY p.title ASC";

        $sql_start = 'SELECT DISTINCT p.* FROM ' . $db->tableStmt('product') . ' p ';
        $sql = 'JOIN ' . $db->tableStmt('product_storeCategories') . ' sc ON p.id = sc.product_id ';
        $sql .= 'WHERE ';
        if (!($user->is_admin || $user->is_acting_admin))
            $sql .= '(p.active_type=0 OR p.active_type IS NULL OR p.active_type=1) AND ';
        $sql .= 'sc.storecategories_id = ' . $this->id;
        $sql  = $sql_start . $sql . $order;
        //echo $sql;
        return $db->selectObjectsBySql($sql);
    }

    /**
     * Return number of products assigned to this category
     *
     * @return int
     */
    public function getCatProductsCount() {
        global $user, $db;

        //if(!empty($order)) $order = " ORDER BY " . $order;
        $order = " ORDER BY p.title ASC";

        $sql_start = 'SELECT count(DISTINCT p.id) as c FROM ' . $db->tableStmt('product') . ' p ';
        $sql = 'JOIN ' . $db->tableStmt('product_storeCategories') . ' sc ON p.id = sc.product_id ';
        $sql .= 'WHERE ';
        if (!($user->is_admin || $user->is_acting_admin))
            $sql .= '(p.active_type=0 OR p.active_type IS NULL OR p.active_type=1) AND ';
        $sql .= 'sc.storecategories_id = ' . $this->id;
        $sql  = $sql_start . $sql . $order;
        //echo $sql;
        return $db->countObjectsBySql($sql);
    }

	/**
	 * Return an image object
	 *
	 * @param string $id specific expFile to return
	 * @return string
	 */
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

        //$sql = 'SELECT DISTINCT p.* FROM '.$db->prefix.'product p ';
        $sql = 'SELECT DISTINCT cf.expfiles_id, exp.directory, exp.filename FROM ' . $db->tableStmt('product') . ' as p ';
        $sql .= 'JOIN ' . $db->tableStmt('content_expFiles') . ' cf ON p.id = cf.content_id ';
		$sql .= 'JOIN ' . $db->tableStmt('expFiles') . ' exp ON cf.expfiles_id = exp.id ';
        $sql .= 'JOIN ' . $db->tableStmt('product_storeCategories') . ' psc ON p.id = psc.product_id ';
        $sql .= 'WHERE psc.storecategories_id IN (';
        $sql .= 'SELECT id FROM ' . $db->tableStmt('storeCategories') . ' WHERE rgt BETWEEN '.$this->lft.' AND '.$this->rgt.')';
        /*$sql  = 'SELECT cf.id FROM '.$db->prefix.'storeCategories sc JOIN '.$db->prefix.'content_expFiles as cf ';
        $sql .= 'ON cf.content_id = psc.product_id ';
        $sql .= 'JOIN '.$db->prefix.'product_storeCategories as psc ON psc.storecategories_id = sc.id ';
        $sql .= 'WHERE sc.storecategories_id=' . $this->id . " AND cf.subtype='mainimage'";*/
        $idObjs = $db->selectObjectsBySql($sql);
		foreach($idObjs as $item) {
			$file = $item->directory . $item->filename;
			if(file_exists($file)) {
				return $item->expfiles_id;
			}
		}
    }

	public function getFullTree() {
		$tree = parent::getFullTree();
		$tree_copy = array();
		foreach($tree as $key=>$node) {  // add link and image
//			$tree_copy[$key] = new stdClass();
            $tree_copy[$key] = new storeCategory();
			$tree_copy[$key]->id = $node->id;
			$tree_copy[$key]->depth = $node->depth;
            $tree_copy[$key]->is_active = $node->is_active;
            $tree_copy[$key]->sef_url = $node->sef_url;
			$tree_copy[$key]->href = makeLink(array('controller'=>'store','action'=>'showall','title'=>$node->sef_url));
			$tree_copy[$key]->parent = $node->parent_id ? $node->parent_id : '#';
			$tree_copy[$key]->parent_id = $node->parent_id;
			$tree_copy[$key]->rgt = $node->rgt;
			$tree_copy[$key]->lft = $node->lft;

            $tree_copy[$key]->count = $node->getCatProductsCount();  // number of products in category
            $tree_copy[$key]->subcount = $node->getSubCatsCount(); // determine is this is a usable category

			if (!empty($node->expFile[0]->id)) {  // add thumbnail
				$tree_copy[$key]->text = '<img class="filepic" src="' . PATH_RELATIVE . 'thumb.php?id=' . $node->expFile[0]->id . '&amp;w=18&amp;h=18&amp;zc=1" height="18" width="18">&#160;' . $node->title . ' (' . $tree_copy[$key]->count . ')';
				$tree_copy[$key]->title = $tree_copy[$key]->text;
            } else {
				$tree_copy[$key]->text = $node->title . ' (' . $tree_copy[$key]->count . ')';
				$tree_copy[$key]->title = $tree_copy[$key]->text;
            }
		}
		return $tree_copy;
	}

	/**
	 * Return existing store category id of string nested category
	 *   checks to ensure each level of nesting exists
	 *
	 * @param string $data
	 * @return string
	 */
	public static function parseCategory($data)
	{
		global $db;

		if (!empty($data)) {
			$cats1 = explode("::", trim($data));
			//eDebug($cats1);
			$cats1count = count($cats1);
			$counter = 1;
			$categories1 = array();
			foreach ($cats1 as $cat) {
				//eDebug($cat);
				$categories1[$counter] = $db->selectObject(
					'storeCategories',
					'title="' . $cat . '" AND parent_id=' . ($counter == 1 ? 0 : $categories1[$counter - 1]->id)
				);
				//eDebug($categories1);
				if (empty($categories1[$counter]->id)) {
					return "'" . $cat . "' " . gt('of the set') . ": '" . $data . "' " . gt(
						"is not a valid category"
					) . ".";
				}

				if ($counter == $cats1count) {
					return $categories1[$counter]->id;
				}
				$counter++;
			}
			//eDebug($createCats);
			//eDebug($categories1,true);
		} else {
			return gt("Category was empty.");
		}
	}

	/**
	 * Convert nested store category into a string
	 *
	 * @param integer $catID
	 * @param bool $reset
	 * @return string
	 */
	public static function buildCategoryString($catID, $reset = false)
	{
		static $cstr = '';
		if ($reset) {
			$cstr = '';
		}
		if (strlen($cstr) > 0) {
			$cstr .= "::";
		}
		$cat = new storeCategory($catID);
		//eDebug($cat);
		if (!empty($cat->parent_id)) {
			self::buildCategoryString($cat->parent_id);
		}
		$cstr .= $cat->title . "::";
		return substr($cstr, 0, -2);
	}

    /**
     * Convert string into a nested store category
     *
     * @param string $data nested category string with :: separators
     * @return integer
     */
	public static function importCategoryString($data)
	{
		global $db;

		$cats1 = explode("::", trim($data));
		$cats1count = count($cats1);
		$counter = 1;
		$categories1 = array();
		foreach ($cats1 as $cat) {
			$categories1[$counter] = $db->selectObject(
				'storeCategories',
				'title="' . $cat . '" AND parent_id=' . ($counter == 1 ? 0 : $categories1[$counter - 1]->id)
			);
			if (empty($categories1[$counter]->id)) {
				$new_sc = new storeCategory(array('parent_id'=>(!empty($categories1[$counter - 1]->id)?$categories1[$counter - 1]->id:0)));
				$params = array();
				$params['title'] = $cat;
				$params['parent_id'] = $counter == 1 ? 0 : $categories1[$counter - 1]->id;
				$params['is_active']= 1;
				$new_sc->create($params);
				$categories1[$counter] = $new_sc;
			}

			if ($counter == $cats1count) {
				return $categories1[$counter]->id;  // we've created/checked the nest of categories
			}
			$counter++;
		}
	}

}

?>