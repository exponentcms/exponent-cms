<?php
##################################################
#
# Copyright (c) 2004-2022 OIC Group, Inc.
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
 * This is the class expNestedNode
 *
 * @subpackage Models
 * @package Core
 */
abstract class expNestedNode extends expRecord {

   /**
    * Array of Tag Node Objects, siblings or children, depending
    * how the class is called.
    *
    * @name $nodes
    * @var array $nodes   Array of Tag Node Objects
    *
    * @access public
    * @PHPUnit Not Defined
    *
    */
	public $nodes = array();
//    protected $attachable_item_types = array(
//        //'content_expFiles'=>'expFile',
//        //'content_expTags'=>'expTag',
//        //'content_expComments'=>'expComment',
//        //'content_expSimpleNote'=>'expSimpleNote',
//    );

	public function __construct($params=null, $get_assoc = false, $get_attached = false) {
		parent::__construct($params, $get_assoc, $get_attached);

		// if this object is empty we'll treat it like a top level'
		if (empty($this->id)) {
			global $db;
            if ($this->havedb == true) {
                $this->lft = $db->min($this->table, 'lft');
                $this->rgt = $db->max($this->table, 'rgt');
            }
		}
		$this->items_per_page = 0;  //note fix for strict mode, UNUSED
	}

	public function create($params) {
		global $db;

		$this->checkForAttachableItems($params);
		$this->build($params);
		$parent_id = isset($this->parent_id) ? $this->parent_id : 0;
		if ($parent_id == 0) {
			$rgt = $db->max($this->table, 'rgt');
			$this->lft = $rgt + 1;
			$this->rgt = $rgt + 2;
		} else {
			// get the parent and figure out if it has children already.
			$parent = $db->selectObject($this->table, 'id='.$parent_id);
			if(empty($parent)) return null;
			$children = $db->selectNestedBranch($parent->id);

			// if this node has no children then we adjust based off the parents lft val
			// otherwise we setup the lft & rgt and adjust from the parents rgt field.
			$adjust_val = empty($children) ? $parent->lft : $parent->rgt;

			// set the lft & rgt for this node and adjust the rest accordingly.
			$this->lft = $adjust_val + 1;
			$this->rgt = $adjust_val + 2;

			$db->sql('UPDATE ' . $db->tableStmt($this->table) . ' SET rgt = rgt + 2 WHERE rgt > '.$adjust_val);
			$db->sql('UPDATE ' . $db->tableStmt($this->table) . ' SET lft = lft + 2 WHERE lft > '.$adjust_val);
		}
		$this->save(true);
	}

	public function moveBefore($target) {
		// if we are getting an id we need a new object..otherwise we'll assume an object was passed
		if (is_numeric($target)) $target = new $this->classname($target);
        $this->parent_id = $target->parent_id;
        $this->save();
		$this->move($target->lft);
	}

	public function moveAfter($target) {
		// if we are getting an id we need a new object..otherwise we'll assume an object was passed
		if (is_numeric($target)) $target = new $this->classname($target);
        $this->parent_id = $target->parent_id;
        $this->save();
		$this->move($target->rgt + 1);
	}

	public function moveInto($target) {
		// if we are getting an id we need a new object..otherwise we'll assume an object was passed
		if (is_numeric($target)) $target = new $this->classname($target);
        $this->parent_id = $target->id;
        $this->save();
		$this->move($target->lft + 1);
	}

	public function move($insertpoint,$type="after") {
		global $db;

		$width = ($this->rgt - $this->lft) + 1;
        $orginal_lft = $this->lft;
        $orginal_rgt = $this->rgt;

		//push nodes over to make room new node(s)
		$db->adjustNestedTreeFrom($this->table, $insertpoint, $width);

        //insert the new node(s) by adjusting their lft & rgt values
        $this->refresh(); //if we're moving this node down we need to do this.
        $differential = $insertpoint - $this->lft;
        //if ($insertpoint < $this->lft) $differential = $differential-2;
        $orginal_lft = $this->lft;
        $orginal_rgt = $this->rgt;
        $db->adjustNestedTreeBetween($this->table, $this->lft, $this->rgt, $differential);

        //simulate a delete by shifting down from the hole created by the moved node(s)
        $db->adjustNestedTreeFrom($this->table, $orginal_rgt + 1, ($width * -1));

        //refresh the moved node to get it's new values from the DB before updating the parent_id
        $this->refresh();
        // $parent = $db->selectNestedNodeParent($this->table, $this->id);
        // $this->parent_id = $parent->id;
        $this->save();
	}

    // public function move($insertpoint) {
    //  global $db;
    //  $width = ($this->rgt - $this->lft) + 1;
    //  $differential = $insertpoint - $this->lft;
    //  if ($insertpoint < $this->lft) $differential = $differential-2;
    //  $orginal_lft = $this->lft;
    //  $orginal_rgt = $this->rgt;
    //
    //  //push nodes over to make room new node(s)
    //  $db->adjustNestedTreeFrom($this->table, $insertpoint, $width);
    //
    //  //insert the new node(s) by adjusting their lft & rgt values
    //  $this->refresh(); //if we're moving this node down we need to do this.
    //  $db->adjustNestedTreeBetween($this->table, $this->lft, $this->rgt, $differential);
    //
    //  //simulate a delete by shifting down from the hole created by the moved node(s)
    //  $db->adjustNestedTreeFrom($this->table, $orginal_rgt + 1, ($width * -1));
    //
    //  //refresh the moved node to get it's new values from the DB before updating the parent_id
    //  $this->refresh();
    //  $parent = $db->selectNestedNodeParent($this->table, $this->id);
    //  $this->parent_id = $parent->id;
    //  $this->save();
    // }

