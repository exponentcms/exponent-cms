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
 * @subpackage Controllers
 * @package    Modules
 */

class storeCategoryController extends expNestedNodeController {
    static function displayname() {
        return gt("Store Category Manager");
    }

    static function description() {
        return gt("This module is for managing categories in your store.");
    }

    protected $add_permissions = array(
        'fix_categories' => 'to run this action.'
    );

    // hide the configs we don't need
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'module',
        'pagination',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','module_title','pagination','rss','tags','twitter',)

    public function edit() {
        global $db;

        $id = empty($this->params['id']) ? null : $this->params['id'];
        $record = new storeCategoryFeeds($id);
        $site_page_default = ecomconfig::getConfig('pagination_default');
        $product_types = ecomconfig::getConfig('product_types');

        //Declaration of array variables for product types bing and google
        $arr_product_type = ''; //A Multi-dimentional array to be passed in the view that contains the html of listbuildercontrol for product types like bing and google

        if (!empty($product_types)) foreach ($product_types as $value) {

            $product_type = $value . 's';
            $product_type_id = $value . 's_id';
            $product_type_list = $value . 's_list';
            $new_product_type = new $product_type;
            $f_recorded_product_types = '';
            $f_types = '';
            //Import product type records if it is empty
            if ($db->tableIsEmpty($product_type)) {
                $file = BASE . "framework/modules/ecommerce/assets/sql/exponent_{$product_type}.sql";
                if (is_readable($file)) {
                    $templine = '';
                    // Read in entire file
                    $lines = file($file);
                    // Loop through each line
                    foreach ($lines as $line) {
                        // Only continue if it's not a comment
                        if (substr($line, 0, 2) != '--' && $line != '') {
                            // Add this line to the current segment
                            $templine .= $line;
                            // If it has a semicolon at the end, it's the end of the query
                            if (substr(trim($line), -1, 1) == ';') {
                                //Query the sql statement making sure that it will not be escape since we are dummping data
                                $db->sql($templine, false);
                                // Reset temp variable to empty
                                $templine = '';
                            }
                        }
                    }
                }
            }

            $recorded_product_type = $db->selectObjectsBySql("SELECT {$product_type_id}, title FROM " . DB_TABLE_PREFIX . "_{$value}s_storeCategories, " . DB_TABLE_PREFIX . "_{$product_type} WHERE {$product_type_id} = id and storecategories_id = " . $this->params['id']);

            foreach ($db->selectFormattedNestedTree("{$product_type}") as $item) {
                $f_types[$item->id] = $item->title;
            }

            foreach ($recorded_product_type as $item) {
                $f_recorded_product_types[$item->$product_type_id] = trim($item->title);
            }
            $control = new listbuildercontrol(@$f_recorded_product_types, $f_types);
            $arr_product_type[$value] = $control->controlToHTML($product_type_list, "copy");
        }

        assign_to_template(array(
            'product_types'     => $product_types,
            'site_page_default' => $site_page_default,
            'record'            => $record,
            'product_type'      => $arr_product_type
        ));

        parent::edit();
    }

    function configure() {
        expHistory::set('editable', $this->params);

        $cat = new storeCategoryFeeds($this->params['id']);
        // little bit of trickery so that that categories can have their own configs
        $this->loc->src = "@store-" . $this->params['id'];
        $config = new expConfig($this->loc);
        $this->config = $config->config;
        $pullable_modules = expModules::listInstalledControllers($this->baseclassname, $this->loc);
        $views = get_config_templates($this, $this->loc);
        assign_to_template(array(
            'config'           => $this->config,
            'pullable_modules' => $pullable_modules,
            'views'            => $views,
//            'title'=>$this->displayname()
            'title'            => gt('Store Category named') . ' ' . $cat->title
        ));
    }

    function saveconfig() {

        // unset some unneeded params
        unset($this->params['module']);
        unset($this->params['controller']);
        unset($this->params['src']);
        unset($this->params['int']);
        unset($this->params['id']);
        unset($this->params['action']);
        unset($this->params['PHPSESSID']);

        // setup and save the config
        $this->loc->src = "@store-" . $this->params['cat-id'];
        $config = new expConfig($this->loc);
        $config->update(array('config' => $this->params));
        flash('message', gt('Configuration updated'));
        expHistory::back();
    }

