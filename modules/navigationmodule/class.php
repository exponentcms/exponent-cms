<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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

class navigationmodule {
	function name() { return exponent_lang_loadKey('modules/navigationmodule/class.php','module_name'); }
	function author() { return 'OIC Group, Inc'; }
	function description() { return exponent_lang_loadKey('modules/navigationmodule/class.php','module_description'); }
	
	function hasContent() { return false; }
	function hasSources() { return false; }
	function hasViews()   { return true; }
	
	function supportsWorkflow() { return false; }
	
	function permissions($internal = '') {
		$i18n = exponent_lang_loadFile('modules/navigationmodule/class.php');
		
		return array(
			'manage'=>gt('Administrate'),
			'view'=>gt('View Page'),
		);
	}
	
	function show($view,$loc = null,$title = '') {
		global $db;
		//$id = exponent_sessions_get('last_section');
		global $sectionObj;
		$id = $sectionObj->id;
		$current = null;
		
		switch( $view )
		{
  			case "Breadcrumb":
				//Show not only the location of a page in the hierarchy but also the location of a standalone page
				global $sections;
				$current = $db->selectObject('section',' id= '.$id);
			
				if( $current->parent == -1 )
				{
					$sections = navigationmodule::levelTemplate(-1,0);
					foreach ($sections as $section) {
						if ($section->id == $id) {
							$current = $section;
							break;
						}
					}
				} else {
					$sections = navigationmodule::levelTemplate(0,0);
					foreach ($sections as $section) {
						if ($section->id == $id) {
							$current = $section;
							break;
						}
					}
				}
			break;
			default:
				//$sections = navigationmodule::levelTemplate(0,0);
				global $sections;
				if ($sectionObj->parent == -1) {
					$current = $sectionObj;
				} else {
					foreach ($sections as $section) {
						if ($section->id == $id) {
							$current = $section;
							break;
						}
					}
				}

			break;
		}

		$template = new template('navigationmodule',$view,$loc);
		$template->assign('sections',$sections);
		$template->assign('current',$current);
		$template->assign('num_sections', count($sections));
		global $user;
		$template->assign('canManage',((isset($user->is_acting_admin) && $user->is_acting_admin == 1) ? 1 : 0));
		$template->assign('moduletitle',$title);
		$template->output();
	}
	
	function deleteIn($loc) {
		// Do nothing, no content
	}
	
	function copyContent($fromloc,$toloc) {
		// Do nothing, no content
	}
	
	function getChildren(&$i) {
		global $sections;

		//echo "i=".$i."<br>";
		if ($sections[$i]->depth == $sections[$i+1]->depth) {
			return array();
		} else {
			$ret_depth = $sections[$i]->depth;
			$i++;
			$ret_array = array();
			for($i; $i<count($sections); $i++) {
				// start setting up the objects to return
				$obj = null;
				$obj->text = $sections[$i]->name;
				
				if ($sections[$i]->active == 1) { 
					$obj->url = $sections[$i]->link;
				} else { 
					$obj->url = "#";
					$obj->onclick = "onclick: { fn: return false }";
				}
				
				//echo "i=".$i."<br>";
				if (navigationmodule::hasChildren($i)) {
					$obj->submenu = null;
					$obj->submenu->id = $sections[$i]->name.$sections[$i]->id;
					//echo "getting children of ".$sections[$i]->name;
					$obj->submenu->itemdata = getChildren($i);
					$ret_array[] = $obj;
				} else {
					$ret_array[] = $obj;	
				}

				if (($i+1) >= count($sections) || $sections[$i+1]->depth <= $ret_depth) {
					return $ret_array;		
				}
			}
		}
	}

	function hasChildren($i) {
		global $sections;
		if ( ($i+1) >= count($sections)) return false;
		return ($sections[$i]->depth < $sections[$i+1]->depth) ? true : false;
	}