	public function delete($where = '') {
		global $db;

        $this->beforeDelete();

		// note this removes the categories only, no associated tables, handle that in beforeDelete()
		$db->deleteNestedNode($this->table, $this->lft, $this->rgt);

        $this->afterDelete();
	}

	public function pathToNode() {
		global $db;

		return $db->selectPathToNestedNode($this->table, $this->id);
	}

    /**
     * Returns an array of individual Top Level Tag Objects.
     *
     * @param string $name  sef_url
     * @param bool $get_assoc
     * @param bool $get_attached
     *
     * @return array $children   array of Top Level Tag Objects, empty if no objects
     * @category nested_nodes
     *
     * @access public
     *
     * @global object $db
     */
    public function getTopLevel($name = "", $get_assoc=false, $get_attached=false) {
		global $db;

        $where = 'parent_id=0';
        if ($name != "")
            $where.=" AND sef_url='" . $name . "'";
        $where .= ' ORDER BY lft ASC';

      /* if ($name != "" )
       {
            echo "Name: " . $name . "Where: " . $where . "<br>";
            eDebug($db->selectExpObjects($this->tablename, $where, $this->classname, false, false),true);
       }*/

        return $db->selectExpObjects($this->tablename, $where, $this->classname, $get_assoc, $get_attached);
	}

	public function getParent() {
		global $db;

		return $db->selectNestedNodeParent($this->table, $this->id);
	}

    /**
     * Returns an array of individual Child Tag Objects that belong to
     * the current "parent" tag as defined in the current class ID property.
     *
     * Some tags may have 'child' tags, this method will access whether a "parent"
     * Tag (as defined in the ID property of the current class instantiation)
     * indeed has children. If so, it will wrap each child in its own Tag Object
     * and wrap them all within a array.
     * <p>
     * If the current "parent" tag does not have children, then an empty array
     * is created and returned.
     * <p>
     * This will *not* return "grandchildren", it is a single level query.
     *
     * @param string $childName  sef_url
     * @param bool $get_assoc
     * @param bool $get_attached
     *
     * @return array $children   array of Child Tag Objects, empty if no children
     * @category nested_nodes
     *
     * @access public
     *
     * @global object $db
     */
	public function getChildren($childName = "", $get_assoc=false, $get_attached=false) {
		global $db;

        /**
        * Create an empty array to hold any "child" tags, or to just send
        * back to indicate this Tag does not have children.
        *
        * @name $_children
        * @var array $children holds "child" Tag Objects
        *
        * @access private
        * @PHPUnit Not Defined
        *
        */
        if (!empty($this->id))
            $where = 'parent_id='.$this->id;
        else
            $where = '1';
        if ($childName != "")
            $where.=" AND sef_url='" . $childName . "'";
        $where .= " ORDER BY rgt ASC";
        return $db->selectExpObjects($this->tablename, $where, $this->classname, $get_assoc, $get_attached);
	}

	public function getBranch($get_assoc = false, $get_attached = false) {
		global $db;

       /**
        * Create an empty array to hold any "child" tags, or to just send
        * back to indicate this Tag does not have children.
        *
        * @name $_children
        * @var array $children holds "child" Tag Objects
        *
        * @access private
        * @PHPUnit Not Defined
        *
        */
		$children = array();

		// Pass the table name for Tags and the current Tag ID to a DB
        // call to retrieve any children this Tag may have
		foreach($db->selectNestedBranch($this->table, $this->id) as $child) {
			$children[] = new $this->classname($child->id, $get_assoc, $get_attached);
		}

		return $children;
	}

	public function getFullTree() {
		global $db;

		$tree = array();
		foreach($db->selectNestedTree($this->table) as $node) {
            $obj = new $this->classname($node->id, false, true);
            $obj->depth = $node->depth;
			$tree[] = $obj;
		}
		return $tree;
	}

    public static function getTree($model_table) {
        global $db;

        return $db->selectNestedTree($model_table);
    }

    /**
     * Modification of "Build a tree from a flat array in PHP"
     *
     * Authors: @DSkinner, @ImmortalFirefly and @SteveEdson
     *
     * @link https://stackoverflow.com/a/28429487/2078474
     */
    public static function buildTree( array &$elements, $parentId = 0 ) {
        $branch = array();
        foreach ( $elements as &$element ) {
            if ( $element->parent_id == $parentId ) {
                $children = self::buildTree( $elements, $element->id );
                if ( $children )
                    $element->children = $children;

                $branch[$element->id] = $element;
                unset( $element );
            }
        }
        return $branch;
    }

}

?>