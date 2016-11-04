<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
        'showall' => 'Show Navigation',
        'breadcrumb' => 'Breadcrumb',
    );
//    protected $remove_permissions = array(
//        'configure',
//        'create',
//        'delete',
//        'edit'
//    );
    protected $add_permissions = array(
        'manage'    => 'Manage',
        'view'      => "View Page"
    );
    protected $manage_permissions = array(
        'move'      => 'Move Page',
        'remove'    => 'Remove Page',
        'reparent'    => 'Reparent Page',
        'dragndroprerank'    => 'Rerank Page',
        'dragndroprerank2'    => 'Rerank Page',
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'pagination',
        'rss',
        'tags',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Navigation"); }

    static function description() { return gt("Places navigation links/menus on the page."); }

    static function isSearchable() { return true; }

    function searchName() { return gt('Webpage'); }

    /**
     * @param null $src
     * @param array $params
     *
     */
    function __construct($src = null, $params = array())
    {
        parent::__construct($src, $params);
        if (!empty($params['id']))  // we normally throw out the $loc->int EXCEPT with navigation pages
            $this->loc = expCore::makeLocation($this->baseclassname, $src, $params['id']);
    }

    public function showall() {
        global $user, $sectionObj, $sections;

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
            'current'      => $current,
            'canManage'    => ((isset($user->is_acting_admin) && $user->is_acting_admin == 1) ? 1 : 0),
        ));
    }

    public function breadcrumb() {
        global $sectionObj;

        expHistory::set('viewable', $this->params);
        $id      = $sectionObj->id;
        $current = null;
        // Show not only the location of a page in the hierarchy but also the location of a standalone page
        $current = new section($id);
        if ($current->parent == -1) {  // standalone page
            $navsections = section::levelTemplate(-1, 0);
            foreach ($navsections as $section) {
                if ($section->id == $id) {
                    $current = $section;
                    break;
                }
            }
        } else {
            $navsections = section::levelTemplate(0, 0);
            foreach ($navsections as $section) {
                if ($section->id == $id) {
                    $current = $section;
                    break;
                }
            }
        }
        assign_to_template(array(
            'sections'     => $navsections,
            'current'      => $current,
        ));
    }

    /**
     * @deprecated 2.3.4 moved to section model
     */
    public static function navhierarchy($notyui=false) {
        global $sections;

        $json_array = array();
        for ($i = 0, $iMax = count($sections); $i < $iMax; $i++) {
            if ($sections[$i]->depth == 0) {
                $obj = new stdClass();
//   				$obj->id = $sections[$i]->name.$sections[$i]->id;
                $obj->id   = $sections[$i]->id;
                $obj->text = $sections[$i]->name;
                $obj->title = $sections[$i]->page_title;
                $obj->description = $sections[$i]->description;
                $obj->new_window = $sections[$i]->new_window;
                $obj->expFile = $sections[$i]->expFile;
                $obj->glyph = $sections[$i]->glyph;
                $obj->glyph_only = $sections[$i]->glyph_only;
                $obj->type = $sections[$i]->alias_type;
                if ($sections[$i]->active == 1) {
                    $obj->url = $sections[$i]->link;
                    if ($obj->type == 1 && substr($obj->url, 0, 4) != 'http') {
                        $obj->url = 'http://' . $obj->url;
                    }
                } else {
                    $obj->url     = "#";
                    $obj->onclick = "onclick: { fn: return false }";
                }
                if ($obj->type == 3) {  // mostly a hack instead of adding more table fields
                    $obj->width = $sections[$i]->internal_id;
                    $obj->class = $sections[$i]->external_link;
                }
                /*if ($sections[$i]->active == 1) {
                    $obj->disabled = false;
                } else {
                    $obj->disabled = true;
                }*/
                //$obj->disabled = true;
                $obj->itemdata = self::getChildren($i,$notyui);
                $obj->maxitems = count($obj->itemdata);
                $obj->maxdepth = 0;
                foreach ($obj->itemdata as $menu) {
                    if ($menu->maxdepth > $obj->maxdepth) $obj->maxdepth = $menu->maxdepth;
                }
            }
            $json_array[] = $obj;
        }
        return $json_array;
    }

    /**
     * @deprecated 2.3.4 moved to section model
     */
    public static function navtojson() {
        return json_encode(self::navhierarchy());
    }

    /**
     * @deprecated 2.3.4 moved to section model
     */
    public static function getChildren(&$i, $notyui=false) {
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
            for ($iMax = count($sections); $i < $iMax; $i++) {
                // start setting up the objects to return
                $obj       = new stdClass();
                $obj->id   = $sections[$i]->id;
                $obj->text = $sections[$i]->name;
                $obj->title = $sections[$i]->page_title;
                $obj->description = $sections[$i]->description;
                $obj->new_window = $sections[$i]->new_window;
                $obj->expFile = $sections[$i]->expFile;
                $obj->glyph = $sections[$i]->glyph;
                $obj->glyph_only = $sections[$i]->glyph_only;
                $obj->depth = $sections[$i]->depth;
                if ($sections[$i]->active == 1) {
                    $obj->url = $sections[$i]->link;
                    if ($sections[$i]->alias_type == 1 && substr($obj->url, 0, 4) != 'http') {
                        $obj->url = 'http://' . $obj->url;
                    }
                } else {
                    $obj->url     = "#";
                    $obj->onclick = "onclick: { fn: return false }";
                }
                //echo "i=".$i."<br>";
                if (self::hasChildren($i)) {
                    if ($notyui) {
                        $obj->itemdata = self::getChildren($i,$notyui);
                        $obj->maxitems = count($obj->itemdata);
                        $obj->maxdepth = 0;
                        foreach ($obj->itemdata as $menu) {
                            if (!empty($menu->maxdepth)) {
                                if ($menu->maxdepth > $obj->maxdepth) $obj->maxdepth = $menu->maxdepth;
                            } else {
                                if ($menu->depth > $obj->maxdepth) $obj->maxdepth = $menu->depth;
                            }
                        }
                    } else {
                        $obj->submenu     = new stdClass();
                        $obj->submenu->id = $sections[$i]->name . $sections[$i]->id;
                        //echo "getting children of ".$sections[$i]->name;
                        $obj->submenu->itemdata = self::getChildren($i,$notyui);
                        $obj->maxitems = count($obj->submenu->itemdata);
                        $obj->maxdepth = 0;
                        foreach ($obj->submenu->itemdata as $menu) {
                            if (!empty($menu->maxdepth)) {
                                if ($menu->maxdepth > $obj->maxdepth) $obj->maxdepth = $menu->maxdepth;
                            } else {
                                if ($menu->depth > $obj->maxdepth) $obj->maxdepth = $menu->depth;
                            }
                        }
                    }
                    $ret_array[]            = $obj;
                } else {
                    $obj->maxdepth = $obj->depth;
                    $ret_array[] = $obj;
                }
                if (($i + 1) >= count($sections) || $sections[$i + 1]->depth <= $ret_depth) {
                    return $ret_array;
                }
            }
            return array();
        }
    }

    /**
     * @deprecated 2.3.4 moved to section model
     */
    public static function hasChildren($i) {
        global $sections;

        if (($i + 1) >= count($sections)) return false;
        return ($sections[$i]->depth < $sections[$i + 1]->depth) ? true : false;
    }

    /** exdoc
     * Creates a location object, based off of the three arguments passed, and returns it.
     *
     * @return array
     * @deprecated 2.3.4 moved to section model
     */
    public static function initializeNavigation() {
        $sections = section::levelTemplate(0, 0);
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
     * @deprecated 2.3.4 moved to section model
     */
    public static function levelTemplate($parent, $depth = 0, $parents = array()) {
        global $user;

        if ($parent != 0) $parents[] = $parent;
        $nodes = array();
        $cache = expSession::getCacheValue('navigation');
        $sect = new section();
        if (!isset($cache['kids'][$parent])) {
            $kids = $sect->find('all','parent=' . $parent);
            $cache['kids'][$parent] = $kids;
            expSession::setCacheValue('navigation', $cache);
        } else {
            $kids = $cache['kids'][$parent];
        }
        $kids = expSorter::sort(array('array' => $kids, 'sortby' => 'rank', 'order' => 'ASC'));
        for ($i = 0, $iMax = count($kids); $i < $iMax; $i++) {
            $child = $kids[$i];
            //foreach ($kids as $child) {
            if ($child->public == 1 || expPermissions::check('view', expCore::makeLocation('navigation', '', $child->id))) {
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
//                    $dest = $db->selectObject('section', 'id=' . $child->internal_id);
                    $dest = $sect->find('first','id=' . $child->internal_id);
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
                    // Normal link, alias_type == 0.  Just create the URL from the section's id.
                    $child->link = expCore::makeLink(array('section' => $child->id), '', $child->sef_name);
                }
                //$child->numChildren = $db->countObjects('section','parent='.$child->id);
                $nodes[] = $child;
                $nodes   = array_merge($nodes, section::levelTemplate($child->id, $depth + 1, $parents));
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
     * @param bool   $addinternalalias
     *
     * @return array
     * @deprecated 2.3.4 moved to section model, HOWEVER still used in theme config
     */
    public static function levelDropdownControlArray($parent, $depth = 0, $ignore_ids = array(), $full = false, $perm = 'view', $addstandalones = false, $addinternalalias = true) {
        global $db;

        $ar = array();
        if ($parent == 0 && $full) {
            $ar[0] = '&lt;' . gt('Top of Hierarchy') . '&gt;';
        }
        if ($addinternalalias) {
            $intalias = '';
        } else {
            $intalias = ' AND alias_type != 2';
        }
        $nodes = $db->selectObjects('section', 'parent=' . $parent . $intalias, 'rank');
        foreach ($nodes as $node) {
            if ((($perm == 'view' && $node->public == 1) || expPermissions::check($perm, expCore::makeLocation('navigation', '', $node->id))) && !in_array($node->id, $ignore_ids)) {
                if ($node->active == 1) {
                    $text = str_pad('', ($depth + ($full ? 1 : 0)) * 3, '.', STR_PAD_LEFT) . $node->name;
                } else {
                    $text = str_pad('', ($depth + ($full ? 1 : 0)) * 3, '.', STR_PAD_LEFT) . '(' . $node->name . ')';
                }
                $ar[$node->id] = $text;
                foreach (self::levelDropdownControlArray($node->id, $depth + 1, $ignore_ids, $full, $perm, $addstandalones, $addinternalalias) as $id => $text) {
                    $ar[$id] = $text;
                }
            }
        }
        if ($addstandalones && $parent == 0) {
            $sections = $db->selectObjects('section', 'parent=-1');
            foreach ($sections as $node) {
                if ((($perm == 'view' && $node->public == 1) || expPermissions::check($perm, expCore::makeLocation('navigation', '', $node->id))) && !in_array($node->id, $ignore_ids)) {
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
//        $db->delete('search', "ref_module='navigation' AND ref_type='section'");
        $db->delete('search', "ref_module='".$this->baseclassname."' AND ref_type='section'");
        // this now ensures we get internal pages, instead of relying on the global $sections, which does not.
        $sections = $db->selectObjects('section', 'active=1');
        foreach ($sections as $section) {
            $search_record = new stdClass();
//            $search_record->category = 'Webpages';
//            $search_record->ref_module = 'navigationController';
//            $search_record->ref_type = 'section';
//            $search_record->ref_module  = $this->classname;
            $search_record->ref_module  = $this->baseclassname;
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
            $loc = expCore::makeLocation('text');
            $controllername = 'text';
            foreach ($db->selectObjects('sectionref', "module='" . $controllername . "' AND section=" . $section->id) as $module) {
                $loc->src   = $module->source;
//                $controller = new $controllername();
                $controller = expModules::getController($controllername);
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
            if (($node->public == 1 || expPermissions::check('view', expCore::makeLocation('navigation', '', $node->id))) && !in_array($node->id, $ignore_ids)) {
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
     * @deprecated 2.0.0 this only for deprecated templates
     */
    public static function getTemplateHierarchyFlat($parent, $depth = 1) {
        global $db;

        $arr  = array();
        $kids = $db->selectObjects('section_template', 'parent=' . $parent, 'rank');
//		$kids = expSorter::sort(array('array'=>$kids,'sortby'=>'rank', 'order'=>'ASC'));
        for ($i = 0, $iMax = count($kids); $i < $iMax; $i++) {
            $page        = $kids[$i];
            $page->depth = $depth;
            $page->first = ($i == 0 ? 1 : 0);
            $page->last  = ($i == count($kids) - 1 ? 1 : 0);
            $arr[]       = $page;
            $arr         = array_merge($arr, self::getTemplateHierarchyFlat($page->id, $depth + 1));
        }
        return $arr;
    }

    /**
     * @deprecated 2.0.0 this only for deprecated templates
     */
    public static function process_section($section, $template) {
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
                if ($ref->module != "container") {
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

    /**
     * @deprecated 2.0.0 this only for deprecated templates
     */
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

    /**
     * Delete page and send its contents to the recycle bin
     *
     * @param $parent
     * @deprecated 2.3.4 moved to section model
     */
    public static function deleteLevel($parent) {
        global $db;

        $kids = $db->selectObjects('section', 'parent=' . $parent);
        foreach ($kids as $kid) {
            self::deleteLevel($kid->id);
        }
        $secrefs = $db->selectObjects('sectionref', 'section=' . $parent);
        foreach ($secrefs as $secref) {
            $loc = expCore::makeLocation($secref->module, $secref->source, $secref->internal);
            recyclebin::sendToRecycleBin($loc, $parent);
            //FIXME if we delete the module & sectionref the module completely disappears
//            if (class_exists($secref->module)) {
//                $modclass = $secref->module;
//                //FIXME: more module/controller glue code
//                if (expModules::controllerExists($modclass)) {
//                    $modclass = expModules::getControllerClassName($modclass);
//                    $mod = new $modclass($loc->src);
//                    $mod->delete_instance();
//                } else {
//                    $mod = new $modclass();
//                    $mod->deleteIn($loc);
//                }
//            }
        }
//        $db->delete('sectionref', 'section=' . $parent);
        $db->delete('section', 'parent=' . $parent);
    }

    /**
     * Move content page and its children to stand-alones
     *
     * @param $parent
     * @deprecated 2.3.4 moved to section model
     */
    public static function removeLevel($parent) {
        global $db;

        $kids = $db->selectObjects('section', 'parent=' . $parent);
        foreach ($kids as $kid) {
            $kid->parent = -1;
            $db->updateObject($kid, 'section');
            self::removeLevel($kid->id);
        }
    }

    /**
     * Check for cascading page view permission, esp. if not public
     * @deprecated 2.3.4 moved to section model
     */
    public static function canView($section) {
        global $db;

        if ($section == null) {
            return false;
        }
        if ($section->public == 0) {
            // Not a public section.  Check permissions.
            return expPermissions::check('view', expCore::makeLocation('navigation', '', $section->id));
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

    /**
     * Check to see if page is public with cascading
     * @deprecated 2.3.4 moved to section model
     */
    public static function isPublic($s) {
        if ($s == null) {
            return false;
        }
        while ($s->public && $s->parent > 0) {
            $s = new section($s->parent);
        }
        $lineage = (($s->public) ? 1 : 0);
        return $lineage;
    }

    public static function canManageStandalones() {
        global $user;

        if ($user->isAdmin()) return true;
        $standalones = section::levelTemplate(-1, 0);
        //		$canmanage = false;
        foreach ($standalones as $standalone) {
            $loc = expCore::makeLocation('navigation', '', $standalone->id);
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
        $branch  = section::levelTemplate($id, 0);
        array_unshift($branch, $section);
        $allusers  = array();
        $allgroups = array();
        while ($section->parent > 0) {
            //			$ploc = expCore::makeLocation('navigationController', null, $section);
            $allusers  = array_merge($allusers, $db->selectColumn('userpermission', 'uid', "permission='manage' AND module='navigation' AND internal=" . $section->parent));
            $allgroups = array_merge($allgroups, $db->selectColumn('grouppermission', 'gid', "permission='manage' AND module='navigation' AND internal=" . $section->parent));
            $section   = $db->selectObject('section', 'id=' . $section->parent);
        }
        foreach ($branch as $section) {
            $sloc = expCore::makeLocation('navigation', null, $section->id);
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
            'canManageStandalones' => self::canManageStandalones(),
            'sasections'           => $db->selectObjects('section', 'parent=-1'),
            'user'                 => $user,
//            'canManagePagesets'    => $user->isAdmin(),
//            'templates'            => $db->selectObjects('section_template', 'parent=0'),
        ));
    }

    public function manage_sitemap() {
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
            'sasections'   => $db->selectObjects('section', 'parent=-1'),
            'sections'     => $navsections,
            'current'      => $current,
            'canManage'    => ((isset($user->is_acting_admin) && $user->is_acting_admin == 1) ? 1 : 0),
        ));
    }

    /**
     * Ajax request for specific pages as json date to yui tree
     */
    public static function returnChildrenAsJSON() {
        global $db;

        //$nav = section::levelTemplate(intval($_REQUEST['id'], 0));
        $id         = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $nav        = $db->selectObjects('section', 'parent=' . $id, 'rank');
        //FIXME $manage_all is moot w/ cascading perms now?
        $manage_all = false;
        if (expPermissions::check('manage', expCore::makeLocation('navigation', '', $id))) {
            $manage_all = true;
        }
        //FIXME recode to use foreach $key=>$value
        $navcount = count($nav);
        for ($i = 0; $i < $navcount; $i++) {
            if ($manage_all || expPermissions::check('manage', expCore::makeLocation('navigation', '', $nav[$i]->id))) {
                $nav[$i]->manage = 1;
                $view = true;
            } else {
                $nav[$i]->manage = 0;
                $view = $nav[$i]->public ? true : expPermissions::check('view', expCore::makeLocation('navigation', '', $nav[$i]->id));
            }
            $nav[$i]->link = expCore::makeLink(array('section' => $nav[$i]->id), '', $nav[$i]->sef_name);
            if (!$view) unset($nav[$i]);
        }
        $nav= array_values($nav);
//        $nav[$navcount - 1]->last = true;
        if (count($nav)) $nav[count($nav) - 1]->last = true;
//        echo expJavascript::ajaxReply(201, '', $nav);
        $ar = new expAjaxReply(201, '', $nav);
        $ar->send();
    }

    /**
     * Ajax request for all pages as json date to jstree
     */
    public static function returnChildrenAsJSON2() {
        global $db;

        $icons = array(
            0 => 'addpage',
            1 => 'addextpage',
            2 => 'addintpage',
            3 => 'addfreeform',
        );

        $navs        = $db->selectObjects('section', 'parent!=-1', 'rank');
        foreach ($navs as $i=>$nav) {
            $navs[$i]->parent = $nav->parent?$nav->parent:'#';
            $navs[$i]->text = $nav->name;
            $navs[$i]->icon = $icons[$nav->alias_type];
            if (!$nav->active) {
                $navs[$i]->icon .= ' inactive';
                $attr = new stdClass();
                $attr->class = 'inactive';  // class to obscure elements
                $navs[$i]->a_attr = $attr;
            }
            if (expPermissions::check('manage', expCore::makeLocation('navigation', '', $navs[$i]->id))) {
                $navs[$i]->manage = 1;
                $view = true;
            } else {
                $navs[$i]->manage = 0;
                $navs[$i]->state->disabled = true;
                $view = $navs[$i]->public ? true : expPermissions::check('view', expCore::makeLocation('navigation', '', $navs[$i]->id));
            }
            $navs[$i]->link = expCore::makeLink(array('section' => $navs[$i]->id), '', $navs[$i]->sef_name);
            if (!$view) {
//                unset($navs[$i]);  //FIXME this breaks jstree if we remove a parent and not the child
                $attr = new stdClass();
                $attr->class = 'hidden';  // bs3 class to hide elements
                $navs[$i]->li_attr = $attr;
            }
        }
        $navs= array_values($navs);
//        header('Content-Type: application/json; charset=utf8');
		echo json_encode($navs);
//        echo expJavascript::ajaxReply(201, '', $navs);
        exit;
    }

    /**
     * Ajax function to reorder page hierarchy from yui tree control
     */
    public static function DragnDropReRank() {
        global $db, $router;

        $move   = intval($router->params['move']);
        $target = intval($router->params['target']);
        $type   = $router->params['type'];
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
            $moveSec->rank = 1;
            //select all children currently of the parent we're about to append to
            $targSecChildren = $db->selectObjects("section", "parent=" . $targSec->id . " ORDER BY rank");
            //update the ranks of the children to +1 higher to accommodate our new rank 0 section being moved in.
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
                $childOfLastMove = $db->selectObjects("section", "parent=" . $oldParent . " ORDER BY rank");
                for ($i = 0, $iMax = count($childOfLastMove); $i < $iMax; $i++) {
                    $childOfLastMove[$i]->rank = $i;
                    $db->updateObject($childOfLastMove[$i], 'section');
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
                    $rerank               = 1;
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
            } else {  // 'before', is this used?
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
                $rerank      = 1;
                foreach ($oldSiblings as $value) {
                    if ($value->id != $moveSec->id) {
                        $value->rank = $rerank;
                        $db->updateObject($value, 'section');
                        $rerank++;
                    }
                }
                if ($oldParent != $moveSec->parent) {
                    //we need to re-rank the children of the parent that the moving section has just left
                    $childOfLastMove = $db->selectObjects("section", "parent=" . $oldParent . " ORDER BY rank");
                    for ($i = 0, $iMax = count($childOfLastMove); $i < $iMax; $i++) {
                        $childOfLastMove[$i]->rank = $i;
                        $db->updateObject($childOfLastMove[$i], 'section');
                    }
                }
            }
        }
        self::checkForSectionalAdmins($move);
        expSession::clearAllUsersSessionCache('navigation');
    }

    /**
     * Ajax function to reorder page hierarchy from jstree control
     */
    public static function DragnDropReRank2() {
        global $router, $db;

        $id = $router->params['id'];
        $page = new section($id);
        $old_rank = $page->rank;
        $old_parent = $page->parent;
        $new_rank = $router->params['position'] + 1;  // rank
        $new_parent = intval($router->params['parent']);

        $db->decrement($page->tablename, 'rank', 1, 'rank>' . $old_rank . ' AND parent=' . $old_parent);  // close in hole
        $db->increment($page->tablename, 'rank', 1, 'rank>=' . $new_rank . ' AND parent=' . $new_parent);  // make room

        $params = array();
        $params['parent'] = $new_parent;
        $params['rank'] = $new_rank;
        $page->update($params);

        self::checkForSectionalAdmins($id);
        expSession::clearAllUsersSessionCache('navigation');
    }

    function edit_section() {
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
//        $section = null;
        $section = new stdClass();
        if (isset($this->params['id'])) {
            // Check to see if an id was passed in get.  If so, retrieve that section from
            // the database, and perform an edit on it.
            $section  = $this->section->find($this->params['id']);
        } elseif (isset($this->params['parent'])) {
            // The isset check is merely a precaution.  This action should
            // ALWAYS be invoked with a parent or id value.
            $section  = new section($this->params);
        } else {
            notfoundController::handle_not_found();
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
                    notfoundController::handle_not_authorized();
                    exit;
                    //$section->parent = 0;
                }
            }
            assign_to_template(array(
                'section' => $section,
                'glyphs' => self::get_glyphs(),
            ));
        } else {  // User does not have permission to manage sections.  Throw a 403
            notfoundController::handle_not_authorized();
        }
    }

    private static function get_glyphs() {
        if (bs()) {
            require_once(BASE . 'external/font-awesome.class.php');
            $fa = new Smk_FontAwesome;
            if (bs3()) {
                $icons = $fa->getArray(BASE . 'external/font-awesome4/css/font-awesome.css');
                $icons = $fa->sortByName($icons);
                return $fa->nameGlyph($icons);
            } elseif (bs2()) {
                expCSS::auto_compile_less(
                    'external/font-awesome/less/font-awesome.less',
                    'external/font-awesome/css/font-awesome.css'
                ); // font-awesome is included within bootstrap2, but not as a separate .css file
                $icons = $fa->getArray(BASE . 'external/font-awesome/css/font-awesome.css', 'icon-');
                return $fa->nameGlyph($icons, 'icon-');
            }
        } else {
            return array();
        }
    }

    function edit_internalalias() {
        $section = isset($this->params['id']) ? $this->section->find($this->params['id']) : new section($this->params);
        if ($section->parent == -1) {
            notfoundController::handle_not_found();
            exit;
        } // doesn't work for standalone pages
        if (empty($section->id)) {
            $section->public = 1;
            if (!isset($section->parent)) {
                // This is another precaution.  The parent attribute
                // should ALWAYS be set by the caller.
                //FJD - if that's the case, then we should die.
                notfoundController::handle_not_authorized();
                exit;
                //$section->parent = 0;
            }
        }
        assign_to_template(array(
            'section' => $section,
            'glyphs' => self::get_glyphs(),
        ));
    }

    function edit_freeform() {
        $section = isset($this->params['id']) ? $this->section->find($this->params['id']) : new section($this->params);
        if ($section->parent == -1) {
            notfoundController::handle_not_found();
            exit;
        } // doesn't work for standalone pages
        if (empty($section->id)) {
            $section->public = 1;
            if (!isset($section->parent)) {
                // This is another precaution.  The parent attribute
                // should ALWAYS be set by the caller.
                //FJD - if that's the case, then we should die.
                notfoundController::handle_not_authorized();
                exit;
                //$section->parent = 0;
            }
        }
        assign_to_template(array(
            'section' => $section,
            'glyphs' => self::get_glyphs(),
        ));
    }

    function edit_externalalias() {
        $section = isset($this->params['id']) ? $this->section->find($this->params['id']) : new section($this->params);
        if ($section->parent == -1) {
            notfoundController::handle_not_found();
            exit;
        } // doesn't work for standalone pages
        if (empty($section->id)) {
            $section->public = 1;
            if (!isset($section->parent)) {
                // This is another precaution.  The parent attribute
                // should ALWAYS be set by the caller.
                //FJD - if that's the case, then we should die.
                notfoundController::handle_not_authorized();
                exit;
                //$section->parent = 0;
            }
        }
        assign_to_template(array(
            'section' => $section,
            'glyphs' => self::get_glyphs(),
        ));
    }

    function update() {
        parent::update();
        expSession::clearAllUsersSessionCache('navigation');
    }

    function move_standalone() {
        expSession::clearAllUsersSessionCache('navigation');
        assign_to_template(array(
            'parent' => $this->params['parent'],
        ));
    }

    /**
     * Move standalone back to hierarchy
     *
     */
    function reparent_standalone() {
        $standalone = $this->section->find($this->params['page']);
        if ($standalone) {
            $standalone->parent = $this->params['parent'];
            $standalone->update();
            expSession::clearAllUsersSessionCache('navigation');
            expHistory::back();
        } else {
            notfoundController::handle_not_found();
        }
    }

    /**
     * Move content page to standalones
     *
     */
    function remove() {
        global $db;

        $section = $db->selectObject('section', 'id=' . $this->params['id']);
        if ($section) {
            section::removeLevel($section->id);
            $db->decrement('section', 'rank', 1, 'rank > ' . $section->rank . ' AND parent=' . $section->parent);
            $section->parent = -1;
            $db->updateObject($section, 'section');
            expSession::clearAllUsersSessionCache('navigation');
            expHistory::back();
        } else {
            notfoundController::handle_not_authorized();
        }
    }

    function delete_standalones() {
        if (!empty($this->params['deleteit'])) {
            foreach ($this->params['deleteit'] as $page) {
                $section = new section(intval($page));
                if ($section) {
//                    self::deleteLevel($section->id);
                    $section->delete();
                }
            }
        }
        expSession::clearAllUsersSessionCache('navigation');
        expHistory::back();
    }

    /**
     * permission functions to aggregate a module's visible permissions based on add/remove permissions
     *
     * @return array
     */
    public function permissions() {
        //set the permissions array
        return $this->add_permissions;
    }

    // create a psuedo global manage pages permission
    public static function checkPermissions($permission,$location) {
        global $exponent_permissions_r, $router;

        // only applies to the 'manage' method
        if (empty($location->src) && empty($location->int) && ((!empty($router->params['action']) && $router->params['action'] == 'manage') || strpos($router->current_url, 'action=manage') !== false)) {
            if (!empty($exponent_permissions_r['navigation'])) foreach ($exponent_permissions_r['navigation'] as $page) {
                foreach ($page as $pageperm) {
                    if (!empty($pageperm['manage'])) return true;
                }
            }
        }
        return false;
    }

    /**
     * Rebuild the sectionref table as a list of modules on a page
     * @deprecated 2.3.4 moved to sectionref model
     */
    public static function rebuild_sectionrefs() {
        global $db;

        // recursive run though all the nested containers
        function scan_container($container_id, $page_id) {
            global $db;

            $containers = $db->selectObjects('container',"external='" . $container_id . "'");
            $ret = '';
            foreach ($containers as $container) {
                $iLoc = expUnserialize($container->internal);
                $newret = recyclebin::restoreFromRecycleBin($iLoc, $page_id);
                if (!empty($newret)) $ret .= $newret . '<br>';
                if ($iLoc->mod == 'container') {
                    $ret .= scan_container($container->internal, $page_id);
                }
            }
            return $ret;
        }

        // recursive run through all the nested pages
        function scan_page($parent_id) {
            global $db;

            $sections = $db->selectObjects('section','parent=' . $parent_id);
            $ret = '';
            foreach ($sections as $page) {
                $cLoc = serialize(expCore::makeLocation('container','@section' . $page->id));
                $ret .= scan_container($cLoc, $page->id);
                $ret .= scan_page($page->id);
            }
            return $ret;
        }

        // first remove duplicate records
        $db->sql('DELETE FROM ' . $db->prefix . 'sectionref WHERE id NOT IN (SELECT * FROM (SELECT MIN(n.id) FROM ' . $db->prefix . 'sectionref n GROUP BY n.module, n.source) x)');
        $ret = scan_page(0);  // the page hierarchy
        $ret .= scan_page(-1);  // now the stand alone pages

        // we need to get the non-main containers such as sidebars, footers, etc...
        $hardcodedmods = $db->selectObjects('sectionref',"refcount=1000 AND source NOT LIKE '%@section%' AND source NOT LIKE '%@random%'");
        foreach ($hardcodedmods as $hardcodedmod) {
            if ($hardcodedmod->module == 'container') {
                $page_id = intval(preg_replace('/\D/', '', $hardcodedmod->source));
                if (empty($page_id)) {
                    $page_id = SITE_DEFAULT_SECTION;  // we'll default to the home page
                }
                $ret .= scan_container(serialize(expCore::makeLocation($hardcodedmod->module, $hardcodedmod->source)), $page_id);
            } else {
                $hardcodedmod->section = 0;  // this is a hard-coded non-container module
                $db->updateObject($hardcodedmod, 'sectionref');
            }
        }

        // mark modules in the recycle bin as section 0
        $db->columnUpdate('sectionref', 'section', 0, "refcount=0");
//        $recycledmods = $db->selectObjects('sectionref',"refcount=0");
//        foreach ($recycledmods as $recycledmod) {
//            $recycledmod->section = 0;  // this is a module in the recycle bin
//            $db->updateObject($recycledmod, 'sectionref');
//        }
        return $ret;
    }

}

?>