	function navtojson(){
		global $sections;
		$json_array = array();

		for($i=0; $i<count($sections); $i++) {
			if ($sections[$i]->depth == 0) {
				$obj = null;
				$obj->id = $sections[$i]->name.$sections[$i]->id;
				
				/*if ($sections[$i]->active == 1) { 
					$obj->disabled = false;
				} else { 
					$obj->disabled = true;
				}*/
				
				//$obj->disabled = true;
				$obj->itemdata = navigationmodule::getChildren($i);
			} 
			$json_array[] = $obj;
		}
		
		return json_encode($json_array);
	}
	
	function spiderContent($item = null) {
		global $db;
		//global $sections;
		global $router;
		
        if (!defined('SYS_SEARCH')) include_once(BASE.'subsystems/search.php');		

	 	$db->delete('search',"ref_module='navigationmodule' AND ref_type='section'");
        
        // this now ensures we get internal pages, instead of relying on the global $sections, which does not.
        $sections = $db->selectObjects('section','1');

        foreach ($sections as $section) {
            $search = null;
            $search->category = 'Webpages';
            $search->ref_module = 'navigationmodule';
            $search->ref_type = 'section';
    	    $search->original_id = $section->id;
        	$search->title = $section->name;
            //$search->view_link = $router->buildUrlByPageId($section->id);
            $link = str_replace(URL_FULL,'', makeLink(array('section'=>$section->id)));
            $search->view_link = $link;
			$search->body = $section->description;
			$search->keywords = $section->keywords;
			
			// now we're going to grab all the textmodules on this page and build the body for the page based off the content
			// of all the text module added together.
			$modnames = array('text', 'textController');
			foreach ($modnames as $mod) {
			    $loc->mod = getControllerName($mod);
			    $controllername = getControllerClassName($mod);    			
			    foreach($db->selectObjects('sectionref', "module='".$controllername."' AND section=".$section->id) as $module) {
				    $loc->src = $module->source;
				    $loc->int = '';				    
				    $controller = new $controllername();
				    $textitems = $db->selectObjects($controller->model_table, "location_data='".serialize($loc)."'");
				    foreach ($textitems as $textitem) {
				        if (!empty($textitem)) {
					        $search->body .= ' ' . exponent_search_removeHTML($textitem->body) . ' ';
					        $search->keywords .= " ".$textitem->title;
				        }
				    }
			    }
            }
            
            $db->insertObject($search,'search');
        }

		return true;
	}

	function searchName() {
        return "Page names and meta tags";
    }
	
	/*
	 * Retrieve either the entire hierarchy, or a subset of the hierarchy, as an array suitable for use
	 * in a dropdowncontrol.  This is used primarily by the section datatype for moving and adding
	 * sections to specific parts of the site hierarchy.
	 *
	 * @param $parent The id of the subtree parent.  If passed as 0 (the default), the entire subtree is parsed.
	 * @param $ignore_ids a value-array of IDs to be ignored when generating the list.  This is used
	 * when moving a section, since a section cannot be made a subsection of itself or any of its subsections.
	 */
	function levelShowDropdown($parent,$depth=0,$default=0,$ignore_ids = array()) {
		$html = '';
		global $db;
		$nodes = $db->selectObjects('section','parent='.$parent);
		if (!defined('SYS_SORTING')) include_once(BASE.'subsystems/sorting.php');
		usort($nodes,'exponent_sorting_byRankAscending');
		foreach ($nodes as $node) {
			if (($node->public == 1 || exponent_permissions_check('view',exponent_core_makeLocation('navigationmodule','',$node->id))) && !in_array($node->id,$ignore_ids)) {
				$html .= '<option value="' . $node->id . '" ';
				if ($default == $node->id) $html .= 'selected';
				$html .= '>';
				if ($node->active == 1) {
					$html .= str_pad('',$depth*3,'.',STR_PAD_LEFT) . $node->name;
				} else {
					$html .= str_pad('',$depth*3,'.',STR_PAD_LEFT) . '('.$node->name.')';
				}
				$html .= '</option>';
				$html .= navigationmodule::levelShowDropdown($node->id,$depth+1,$default,$ignore_ids);
			}
		}
		return $html;
	}
	
