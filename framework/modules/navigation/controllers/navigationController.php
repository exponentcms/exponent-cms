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
 * @subpackage Controllers
 * @package    Modules
 */
class navigationController extends expController {
    public $basemodel_name = 'section';
    public $useractions = array(
        'showall' => 'Show Menu',
        'breadcrumb' => 'Breadcrumb',
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'files',
//        'module_title',
        'pagination',
        'rss',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')
    public $add_permissions = array(
        'view' => "View Page"
    );
    public $remove_permissions = array(
        'configure',
        'create',
        'delete',
        'edit'
    );
    public $codequality = 'alpha';

    static function displayname() { return gt("Navigation"); }

    static function description() { return gt("Places navigation links/menus on the page."); }

    static function isSearchable() { return true; }

    function searchName() { return gt('Webpage'); }

    public function showall() {
        global $db, $user, $sectionObj, $sections;

        expHistory::set('viewable', $this->params);
        $id      = $sectionObj->id;
        $current = null;
        // all we need to do is determine the current section
        $navsections = $sections;
        if ($sectionObj->parent == -1) {
            $current = $sectionObj;
        } else {
            foreach ($navsections as $section) {
                if ($section->id == $id) {
                    $current = $section;
                    break;
                }
            }
        }
        assign_to_template(array(
            'sections'     => $navsections,
//            'hierarchy'    => self::navhierarchy(),
            'current'      => $current,
//            'num_sections' => count($sections),
            'canManage'    => ((isset($user->is_acting_admin) && $user->is_acting_admin == 1) ? 1 : 0),
        ));
    }

    public function breadcrumb() {
        global $db, $user, $sectionObj, $sections;

        expHistory::set('viewable', $this->params);
        $id      = $sectionObj->id;
        $current = null;
        // Show not only the location of a page in the hierarchy but also the location of a standalone page
        $current = $db->selectObject('section', ' id= ' . $id);
        if ($current->parent == -1) {  // standalone page
            $navsections = self::levelTemplate(-1, 0);
            foreach ($navsections as $section) {
                if ($section->id == $id) {
                    $current = $section;
                    break;
                }
            }
        } else {
            $navsections = self::levelTemplate(0, 0);
            foreach ($navsections as $section) {
                if ($section->id == $id) {
                    $current = $section;
                    break;
                }
            }
        }
        assign_to_template(array(
            'sections'     => $navsections,
//            'hierarchy'    => self::navhierarchy(),
            'current'      => $current,
//            'num_sections' => count($sections),
//            'canManage'    => ((isset($user->is_acting_admin) && $user->is_acting_admin == 1) ? 1 : 0),
        ));
    }

    public static function navhierarchy() {
        global $sections;

        $json_array = array();
        for ($i = 0; $i < count($sections); $i++) {
            if ($sections[$i]->depth == 0) {
                $obj = new stdClass();
//   				$obj->id = $sections[$i]->name.$sections[$i]->id;
                $obj->id   = $sections[$i]->id;
                $obj->text = $sections[$i]->name;
                if ($sections[$i]->active == 1) {
                    $obj->url = $sections[$i]->link;
                } else {
                    $obj->url     = "#";
                    $obj->onclick = "onclick: { fn: return false }";
                }
                /*if ($sections[$i]->active == 1) {
                    $obj->disabled = false;
                } else {
                    $obj->disabled = true;
                }*/
                //$obj->disabled = true;
                $obj->itemdata = self::getChildren($i);
            }
            $json_array[] = $obj;
        }
        return $json_array;
    }

    public static function navtojson() {
        return json_encode(self::navhierarchy());
    }

    public static function getChildren(&$i) {
        global $sections;

        //		echo "i=".$i."<br>";
        if ($i + 1 == count($sections)) { // last entry
            return array();
        } elseif ($sections[$i]->depth == $sections[$i + 1]->depth) {
            return array();
        } else {
            $ret_depth = $sections[$i]->depth;
            $i++;
            $ret_array = array();
            for ($i; $i < count($sections); $i++) {
                // start setting up the objects to return
                $obj       = new stdClass();
                $obj->id   = $sections[$i]->id;
                $obj->text = $sections[$i]->name;
                if ($sections[$i]->active == 1) {
                    $obj->url = $sections[$i]->link;
                } else {
                    $obj->url     = "#";
                    $obj->onclick = "onclick: { fn: return false }";
                }
                //echo "i=".$i."<br>";
                if (self::hasChildren($i)) {
                    $obj->submenu     = new stdClass();
                    $obj->submenu->id = $sections[$i]->name . $sections[$i]->id;
                    //echo "getting children of ".$sections[$i]->name;
                    $obj->submenu->itemdata = self::getChildren($i);
                    $ret_array[]            = $obj;
                } else {
                    $ret_array[] = $obj;
                }
                if (($i + 1) >= count($sections) || $sections[$i + 1]->depth <= $ret_depth) {
                    return $ret_array;
                }
            }
            return array();
        }
    }

    public static function hasChildren($i) {
        global $sections;

        if (($i + 1) >= count($sections)) return false;
        return ($sections[$i]->depth < $sections[$i + 1]->depth) ? true : false;
    }

    /** exdoc
     * Creates a location object, based off of the three arguments passed, and returns it.
     *
     * @internal param \The $mo module component of the location.
     *
     * @internal param \The $src source component of the location.
     *
     * @internal param \The $int internal component of the location.
     * @return array
     * @node     Subsystems:expCore
     */
    public static function initializeNavigation() {
        $sections = self::levelTemplate(0, 0);
        return $sections;
    }

    /**
     * returns all the section's children
     *
     * @static
     *
     * @param int   $parent top level parent id
     * @param int   $depth  variable to hold level of recursion
     * @param array $parents
     *
     * @return array
     */
    public static function levelTemplate($parent, $depth = 0, $parents = array()) {
        global $db, $user;

        if ($parent != 0) $parents[] = $parent;
        $nodes = array();
        $cache = expSession::getCacheValue('navigation');
        if (!isset($cache['kids'][$parent])) {
            $kids                   = $db->selectObjects('section', 'parent=' . $parent);
            $cache['kids'][$parent] = $kids;
            expSession::setCacheValue('navigation', $cache);
        } else {
            $kids = $cache['kids'][$parent];
        }
        $kids = expSorter::sort(array('array' => $kids, 'sortby' => 'rank', 'order' => 'ASC'));
        for ($i = 0; $i < count($kids); $i++) {
            $child = $kids[$i];
            //foreach ($kids as $child) {
            if ($child->public == 1 || expPermissions::check('view', expCore::makeLocation('navigationController', '', $child->id))) {
                $child->numParents    = count($parents);
                $child->depth         = $depth;
                $child->first         = ($i == 0 ? 1 : 0);
                $child->last          = ($i == count($kids) - 1 ? 1 : 0);
                $child->parents       = $parents;
                $child->canManage     = (isset($user->is_acting_admin) && $user->is_acting_admin == 1 ? 1 : 0);
                $child->canManageRank = $child->canManage;
                if (!isset($child->sef_name)) {
                    $child->sef_name = '';
                }
                // Generate the link attribute base on alias type.
                if ($child->alias_type == 1) {
                    // External link.  Set the link to the configured website URL.
                    // This is guaranteed to be a full URL because of the
                    // section::updateExternalAlias() method in models-1/section.php
                    $child->link = $child->external_link;
                } else if ($child->alias_type == 2) {
                    // Internal link.
                    // Need to check and see if the internal_id is pointing at an external link.
                    $dest = $db->selectObject('section', 'id=' . $child->internal_id);
                    if (!empty($dest->alias_type) && $dest->alias_type == 1) {
                        // This internal alias is pointing at an external alias.
                        // Use the external_link of the destination section for the link
                        $child->link = $dest->external_link;
                    } else {
                        // Pointing at a regular section.  This is guaranteed to be
                        // a regular section because aliases cannot be turned into sections,
                        // (and vice-versa) and because the section::updateInternalLink
                        // does 'alias to alias' dereferencing before the section is saved
                        // (see models-1/section.php)
                        //added by Tyler to pull the descriptions through for the children view
                        $child->description = !empty($dest->description) ? $dest->description : '';
                        $child->link        = expCore::makeLink(array('section' => $child->internal_id));
                    }
                } else {
                    // Normal link.  Just create the URL from the section's id.
                    $child->link = expCore::makeLink(array('section' => $child->id), '', $child->sef_name);
                }
                //$child->numChildren = $db->countObjects('section','parent='.$child->id);
                $nodes[] = $child;
                $nodes   = array_merge($nodes, self::levelTemplate($child->id, $depth + 1, $parents));
            }
        }
        return $nodes;
    }

    /**
     * Returns a flat representation of the full site hierarchy.
     *
     * @param int    $parent         top level parent id
     * @param int    $depth          variable to hold level of recursion
     * @param array  $ignore_ids     array of pages to ignore
     * @param bool   $full           include a 'top' level entry
     * @param string $perm           permission level to build list
     * @param bool   $addstandalones should we add the stand-alone pages also
     *
     * @return array
     */
    public static function levelDropDownControlArray($parent, $depth = 0, $ignore_ids = array(), $full = false, $perm = 'view', $addstandalones = false) {
        global $db;

        $ar = array();
        if ($parent == 0 && $full) {
            $ar[0] = '&lt;' . gt('Top of Hierarchy') . '&gt;';
        }
        $nodes = $db->selectObjects('section', 'parent=' . $parent, 'rank');
//		$nodes = expSorter::sort(array('array'=>$nodes,'sortby'=>'rank', 'order'=>'ASC'));
        foreach ($nodes as $node) {
            if ((($perm == 'view' && $node->public == 1) || expPermissions::check($perm, expCore::makeLocation('navigationController', '', $node->id))) && !in_array($node->id, $ignore_ids)) {
                if ($node->active == 1) {
                    $text = str_pad('', ($depth + ($full ? 1 : 0)) * 3, '.', STR_PAD_LEFT) . $node->name;
                } else {
                    $text = str_pad('', ($depth + ($full ? 1 : 0)) * 3, '.', STR_PAD_LEFT) . '(' . $node->name . ')';
                }
                $ar[$node->id] = $text;
                foreach (self::levelDropdownControlArray($node->id, $depth + 1, $ignore_ids, $full, $perm) as $id => $text) {
                    $ar[$id] = $text;
                }
            }
        }
        if ($addstandalones && $parent == 0) {
            $sections = $db->selectObjects('section', 'parent=-1');
            foreach ($sections as $node) {
                if ((($perm == 'view' && $node->public == 1) || expPermissions::check($perm, expCore::makeLocation('navigationController', '', $node->id))) && !in_array($node->id, $ignore_ids)) {
                    if ($node->active == 1) {
                        $text = str_pad('', ($depth + ($full ? 1 : 0)) * 3, '.', STR_PAD_LEFT) . $node->name;
                    } else {
                        $text = str_pad('', ($depth + ($full ? 1 : 0)) * 3, '.', STR_PAD_LEFT) . '(' . $node->name . ')';
                    }
                    $ar[$node->id] = '(' . gt('Standalone') . ') ' . $text;
                }
            }
//            $ar = array_merge($ar,$sections);
        }
        return $ar;
    }

    /**
     * add all module items to search index
     *
     * @return int
     */
    function addContentToSearch() {
        global $db;

        //global $sections;
        //		global $router;
        $db->delete('search', "ref_module='navigationController' AND ref_type='section'");
        // this now ensures we get internal pages, instead of relying on the global $sections, which does not.
        $sections = $db->selectObjects('section', 'active=1');
        foreach ($sections as $section) {
            $search_record = new stdClass();
//            $search_record->category = 'Webpages';
//            $search_record->ref_module = 'navigationController';
//            $search_record->ref_type = 'section';
            $search_record->ref_module  = $this->classname;
            $search_record->category    = $this->searchName();
            $search_record->ref_type    = $this->searchCategory();
            $search_record->original_id = $section->id;
            $search_record->title       = $section->name;
            //$search_record->view_link = $router->buildUrlByPageId($section->id);
            $link = str_replace(URL_FULL, '', makeLink(array('section' => $section->id)));
            if ($link . '/' == URL_FULL) $link = '';
            $search_record->view_link = $link;
            $search_record->body      = $section->description;
            $search_record->keywords  = $section->keywords;
            // now we're going to grab all the textmodules on this page and build the body for the page based off the content
            // of all the text module added together.
            $loc            = new stdClass();
            $loc->mod       = $this->baseclassname;
            $loc->int       = '';
            $controllername = $this->classname;
            foreach ($db->selectObjects('sectionref', "module='" . $controllername . "' AND section=" . $section->id) as $module) {
                $loc->src   = $module->source;
                $controller = new $controllername();
                $textitems  = $db->selectObjects($controller->model_table, "location_data='" . serialize($loc) . "'");
                foreach ($textitems as $textitem) {
                    if (!empty($textitem)) {
                        $search_record->body .= ' ' . search::removeHTML($textitem->body) . ' ';
                        $search_record->keywords .= " " . $textitem->title;
                    }
                }
            }
            $db->insertObject($search_record, 'search');
        }
        return count($sections);

        // old method
        global $db, $router;
        $count   = 0;
        $model   = new $this->basemodel_name(null, false, false);
        $content = $db->selectArrays($model->tablename);
        foreach ($content as $cnt) {
            $origid = $cnt['id'];
            unset($cnt['id']);
            $sql      = "original_id=" . $origid . " AND ref_module='" . $this->classname . "'";
            $oldindex = $db->selectObject('search', $sql);
            if (!empty($oldindex)) {
                $search_record = new search($oldindex->id, false, false);
                $search_record->update($cnt);
            } else {
                $search_record = new search($cnt, false, false);
            }
            //build the search record and save it.
            $search_record->original_id = $origid;
            $search_record->posted      = empty($cnt['created_at']) ? null : $cnt['created_at'];
            // get the location data for this content
            if (isset($cnt['location_data'])) $loc = expUnserialize($cnt['location_data']);
            $src = isset($loc->src) ? $loc->src : null;
            if (!empty($cnt['sef_url'])) {
                $link = str_replace(URL_FULL, '', makeLink(array('controller' => $this->baseclassname, 'action' => 'show', 'title' => $cnt['sef_url'])));
            } else {
                $link = str_replace(URL_FULL, '', makeLink(array('controller' => $this->baseclassname, 'action' => 'show', 'id' => $origid, 'src' => $src)));
            }
//	        if (empty($search_record->title)) $search_record->title = 'Untitled';
            $search_record->view_link  = $link;
            $search_record->ref_module = $this->classname;
            $search_record->category   = $this->searchName();
            $search_record->ref_type   = $this->searchCategory();
            $search_record->save();
            $count += 1;
        }
        return $count;
    }

    /**
     * Retrieve either the entire hierarchy, or a subset of the hierarchy, as an array suitable for use
     * in a dropdowncontrol.  This is used primarily by the section datatype for moving and adding
     * sections to specific parts of the site hierarchy.
     *
     * @param int   $parent     The id of the subtree parent.  If passed as 0 (the default), the entire subtree is parsed.
     * @param int   $depth
     * @param int   $default
     * @param array $ignore_ids a value-array of IDs to be ignored when generating the list.  This is used
     *                          when moving a section, since a section cannot be made a subsection of itself or any of its subsections.
     *
     * @return string
     */
    function levelShowDropdown($parent, $depth = 0, $default = 0, $ignore_ids = array()) {
        global $db;

        $html = '';
        $nodes = $db->selectObjects('section', 'parent=' . $parent, 'rank');
//		$nodes = expSorter::sort(array('array'=>$nodes,'sortby'=>'rank', 'order'=>'ASC'));
        foreach ($nodes as $node) {
            if (($node->public == 1 || expPermissions::check('view', expCore::makeLocation('navigationController', '', $node->id))) && !in_array($node->id, $ignore_ids)) {
                $html .= '<option value="' . $node->id . '" ';
                if ($default == $node->id) $html .= 'selected';
                $html .= '>';
                if ($node->active == 1) {
                    $html .= str_pad('', $depth * 3, '.', STR_PAD_LEFT) . $node->name;
                } else {
                    $html .= str_pad('', $depth * 3, '.', STR_PAD_LEFT) . '(' . $node->name . ')';
                }
                $html .= '</option>';
                $html .= self::levelShowDropdown($node->id, $depth + 1, $default, $ignore_ids);
            }
        }
        return $html;
    }

    /**
     * recursively lists the template hierarchy
     *
     * @static
     *
     * @param int $parent top level parent id
     * @param int $depth  variable to hold level of recursion
     *
     * @return array
     */
    public static function getTemplateHierarchyFlat($parent, $depth = 1) {
        global $db;

        $arr  = array();
        $kids = $db->selectObjects('section_template', 'parent=' . $parent, 'rank');
//		$kids = expSorter::sort(array('array'=>$kids,'sortby'=>'rank', 'order'=>'ASC'));
        for ($i = 0; $i < count($kids); $i++) {
            $page        = $kids[$i];
            $page->depth = $depth;
            $page->first = ($i == 0 ? 1 : 0);
            $page->last  = ($i == count($kids) - 1 ? 1 : 0);
            $arr[]       = $page;
            $arr         = array_merge($arr, self::getTemplateHierarchyFlat($page->id, $depth + 1));
        }
        return $arr;
    }

    public static function process_section($section, $template) { //FIXME is this only for deprecated templates?
        global $db;

        if (!is_object($template)) {
            $template          = $db->selectObject('section_template', 'id=' . $template);
            $section->subtheme = $template->subtheme;
            $db->updateObject($section, 'section');
        }
        $prefix = '@st' . $template->id;
        $refs   = $db->selectObjects('sectionref', "source LIKE '$prefix%'");
        // Copy all modules and content for this section
        foreach ($refs as $ref) {
            $src = substr($ref->source, strlen($prefix)) . $section->id;
            if (call_user_func(array($ref->module, 'hasContent'))) {
                $oloc = expCore::makeLocation($ref->module, $ref->source);
                $nloc = expCore::makeLocation($ref->module, $src);
                if ($ref->module != "containermodule") {
                    call_user_func(array($ref->module, 'copyContent'), $oloc, $nloc);
                } else {
                    call_user_func(array($ref->module, 'copyContent'), $oloc, $nloc, $section->id);
                }
            }
        }
        // Grab sub pages
        foreach ($db->selectObjects('section_template', 'parent=' . $template->id) as $t) {
            self::process_subsections($section, $t);
        }

    }

    function process_subsections($parent_section, $subtpl) {
        global $db, $router;

        $section              = new stdClass();
        $section->parent      = $parent_section->id;
        $section->name        = $subtpl->name;
        $section->sef_name    = $router->encode($section->name);
        $section->subtheme    = $subtpl->subtheme;
        $section->active      = $subtpl->active;
        $section->public      = $subtpl->public;
        $section->rank        = $subtpl->rank;
        $section->page_title  = $subtpl->page_title;
        $section->keywords    = $subtpl->keywords;
        $section->description = $subtpl->description;
        $section->id          = $db->insertObject($section, 'section');
        self::process_section($section, $subtpl);
    }

    public static function deleteLevel($parent) {
        global $db;

        $kids = $db->selectObjects('section', 'parent=' . $parent);
        foreach ($kids as $kid) {
            self::deleteLevel($kid->id);
        }
        $secrefs = $db->selectObjects('sectionref', 'section=' . $parent);
        foreach ($secrefs as $secref) {
            $loc = expCore::makeLocation($secref->module, $secref->source, $secref->internal);
            expCore::decrementLocationReference($loc, $parent);
            if (class_exists($secref->module)) {
                $modclass = $secref->module;
                //FIXME: more module/controller glue code
                if (expModules::controllerExists($modclass)) {
                    $mod = new $modclass($loc->src);
                    $mod->delete_instance();
                } else {
                    $mod = new $modclass();
                    $mod->deleteIn($loc);
                }
            }
        }
        $db->delete('sectionref', 'section=' . $parent);
        $db->delete('section', 'parent=' . $parent);
    }

    public static function removeLevel($parent) {
        global $db;

        $kids = $db->selectObjects('section', 'parent=' . $parent);
        foreach ($kids as $kid) {
            $kid->parent = -1;
            $db->updateObject($kid, 'section');
            self::removeLevel($kid->id);
        }
    }

    public static function canView($section) {
        global $db;

        if ($section == null) {
            return false;
        }
        if ($section->public == 0) {
            // Not a public section.  Check permissions.
            return expPermissions::check('view', expCore::makeLocation('navigationController', '', $section->id));
        } else { // Is public.  check parents.
            if ($section->parent <= 0) {
                // Out of parents, and since we are still checking, we haven't hit a private section.
                return true;
            } else {
                $s = $db->selectObject('section', 'id=' . $section->parent);
                return self::canView($s);
            }
        }
    }

    public static function isPublic($s) {
        global $db;

        if ($s == null) {
            return false;
        }
        while ($s->public && $s->parent > 0) {
            $s = $db->selectObject('section', 'id=' . $s->parent);
        }
        $lineage = (($s->public) ? 1 : 0);
        return $lineage;
    }

    public static function canManageStandalones() {
        global $user;

        if ($user->isAdmin()) return true;
        $standalones = self::levelTemplate(-1, 0);
        //		$canmanage = false;
        foreach ($standalones as $standalone) {
            $loc = expCore::makeLocation('navigationController', '', $standalone->id);
            if (expPermissions::check('manage', $loc)) return true;
        }
        return false;
    }

    /**
     * Reassign permissions based on a check/change in menu/page hierarchy
     *
     * @static
     *
     * @param $id
     */
    public static function checkForSectionalAdmins($id) {
        global $db;

        $section = $db->selectObject('section', 'id=' . $id);
        $branch  = self::levelTemplate($id, 0);
        array_unshift($branch, $section);
        $allusers  = array();
        $allgroups = array();
        while ($section->parent > 0) {
            //			$ploc = expCore::makeLocation('navigationController', null, $section);
            $allusers  = array_merge($allusers, $db->selectColumn('userpermission', 'uid', "permission='manage' AND module='navigationController' AND internal=" . $section->parent));
            $allgroups = array_merge($allgroups, $db->selectColumn('grouppermission', 'gid', "permission='manage' AND module='navigationController' AND internal=" . $section->parent));
            $section   = $db->selectObject('section', 'id=' . $section->parent);
        }
        foreach ($branch as $section) {
            $sloc = expCore::makeLocation('navigationController', null, $section->id);
            // remove any manage permissions for this page and it's children
            // $db->delete('userpermission', "module='navigationController' AND internal=".$section->id);
            // $db->delete('grouppermission', "module='navigationController' AND internal=".$section->id);
            foreach ($allusers as $uid) {
                $u = user::getUserById($uid);
                expPermissions::grant($u, 'manage', $sloc);
            }
            foreach ($allgroups as $gid) {
                $g = group::getGroupById($gid);
                expPermissions::grantGroup($g, 'manage', $sloc);
            }
        }
    }

    function manage() {
        global $db, $router, $user;

        expHistory::set('manageable', $router->params);
        assign_to_template(array(
            'canManageStandalones' => navigationController::canManageStandalones(),
            'sasections'           => $db->selectObjects('section', 'parent=-1'),
            'user'                 => $user,
            'canManagePagesets'    => $user->isAdmin(),
            'templates'            => $db->selectObjects('section_template', 'parent=0'),
        ));
    }

    public static function returnChildrenAsJSON() {
        global $db;

        //$nav = navigationController::levelTemplate(intval($_REQUEST['id'], 0));
        $id         = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $nav        = $db->selectObjects('section', 'parent=' . $id, 'rank');
        $manage_all = false;
        if (expPermissions::check('manage', expCore::makeLocation('navigationController', '', $id))) {
            $manage_all = true;
        }
        $navcount = count($nav);
        for ($i = 0; $i < $navcount; $i++) {
            if ($manage_all || expPermissions::check('manage', expCore::makeLocation('navigationController', '', $nav[$i]->id))) {
                $nav[$i]->manage = 1;
            } else {
                $nav[$i]->manage = 0;
            }
            $nav[$i]->link = expCore::makeLink(array('section' => $nav[$i]->id), '', $nav[$i]->sef_name);
        }
        $nav[$navcount - 1]->last = true;
        echo expJavascript::ajaxReply(201, '', $nav);
        exit;
    }

    public static function DragnDropReRank() {
        global $db;

        $move   = intval($_REQUEST['move']);
        $target = intval($_REQUEST['target']);
        $type   = $_REQUEST['type'];
        $targSec = $db->selectObject("section","id=".$target);
//        $targSec  = new section($target);
        $check_id = $targSec->parent;
        $moveSec = $db->selectObject("section","id=".$move);
//        $moveSec = new section($move);

        // dropped on top of page
        if ($type == "append") {
            //save the old parent in case we are changing the depth of the moving section
            $oldParent = $moveSec->parent;
            //assign the parent of the moving section to the ID of the target section
            $moveSec->parent = $targSec->id;
            //set the rank of the moving section to 0 since it will appear first in the new order
            $moveSec->rank = 0;
            //select all children currently of the parent we're about to append to
            $targSecChildren = $db->selectObjects("section", "parent=" . $targSec->id . " ORDER BY rank");
            //update the ranks of the children to +1 higher to accomodate our new ranl 0 section being moved in.
            $newrank = 1;
            foreach ($targSecChildren as $value) {
                if ($value->id != $moveSec->id) {
                    $value->rank = $newrank;
                    $db->updateObject($value, 'section');
                    $newrank++;
                }
            }
            $db->updateObject($moveSec, 'section');
            if ($oldParent != $moveSec->parent) {
                //we need to re-rank the children of the parent that the miving section has just left
                $chilOfLastMove = $db->selectObjects("section", "parent=" . $oldParent . " ORDER BY rank");
                for ($i = 0; $i < count($chilOfLastMove); $i++) {
                    $chilOfLastMove[$i]->rank = $i;
                    $db->updateObject($chilOfLastMove[$i], 'section');
                }

            }
//            echo $moveSec->name . " was appended to " . $targSec->name;

        } elseif ($type == "after") {  // dropped between (after) pages
            if ($targSec->parent == $moveSec->parent) {
                //are we moving up...
                if ($targSec->rank < $moveSec->rank) {
                    $moveSec->rank    = $targSec->rank + 1;
                    $moveNextSiblings = $db->selectObjects("section", "id!=" . $moveSec->id . " AND parent=" . $targSec->parent . " AND rank>" . $targSec->rank . " ORDER BY rank");
                    $rerank           = $moveSec->rank + 1;
                    foreach ($moveNextSiblings as $value) {
                        if ($value->id != $moveSec->id) {
                            $value->rank = $rerank;
                            $db->updateObject($value, 'section');
                            $rerank++;
                        }
                    }
                    $db->updateObject($targSec, 'section');
//                    $targSec->update();
                    $db->updateObject($moveSec, 'section');
//                    $moveSec->update();
                    //or are we moving down...
                } else {
                    $targSec->rank        = $targSec->rank - 1;
                    $moveSec->rank        = $targSec->rank + 1;
                    $movePreviousSiblings = $db->selectObjects("section", "id!=" . $moveSec->id . " AND parent=" . $targSec->parent . " AND rank<=" . $targSec->rank . " ORDER BY rank");
                    $rerank               = 0;
                    foreach ($movePreviousSiblings as $value) {
                        if ($value->id != $moveSec->id) {
                            $value->rank = $rerank;
                            $db->updateObject($value, 'section');
                            $rerank++;
                        }
                    }
                    $db->updateObject($targSec, 'section');
//                    $targSec->update();
                    $db->updateObject($moveSec, 'section');
//                    $moveSec->update();
                }
            } else {
                //store ranks from the depth we're moving from.  Used to re-rank the level depth the moving section is moving from.
                $oldRank   = $moveSec->rank;
                $oldParent = $moveSec->parent;
                //select all children of the target sections parent with a rank higher than it's own
                $moveNextSiblings = $db->selectObjects("section", "parent=" . $targSec->parent . " AND rank>" . $targSec->rank . " ORDER BY rank");
                //update moving sections rank and parent
                $moveSec->rank   = $targSec->rank + 1;
                $moveSec->parent = $targSec->parent;
                //$rerank=$moveSec->rank+1;
                foreach ($moveNextSiblings as $value) {
                    $value->rank = $value->rank + 1;
                    $db->updateObject($value, 'section');
                }
                $db->updateObject($moveSec, 'section');
                //handle re-ranking of previous parent
                $oldSiblings = $db->selectObjects("section", "parent=" . $oldParent . " AND rank>" . $oldRank . " ORDER BY rank");
                $rerank      = 0;
                foreach ($oldSiblings as $value) {
                    if ($value->id != $moveSec->id) {
                        $value->rank = $rerank;
                        $db->updateObject($value, 'section');
                        $rerank++;
                    }
                }
                if ($oldParent != $moveSec->parent) {
                    //we need to re-rank the children of the parent that the miving section has just left
                    $chilOfLastMove = $db->selectObjects("section", "parent=" . $oldParent . " ORDER BY rank");
                    for ($i = 0; $i < count($chilOfLastMove); $i++) {
                        $chilOfLastMove[$i]->rank = $i;
                        $db->updateObject($chilOfLastMove[$i], 'section');
                    }
                }
            }
        }
        navigationController::checkForSectionalAdmins($move);
        expSession::clearAllUsersSessionCache('navigation');
    }

    function add_section() {
        global $db, $user;

        $parent = new section($this->params['parent']);
        if (empty($parent->id)) $parent->id = 0;
        assign_to_template(array(
            'haveStandalone'  => ($db->countObjects('section', 'parent=-1') && $parent->id >= 0),
            'parent'          => $parent,
            'isAdministrator' => $user->isAdmin(),
        ));
    }

    function edit_contentpage() {
        //FIXME we come here for new/edit content/standalone pages
        // FIXME: Allow non-administrative users to manage certain parts of the section hierarchy.
        //if ($user->is_acting_admin == 1 /*TODO: section admin*/) {
        $section = null;
        if (isset($this->params['id'])) {
            // Check to see if an id was passed in get.  If so, retrieve that section from
            // the database, and perform an edit on it.
            $section  = $this->section->find($this->params['id']);
        } elseif (isset($this->params['parent'])) {
            // The isset check is merely a precaution.  This action should
            // ALWAYS be invoked with a parent or id value.
            $section  = new section($this->params);
        } else {
            echo SITE_404_HTML;
            exit;
        }
        if (!empty($section->id)) {
            $check_id = $section->id;
        } else {
            $check_id = $section->parent;
        }
        if (expPermissions::check('manage', expCore::makeLocation('navigation', '', $check_id))) {
            if (empty($section->id)) {
                $section->active = 1;
                $section->public = 1;
                if (!isset($section->parent)) {
                    // This is another precaution.  The parent attribute
                    // should ALWAYS be set by the caller.
                    //FJD - if that's the case, then we should die.
                    die(SITE_403_HTML);
                    //$section->parent = 0;
                }
            }
            assign_to_template(array(
                'section' => $section,
            ));
        } else {  // User does not have permission to manage sections.  Throw a 403
            echo SITE_403_HTML;
        }
    }

    function edit_internalalias() {
        $section = isset($this->params['id']) ? $this->section->find($this->params['id']) : new section($this->params);
        if ($section->parent == -1) {
            echo SITE_404_HTML;
            exit;
        } // doesn't work for standalone pages
        if (empty($section->id)) {
            $section->public = 1;
            if (!isset($section->parent)) {
                // This is another precaution.  The parent attribute
                // should ALWAYS be set by the caller.
                //FJD - if that's the case, then we should die.
                die(SITE_403_HTML);
                //$section->parent = 0;
            }
        }
        assign_to_template(array(
            'section' => $section,
        ));
    }

    function edit_externalalias() {
        $section = isset($this->params['id']) ? $this->section->find($this->params['id']) : new section($this->params);
        if ($section->parent == -1) {
            echo SITE_404_HTML;
            exit;
        } // doesn't work for standalone pages
        if (empty($section->id)) {
            $section->public = 1;
            if (!isset($section->parent)) {
                // This is another precaution.  The parent attribute
                // should ALWAYS be set by the caller.
                //FJD - if that's the case, then we should die.
                die(SITE_403_HTML);
                //$section->parent = 0;
            }
        }
        assign_to_template(array(
            'section' => $section,
        ));
    }

    function move_standalone() {
        expSession::clearAllUsersSessionCache('navigation');
        assign_to_template(array(
            'parent' => $this->params['parent'],
        ));
    }

    function reparent_standalone() {
        $standalone = $this->section->find($this->params['page']);
        if ($standalone) {
            $standalone->parent = $this->params['parent'];
            $standalone->update();
            expSession::clearAllUsersSessionCache('navigation');
            expHistory::back();
        } else {
            echo SITE_404_HTML;
        }
    }

    function remove() {
        global $db;

        $section = $db->selectObject('section', 'id=' . $this->params['id']);
        if ($section) {
            navigationController::removeLevel($section->id);
            $db->decrement('section', 'rank', 1, 'rank > ' . $section->rank . ' AND parent=' . $section->parent);
            $section->parent = -1;
            $db->updateObject($section, 'section');
            expSession::clearAllUsersSessionCache('navigation');
            expHistory::back();
        } else {
            echo SITE_403_HTML;
        }
    }

    function delete_standalones() {
        if (!empty($this->params['deleteit'])) {
            foreach ($this->params['deleteit'] as $page) {
                $section = new section(intval($page));
                if ($section) {
                    navigationController::deleteLevel($section->id);
                    $section->delete();
                }
            }
        }
        expSession::clearAllUsersSessionCache('navigation');
        expHistory::back();
    }

}

?>
