<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Created by Adam Kessler @ 05/28/2008
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

class storeCategoryController extends expNestedNodeController {
	
	function displayname() { return "Store Category Manager"; }
	function description() { return "This module is for manageing categories in your store."; }
	function author() { return "OIC Group, Inc"; }

	protected $add_permissions = array('fix_categories'=>'to run this action.');

    // hide the configs we don't need
    public $remove_configs = array(
        'comments',
        'ealerts',
        'files',
        'rss',
        'aggregation',
        'tags'
    );

    public function edit() {
		global $db;
		$record = new storeCategoryFeeds($this->params['id']);
        $site_page_default = ecomconfig::getConfig('pagination_default');
		$product_types = ecomconfig::getConfig('product_types');
		//TODO: 
		/*
		Create a central table for the external product types to minimized table redundancy as fred mentioned it will be more than 10 more.
		
		*/
		//Declaration of array variables for product types bing and google
		$product_type = ''; //A Multi-dimentional array to be passed in the view that contains the html of listbuildercontrol for product types like bing and google
		
		$google_product_types = new google_product_types(); //Store all the google product types
		$google_types = ''; //An array being indexed by the id of product type to be passed as the source of the listbuildercontrol for google
		$google_recorded_product_types = ''; //An array being indexed by the id of product type to be passed as the default of the listbuildercontrol for google
		
		$bing_product_types = new bing_product_types(); //Store all the bing product types
		$bing_types = ''; //An array being indexed by the id of bing and has a value of the bing product type to be passed as the source of the listbuildercontrol
		$bing_recorded_product_types = ''; //An array being indexed by the id of product type to be passed as the default of the listbuildercontrol for bing
		
		$nextag_product_types = new nextag_product_types(); //Store all the nextag product types
		$nextag_types = ''; //An array being indexed by the id of nextag and has a value of the nextag product type to be passed as the source of the listbuildercontrol
		$nextag_recorded_product_types = ''; //An array being indexed by the id of product type to be passed as the default of the listbuildercontrol for nextag
		
		$shopzilla_product_types = new shopzilla_product_types(); //Store all the shopzilla product types
		$shopzilla_types = ''; //An array being indexed by the id of shopzilla and has a value of the shopzilla product type to be passed as the source of the listbuildercontrol
		$shopzilla_recorded_product_types = ''; //An array being indexed by the id of product type to be passed as the default of the listbuildercontrol for shopzilla
		
		$shopping_product_types = new shopping_product_types(); //Store all the shopping product types
		$shopping_types = ''; //An array being indexed by the id of shopping and has a value of the shopping product type to be passed as the source of the listbuildercontrol
		$shopping_recorded_product_types = ''; //An array being indexed by the id of product type to be passed as the default of the listbuildercontrol for shopping
		
		//Google product types getting the source and destination for the listbuilder control
		$google_recorded_types = $db->selectObjectsBySql("SELECT google_product_types_id, title FROM " . DB_TABLE_PREFIX . "_google_product_types_storeCategories, " . DB_TABLE_PREFIX . "_google_product_types WHERE google_product_types_id = id and storecategories_id = " . $this->params['id']);
		foreach ($db->selectFormattedNestedTree('google_product_types') as $item) {
			$google_types[$item->id] = $item->title;
		}
		foreach ($google_recorded_types as $item) {
			$google_recorded_product_types[$item->google_product_types_id] = trim($item->title);
		}
		$control = new listbuildercontrol($google_recorded_product_types, $google_types);
		$product_type['google_product_type'] = $control->controlToHTML('google_product_types_list','copy');
		// eDebug($db->selectFormattedNestedTree('bing_product_types'), true);
	
		//Bing product types getting the source and destination for the listbuilder control
		$bing_recorded_types   = $db->selectObjectsBySql("SELECT bing_product_types_id, title FROM " . DB_TABLE_PREFIX . "_bing_product_types_storeCategories, " . DB_TABLE_PREFIX . "_bing_product_types WHERE bing_product_types_id = id and storecategories_id = " . $this->params['id']);
		foreach ($db->selectFormattedNestedTree('bing_product_types') as $item) {
			$bing_types[$item->id] = $item->title;
		}
		foreach ($bing_recorded_types as $item) {
			$bing_recorded_product_types[$item->bing_product_types_id] = $item->title;
		}
		$control = new listbuildercontrol($bing_recorded_product_types, $bing_types);
		$product_type['bing_product_type'] = $control->controlToHTML('bing_product_types_list','copy');
		
		//Nextag product types getting the source and destination for the listbuilder control
		$nextag_recorded_types   = $db->selectObjectsBySql("SELECT nextag_product_types_id, title FROM " . DB_TABLE_PREFIX . "_nextag_product_types_storeCategories, " . DB_TABLE_PREFIX . "_nextag_product_types WHERE nextag_product_types_id = id and storecategories_id = " . $this->params['id']);
		foreach ($db->selectFormattedNestedTree('nextag_product_types') as $item) {
			$nextag_types[$item->id] = $item->title;
		}
		foreach ($nextag_recorded_types as $item) {
			$nextag_recorded_product_types[$item->nextag_product_types_id] = $item->title;
		}
		$control = new listbuildercontrol($nextag_recorded_product_types, $nextag_types);
		$product_type['nextag_product_type'] = $control->controlToHTML('nextag_product_types_list','copy');
		
		//Shopzilla product types getting the source and destination for the listbuilder control
		$shopzilla_recorded_types   = $db->selectObjectsBySql("SELECT shopzilla_product_types_id, title FROM " . DB_TABLE_PREFIX . "_shopzilla_product_types_storeCategories, " . DB_TABLE_PREFIX . "_shopzilla_product_types WHERE shopzilla_product_types_id = id and storecategories_id = " . $this->params['id']);
		foreach ($db->selectFormattedNestedTree('shopzilla_product_types') as $item) {
			$shopzilla_types[$item->id] = $item->title;
		}
		foreach ($shopzilla_recorded_types as $item) {
			$shopzilla_recorded_product_types[$item->shopzilla_product_types_id] = $item->title;
		}
		$control = new listbuildercontrol($shopzilla_recorded_product_types, $shopzilla_types);
		$product_type['shopzilla_product_type'] = $control->controlToHTML('shopzilla_product_types_list','copy');
		
		//Shopping.com product types getting the source and destination for the listbuilder control
		$shopping_recorded_types   = $db->selectObjectsBySql("SELECT shopping_product_types_id, title FROM " . DB_TABLE_PREFIX . "_shopping_product_types_storeCategories, " . DB_TABLE_PREFIX . "_shopping_product_types WHERE shopping_product_types_id = id and storecategories_id = " . $this->params['id']);
		foreach ($db->selectFormattedNestedTree('shopping_product_types') as $item) {
			$shopping_types[$item->id] = $item->title;
		}
		foreach ($shopping_recorded_types as $item) {
			$shopping_recorded_product_types[$item->shopping_product_types_id] = $item->title;
		}
		$control = new listbuildercontrol($shopping_recorded_product_types, $shopping_types);
		$product_type['shopping_product_type'] = $control->controlToHTML('shopping_product_types_list','copy');
		
        assign_to_template(array('product_types'=>$product_types, 'site_page_default'=>$site_page_default, 'record'=>$record, 'product_type' => $product_type));
		
        parent::edit();
    }
    