	/*
	 * Returns a flat representation of the full site hierarchy.
	 */
	function levelDropDownControlArray($parent,$depth = 0,$ignore_ids = array(),$full=false) {
		$i18n = exponent_lang_loadFile('modules/navigationmodule/class.php');

		$ar = array();
		if ($parent == 0 && $full) {
			$ar[0] = '&lt;'.$i18n['top_of_hierarchy'].'&gt;';
		}
		global $db;
		$nodes = $db->selectObjects('section','parent='.$parent);
		if (!defined('SYS_SORTING')) include_once(BASE.'subsystems/sorting.php');
		usort($nodes,'exponent_sorting_byRankAscending');
		foreach ($nodes as $node) {
			if (($node->public == 1 || exponent_permissions_check('view',exponent_core_makeLocation('navigationmodule','',$node->id))) && !in_array($node->id,$ignore_ids)) {
				if ($node->active == 1) {
					$text = str_pad('',($depth+($full?1:0))*3,'.',STR_PAD_LEFT) . $node->name;
				} else {
					$text = str_pad('',($depth+($full?1:0))*3,'.',STR_PAD_LEFT) . '('.$node->name.')';
				}
				$ar[$node->id] = $text;
				foreach (navigationmodule::levelDropdownControlArray($node->id,$depth+1,$ignore_ids,$full) as $id=>$text) {
					$ar[$id] = $text;
				}
			}
		}
		
		return $ar;
	}
	
	function levelTemplate($parent, $depth = 0, $parents = array()) {
		if ($parent != 0) $parents[] = $parent;
		global $db, $user;
		$nodes = array();
		$cache = exponent_sessions_getCacheValue('navigationmodule');
		if (!isset($cache['kids'][$parent])) {
			$kids = $db->selectObjects('section','parent='.$parent);
			$cache['kids'][$parent] = $kids;			
			exponent_sessions_setCacheValue('navigationmodule', $cache);
		} else {
			$kids = $cache['kids'][$parent];
		}		
			
        	if (!defined('SYS_SORTING')) include_once(BASE.'subsystems/sorting.php');
		usort($kids,'exponent_sorting_byRankAscending');
		for ($i = 0; $i < count($kids); $i++) {
			$child = $kids[$i];
			//foreach ($kids as $child) {
			if ($child->public == 1 || exponent_permissions_check('view',exponent_core_makeLocation('navigationmodule','',$child->id))) {
				$child->numParents = count($parents);
				$child->depth = $depth;
				$child->first = ($i == 0 ? 1 : 0);
				$child->last = ($i == count($kids)-1 ? 1 : 0);
				$child->parents = $parents;
				$child->canManage = (isset($user->is_acting_admin) && $user->is_acting_admin == 1 ? 1 : 0);
				$child->canManageRank = $child->canManage;
				if (!isset($child->sef_name)) {$child->sef_name = '';}
				
				// Generate the link attribute base on alias type.
				if ($child->alias_type == 1) {
					// External link.  Set the link to the configured website URL.
					// This is guaranteed to be a full URL because of the
					// section::updateExternalAlias() method in datatypes/section.php
					$child->link = $child->external_link;
				} else if ($child->alias_type == 2) {
					// Internal link.
					
					// Need to check and see if the internal_id is pointing at an external link.
					$dest = $db->selectObject('section','id='.$child->internal_id);
					if ($dest->alias_type == 1) {
						// This internal alias is pointing at an external alias.
						// Use the external_link of the destination section for the link
						$child->link = $dest->external_link;
					} else {
						// Pointing at a regular section.  This is guaranteed to be
						// a regular section because aliases cannot be turned into sections,
						// (and vice-versa) and because the section::updateInternalLink
						// does 'alias to alias' dereferencing before the section is saved
						// (see datatypes/section.php)
						
						//added by Tyler to pull the descriptions through for the children view
						$child->description = $dest->description;
						
						$child->link = exponent_core_makeLink(array('section'=>$child->internal_id));
					}
				} else {
					// Normal link.  Just create the URL from the section's id.
					$child->link = exponent_core_makeLink(array('section'=>$child->id),'',$child->sef_name);
				}
				//$child->numChildren = $db->countObjects('section','parent='.$child->id);
				$nodes[] = $child;
				$nodes = array_merge($nodes,navigationmodule::levelTemplate($child->id,$depth+1,$parents));
			}
		}
		return $nodes;
	}

	
	function getTemplateHierarchyFlat($parent,$depth = 1) {
		global $db;
		
		$arr = array();
		$kids = $db->selectObjects('section_template','parent='.$parent);
		if (!defined('SYS_SORTING')) include_once(BASE.'subsystems/sorting.php');
		usort($kids,'exponent_sorting_byRankAscending');
		
		for ($i = 0; $i < count($kids); $i++) {
			$page = $kids[$i];
			
			$page->depth = $depth;
			$page->first = ($i == 0 ? 1 : 0);
			$page->last = ($i == count($kids)-1 ? 1 : 0);
			$arr[] = $page;
			$arr = array_merge($arr,navigationmodule::getTemplateHierarchyFlat($page->id,$depth + 1));
		}
		return $arr;
	}
	