    function manage_ranks() {
        global $db;

        $rank = 1;
        $category = new storeCategory($this->params['id']);
        foreach ($this->params['rerank'] as $id) {
            $sql = "SELECT DISTINCT sc.* FROM " . DB_TABLE_PREFIX . "_product_storeCategories sc JOIN " . DB_TABLE_PREFIX . "_product p ON p.id = sc.product_id WHERE p.id=" . $id . " AND sc.storecategories_id IN (SELECT id FROM " . DB_TABLE_PREFIX . "_storeCategories WHERE rgt BETWEEN " . $category->lft . " AND " . $category->rgt . ") ORDER BY rank ASC";
            $prod = $db->selectObjectBySQL($sql);
            $prod->rank = $rank;
            $db->updateObject($prod, "product_storeCategories", "storecategories_id=" . $prod->storecategories_id . " AND product_id=" . $id);
            $rank += 1;
        }

        expHistory::back();
    }

    function manage() {
        //         $category = new storeCategory();
        //         $categories = $category->getFullTree();
        //         
        //         // foreach($categories as $i=>$val){
        //         //  if (!empty($this->values) && in_array($val->id,$this->values)) {
        //         //      $this->tags[$i]->value = true;
        //         //  } else {
        //         //      $this->tags[$i]->value = false;
        //         //  }
        //         //  $this->tags[$i]->draggable = $this->draggable; 
        //         //  $this->tags[$i]->checkable = $this->checkable; 
        //         // }
        //
        // $obj = json_encode($categories);  
    }

    public function update() {
        $product_types = ecomconfig::getConfig('product_types');

        foreach ($product_types as $value) {
            $this->params["{$value}s"] = listbuildercontrol::parseData($this->params, "{$value}s_list");
        }

        $curcat = new storeCategory($this->params);
        $children = $curcat->getChildren();
        foreach ($children as $child) {
            $chldcat = new storeCategory($child->id);
            $chldcat->is_active = $this->params['is_active'];
            $chldcat->save();
        }

        foreach ($product_types as $value) {
            $type = $value . 's';
            $product_type = new $type();
            $product_type->saveCategories($this->params["{$type}"], $curcat->id, $type);
        }

        parent::update();
    }