    function configure() {
        expHistory::set('editable', $this->params);

        // little bit of trickery so that that categories can have their own configs
        
        $this->loc->src = "@store-".$this->params['id'];
        $config = new expConfig($this->loc);
        $this->config = $config->config;
        $pullable_modules = listInstalledControllers($this->classname, $this->loc);
        $views = get_config_templates($this, $this->loc);
        assign_to_template(array('config'=>$this->config, 'pullable_modules'=>$pullable_modules, 'views'=>$views));
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
        $this->loc->src = "@store-".$this->params['cat-id'];
        $config = new expConfig($this->loc);
        $config->update(array('config'=>$this->params));
        flash('message', 'Configuration updated');
        expHistory::back();
    }

    function manage_ranks() {
        global $db;
        $rank = 1;
        $category = new storeCategory($this->params['id']);
        foreach($this->params['rerank'] as $key=>$id) {
            $sql = "SELECT DISTINCT sc.* FROM exponent_product_storeCategories sc JOIN exponent_product p ON p.id = sc.product_id WHERE p.id=".$id." AND sc.storecategories_id IN (SELECT id FROM exponent_storeCategories WHERE rgt BETWEEN ".$category->lft." AND ".$category->rgt.") ORDER BY rank ASC";
            $prod = $db->selectObjectBySQL($sql);
            $prod->rank = $rank;
            $db->updateObject($prod,"product_storeCategories","storecategories_id=".$prod->storecategories_id." AND product_id=".$id);
            $rank += 1;
        }
        
        expHistory::back();
    }
    