	function process_section($section,$template) {
		global $db;
		
	        if (!is_object($template)) {
			$template = $db->selectObject('section_template','id='.$template);
			$section->subtheme = $template->subtheme;
			$db->updateObject($section,'section');
		}

		$prefix = '@st'.$template->id;
		$refs = $db->selectObjects('locationref',"source LIKE '$prefix%'");
		
		// Copy all modules and content for this section
		foreach ($refs as $ref) {
			$src = substr($ref->source,strlen($prefix)) . $section->id;
			
			if (call_user_func(array($ref->module,'hasContent'))) {
				$oloc = exponent_core_makeLocation($ref->module,$ref->source);
				$nloc = exponent_core_makeLocation($ref->module,$src);
		
				if ($ref->module != "containermodule") {	
					call_user_func(array($ref->module,'copyContent'),$oloc,$nloc);
				} else {
					call_user_func(array($ref->module,'copyContent'),$oloc,$nloc, $section->id);
				}
			}
		}
		
		// Grab sub pages
		foreach ($db->selectObjects('section_template','parent='.$template->id) as $t) {
			navigationmodule::process_subsections($section,$t);
		}
		
	}
	
	function process_subsections($parent_section,$subtpl) {
		global $db, $router;
		
		$section = null;
		$section->parent = $parent_section->id;
		$section->name = $subtpl->name;
		$section->sef_name = $router->encode($section->name);
		$section->subtheme = $subtpl->subtheme;
		$section->active = $subtpl->active;
		$section->public = $subtpl->public;
		$section->rank = $subtpl->rank;
		$section->page_title = $subtpl->page_title;
		$section->keywords = $subtpl->keywords;
		$section->description = $subtpl->description;
		
		$section->id = $db->insertObject($section,'section');
		
		navigationmodule::process_section($section,$subtpl);
	}
	
	function deleteLevel($parent) {
		global $db;
		$kids = $db->selectObjects('section','parent='.$parent);
		foreach ($kids as $kid) {
			navigationmodule::deleteLevel($kid->id);
		}
		$secrefs = $db->selectObjects('sectionref','section='.$parent);
		foreach ($secrefs as $secref) {
			$loc = exponent_core_makeLocation($secref->module,$secref->source,$secref->internal);
			exponent_core_decrementLocationReference($loc,$parent);
			
			foreach ($db->selectObjects('locationref',"module='".$secref->module."' AND source='".$secref->source."' AND internal='".$secref->internal."' AND refcount = 0") as $locref) {
				if (class_exists($locref->module)) {
				    $modclass = $locref->module;
				    
				    //FIXME: more module/controller glue code
	                if (controllerExists($modclass)) {
	                    $mod = new $modclass($iloc->src);
	                    $mod->delete_instance();
	                } else {
	                    $mod = new $modclass();
	                    $mod->deleteIn(exponent_core_makeLocation($locref->module,$locref->source,$locref->internal));
	                }
				}
			}
			$db->delete('locationref',"module='".$secref->module."' AND source='".$secref->source."' AND internal='".$secref->internal."' AND refcount = 0");
		}
		$db->delete('sectionref','section='.$parent);
		$db->delete('section','parent='.$parent);
	}
	