    function fix_categories() {
        //--Flat Structure--//
//        global $db;

        $baseCat = new storeCategory();
        //$Nodes = $db->selectObjects('storeCategories');
        $Nodes = $baseCat->find('all', '', 'lft ASC');

        //--This function converts flat structure into an array--//
        function BuildTree($TheNodes, $ID = 0, $depth = -1) {
            $Tree = array();
            if (is_array($TheNodes)) {
                foreach ($TheNodes as $Node) {
                    if ($Node->parent_id == $ID) {
                        array_push($Tree, $Node);
                    }
                }
                $depth++;
                for ($x = 0; $x < count($Tree); $x++) {
                    $Tree[$x]->depth = $depth;
                    $Tree[$x]->kids = BuildTree($TheNodes, $Tree[$x]->id, $depth);
                    //array_merge($test,$Tree[$x]["kids"]);
                }
                return ($Tree);
            }
        }

        //--Call Build Tree (returns structured array)
        $TheTree = BuildTree($Nodes);

        //eDebug($TheTree,true);
        // flattens a tree created by parent/child relationships

        function recurseBuild(&$thisNode, &$thisLeft, &$thisRight) {
            $thisNode->lft = $thisLeft;
            if (count($thisNode->kids) > 0) {
                $thisLeft = $thisNode->lft + 1;
                foreach ($thisNode->kids as &$myKidNode) {
                    $thisRight = $thisLeft + 1;
                    recurseBuild($myKidNode, $thisLeft, $thisRight);
                    $myKidNode->save();
                }
                $thisNode->rgt = $thisLeft;
                $thisLeft = $thisRight;
            } else {
                $thisNode->rgt = $thisRight;

                $thisLeft = $thisRight + 1;
            }

            $thisRight = $thisLeft + 1;
            $thisNode->save();
        }

        //if kids, set lft, but not right
        //else set both and move down
        $newLeft = 1;
        $newRight = 2;
        foreach ($TheTree as &$myNode) {
            recurseBuild($myNode, $newLeft, $newRight);
        }
        //eDebug($TheTree,true);

        echo "Done";

        /*function flattenArray(array $array){
            $ret_array = array();
            $counter=0;
            foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $key=>$value) {
                if ($key=='id') {
                    $counter++;
                }
                $ret_array[$counter][$key] = $value;
            }
            return $ret_array;
        }*/

        // takes a flat array with propper parent/child relationships in propper order
        // and adds the lft and rgt extents correctly for a nested set

        /*function nestify($categories) {
            // Trees mapped            
            $trees = array();
            $trackParents = array();
            $depth=0;
            $counter=1;
            $prevDepth=0;

            foreach ($categories as $key=>$val) {
                if ($counter==1) {
                    # first in loop. We should only hit this once: first.
                    $categories[$key]['lft'] = $counter;
                    $counter++;
                } else if ($val['depth']>$prevDepth) {
                    # we have a child of the previous node
                    $trackParents[] = $key-1;
                    $categories[$key]['lft'] = $counter;
                    $counter++;
                } else if ($val['depth']==$prevDepth) {
                    # we have a sibling of the previous node
                    $categories[$key-1]['rgt'] = $counter;
                    $counter++;
                    $categories[$key]['lft'] = $counter;
                    $counter++;
                } else {
                    # we have moved up in depth, but how far up?
                    $categories[$key-1]['rgt'] = $counter;
                    $counter++;
                    $l=count($trackParents);
                    while($l > 0 && $trackParents[$l - 1]['depth'] >= $val['depth']) {
                        $categories[$trackParents[$l - 1]]['rgt'] = $counter;
                        array_pop($trackParents);
                        $counter++;
                        $l--;
                    }
                    
                    $categories[$key]['lft'] = $counter;
                    //???$counter++;
                }        
                $prevDepth=$val['depth'];
            }

            $categories[$key]['rgt'] = $counter;
            return $categories;
        } */

        // takes a flat nested set formatted array and creates a multi-dimensional array from it

        /*function toHierarchy($collection)
        {
                // Trees mapped
                $trees = array();
                $l = 0;

                if (count($collection) > 0) {
                        // Node Stack. Used to help building the hierarchy
                        $stack = array();

                        foreach ($collection as $node) {
                                $item = $node;
                                $item['children'] = array();

                                // Number of stack items
                                $l = count($stack);

                                // Check if we're dealing with different levels
                                while($l > 0 && $stack[$l - 1]['depth'] >= $item['depth']) {
                                        array_pop($stack);
                                        $l--;
                                }

                                // Stack is empty (we are inspecting the root)
                                if ($l == 0) {
                                        // Assigning the root node
                                        $i = count($trees);
                                        $trees[$i] = $item;
                                        $stack[] = & $trees[$i];
                                } else {
                                        // Add node to parent
                                        $i = count($stack[$l - 1]['children']);
                                        $stack[$l - 1]['children'][$i] = $item;
                                        $stack[] = & $stack[$l - 1]['children'][$i];
                                }
                        }
                }

                return $trees;
        }*/

        // this will test our data manipulation
        // eDebug(toHierarchy(nestify(flattenArray($TheTree))),1);

        /*$flat_fixed_cats = nestify(flattenArray($TheTree));
                
        foreach ($flat_fixed_cats as $k=>$v) {
            $cat = new storeCategory($v['id']);
            $cat->lft = $v['lft'];
            $cat->rgt = $v['rgt'];
            $cat->save();
            eDebug($cat);
        }
          */
        //-Show Array Structure--//
        // print_r($TheTree);
        // 
        // 
        // //--Print the Categories, and send their children to DrawBranch--//
        // //--The code below allows you to keep track of what category you're currently drawing--//
        // 
        // printf("<ul>");
        // 
        // foreach($TheTree as $MyNode) {
        //     printf("<li>{$MyNode['Name']}</li>");
        //     if(is_array($MyNode["Children"]) && !empty($MyNode["Children"])) {
        //         DrawBranch($MyNode["Children"]);
        //     }
        // }
        // printf("</ul>");
        // //--Recursive printer, should draw a child, and any of its children--//
        // 
        // function DrawBranch($Node){
        //     printf("<ul>");
        // 
        //     foreach($Node as $Entity) {
        //         printf("<li>{$Entity['Name']}</li>");
        // 
        //         if(is_array($Entity["Children"]) && !empty($Entity["Children"])) {
        //             DrawBranch($Entity["Children"]);
        //         }
        // 
        //         printf("</ul>");
        //     }
        // }
    }
}

?>