    function manage () {
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
        //         
        // $obj = json_encode($categories);  
    }
    
    public function update() {
		// eDebug($this->params['google_product_types'], true);
		// eDebug($this->params, true);
		$this->params['google_product_types'] = listbuildercontrol::parseData($this->params,'google_product_types_list');
		$this->params['bing_product_types']   = listbuildercontrol::parseData($this->params,'bing_product_types_list');
		$this->params['nextag_product_types']   = listbuildercontrol::parseData($this->params,'nextag_product_types_list');
		$this->params['shopzilla_product_types']  = listbuildercontrol::parseData($this->params,'shopzilla_product_types_list');
		$this->params['shopping_product_types']  = listbuildercontrol::parseData($this->params,'shopping_product_types_list');
		
        $curcat = new storeCategory($this->params);
        $children = $curcat->getChildren();
        foreach ($children as $key=>$child) {
            $chldcat = new storeCategory($child->id);
            $chldcat->is_active = $this->params['is_active'];
            $chldcat->save();
        }
		
		$category_type = 'google_product_types';
		$google_product_type = new $category_type();
		$google_product_type->saveCategories($this->params['google_product_types'], $curcat->id); 
		
		$category_type = 'bing_product_types';
		$bing_product_type = new $category_type();
		$bing_product_type->saveCategories($this->params['bing_product_types'], $curcat->id); 
		
		$category_type = 'nextag_product_types';
		$nextag_product_type = new $category_type();
		$nextag_product_type->saveCategories($this->params['nextag_product_types'], $curcat->id);
		
		$category_type = 'shopzilla_product_types';
		$shopzilla_product_type = new $category_type();
		$shopzilla_product_type->saveCategories($this->params['shopzilla_product_types'], $curcat->id);
		
		$category_type = 'shopping_product_types';
		$shopping_product_type = new $category_type();
		$shopping_product_type->saveCategories($this->params['shopping_product_types'], $curcat->id);
		
         parent::update();
    }
    
    function fix_categories() {
        //--Flat Structure--//
        global $db;
        $baseCat = new storeCategory();
        //$Nodes = $db->selectObjects('storeCategories');
        $Nodes = $baseCat->find('all','','lft ASC');

        //--This function converts flat structure into an array--//
        function BuildTree($TheNodes, $ID = 0, $depth=-1) {
            $Tree = array();
            if(is_array($TheNodes)) {                
                foreach($TheNodes as $Node) {
                    if($Node->parent_id == $ID) {
                        array_push($Tree, $Node);
                    }
                }
                $depth++;
                for($x = 0; $x < count($Tree); $x++) {
                    $Tree[$x]->depth = $depth;
                    $Tree[$x]->kids = BuildTree($TheNodes, $Tree[$x]->id, $depth);
                    //array_merge($test,$Tree[$x]["kids"]);
                }
                return($Tree);
            }
        }
        

        //--Call Build Tree (returns structured array)
        $TheTree = BuildTree($Nodes);
        
        //eDebug($TheTree,true);
        // flattens a tree created by parent/child relationships
        
        function recurseBuild(&$thisNode, &$thisLeft, &$thisRight)
        {
           $thisNode->lft = $thisLeft;
           if(count($thisNode->kids) > 0) 
           {
                $thisLeft = $thisNode->lft + 1;                 
                foreach ($thisNode->kids as &$myKidNode)
                {
                    $thisRight = $thisLeft + 1;   
                    recurseBuild($myKidNode,$thisLeft, $thisRight); 
                    $myKidNode->save();                  
                }    
                $thisNode->rgt = $thisLeft;
                $thisLeft = $thisRight;
           }else
           {                  
               $thisNode->rgt = $thisRight;               
               
               $thisLeft = $thisRight+1;
           }                  
           
           $thisRight = $thisLeft+1;
           $thisNode->save();
        }
        
        //if kids, set lft, but not right
        //else set both and move down
        $newLeft = 1;
        $newRight = 2;
        foreach ($TheTree as &$myNode)
        {
           recurseBuild($myNode,$newLeft, $newRight);
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