	function removeLevel($parent) {
		global $db;
		$kids = $db->selectObjects('section','parent='.$parent);
		foreach ($kids as $kid) {
			$kid->parent = -1;
			$db->updateObject($kid,'section');
			navigationmodule::removeLevel($kid->id);
		}
	}
	
	function canView($section) {
		global $db;
		if ($section == null) {return false;}
		if ($section->public == 0) {
			// Not a public section.  Check permissions.
			return exponent_permissions_check('view',exponent_core_makeLocation('navigationmodule','',$section->id));
		} else { // Is public.  check parents.
			if ($section->parent <= 0) {
				// Out of parents, and since we are still checking, we haven't hit a private section.
				return true;
			} else {
				$s = $db->selectObject('section','id='.$section->parent);
				return navigationmodule::canView($s);
			}
		}
	}


    function isPublic($s) {
       global $db;
	if ($s == null) {return false;}
        while ($s->public && $s->parent >0) {
            $s = $db->selectObject('section','id='.$s->parent);
        }
        $lineage = (($s->public)? 1 : 0);
        return $lineage;
    }

	function canManageStandalones() {
		if (exponent_users_isAdmin()) return true;
		$standalones = navigationmodule::levelTemplate(-1,0);
		$canmanage = false;
		foreach ($standalones as $standalone) {
			$loc = exponent_core_makeLocation('navigationmodule', '', $standalone->id);
			if (exponent_permissions_check('manage', $loc)) return true;
		}
	}

	function checkForSectionalAdmins($id) {
		global $db;

		$section = $db->selectObject('section', 'id='.$id);
		$branch = navigationmodule::levelTemplate($id, 0);
		array_unshift($branch, $section);

		$allusers = array();
		$allgroups = array();
		while ($section->parent > 0) {
			$ploc = exponent_core_makeLocation('navigationmodule', null, $section);
			$allusers = array_merge($allusers, $db->selectColumn('userpermission', 'uid', "permission='manage' AND module='navigationmodule' AND internal=".$section->parent));
			$allgroups = array_merge($allgroups, $db->selectColumn('grouppermission', 'gid', "permission='manage' AND module='navigationmodule' AND internal=".$section->parent));
			$section = $db->selectObject('section', 'id='.$section->parent);
		}
		
		foreach ($branch as $section) {
			$sloc = exponent_core_makeLocation('navigationmodule', null, $section->id);

			// remove any manage permissions for this page and it's children
			$db->delete('userpermission', "module='navigationmodule' AND internal=".$section->id);
			$db->delete('grouppermission', "module='navigationmodule' AND internal=".$section->id);

			foreach ($allusers as $uid) {
				$u = exponent_users_getUserById($uid);
				exponent_permissions_grant($u, 'manage', $sloc);
			}
			
			foreach ($allgroups as $gid) {
        	        	$g = exponent_users_getGroupById($gid);
                		exponent_permissions_grantGroup($g, 'manage', $sloc);
                	}
		}	
	}
    /*
	//Commented out per Hans and Tom
	function isPublic($section) {
		$hier = navigationmodule::levelTemplate(0,0);
		
		while (true) {
			//echo "Section:<br>";
			//echo "<xmp>";
			//print_r($section);
			//echo "</xmp>";
			//dump_debug();
			
			if ($section->public == 0) {
				// Not a public section.  Check permissions.
				return false;
			} else { // Is public.  check parents.
				if ($section->parent <= 0) {
					// Out of parents, and since we are still checking, we haven't hit a private section.
					return true;
				} else {
					$section = $hier[$section->parent];
	                return $section;
                }
			}
		}
	}
	*/
}

?>
