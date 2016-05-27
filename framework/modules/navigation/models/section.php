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
 * @subpackage Models
 * @package Modules
 */

class section extends expRecord {
//	public $table = 'section';

    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile'
    );

#	public $validates = array(
#		'presence_of'=>array(
#			'body'=>array('message'=>'Body is a required field.'),
#		));

    function __construct($params=null, $get_assoc=true, $get_attached=true) {
        parent::__construct($params, $get_assoc,$get_attached);
        if (empty($this->parent)) $this->parent = 0;
        $this->grouping_sql = " AND parent='".$this->parent."'";
    }

    function update($params=array()) {
        $this->grouping_sql = " AND parent='".$this->parent."'";
        if (empty($this->sef_name) && empty($params['sef_name']))
            $params['sef_name'] = self::makeSefUrl();
        parent::update($params);
        expSession::clearAllUsersSessionCache('navigation');
//        expHistory::back();
    }

    /**
     * make an sef_name for section
     *
     * @param string $title
     *
     * @return mixed|string
     */
    public function makeSefUrl()
    {
        global $db, $router;

        if (!empty($this->name)) {
            $sef_name = $router->encode($this->name);
        } else {
            $sef_name = $router->encode('Untitled');
        }
        $dupe = $db->selectValue($this->tablename, 'sef_name', 'sef_name="' . $sef_name . '"');
        if (!empty($dupe)) {
            list($u, $s) = explode(' ', microtime());
            $sef_name .= '-' . $s . '-' . $u;
        }
        return $sef_name;
    }

    public function beforeSave() {
        $this->grouping_sql = " AND parent='".$this->parent."'";
        parent::beforeSave();
    }

    function delete($where = '') {
        if ($this->parent == -1) {
            unset($this->rank);
            $where = '';
        } else {
            $where = "parent='".$this->parent."'";
        }
        parent::delete($where);
        section::deleteLevel($this->id);
        expSession::clearAllUsersSessionCache('navigation');
//        expHistory::back();
    }

    /** exdoc
     * Creates a location object, based off of the three arguments passed, and returns it.
     *
     * @return array
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
                    if ($dest->public == 1 || expPermissions::check('view', expCore::makeLocation('navigation', '', $dest->id))) {
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
                            $child->link = expCore::makeLink(array('section' => $child->internal_id));
                        }
                    } else {
                        $child = null;
                    }
                } else {
                    // Normal link, alias_type == 0.  Just create the URL from the section's id.
                    $child->link = expCore::makeLink(array('section' => $child->id), '', $child->sef_name);
                }
                //$child->numChildren = $db->countObjects('section','parent='.$child->id);
                if ($child !== null) {
                    $nodes[] = $child;
                    $nodes   = array_merge($nodes, self::levelTemplate($child->id, $depth + 1, $parents));
                }
            }
        }
        return $nodes;
    }

    /**
     * Check for cascading page view permission, esp. if not public
     */
    public function canView() {
        if ($this->public == 0) {
            // Not a public section.  Check permissions.
            return expPermissions::check('view', expCore::makeLocation('navigation', '', $this->id));
        } else { // Is public.  check parents.
            if ($this->parent <= 0) {
                // Out of parents, and since we are still checking, we haven't hit a private section.
                return true;
            } else {
                $s = new section($this->parent);
                return $s->canView();
            }
        }
    }

    /**
     * Check to see if page is public with cascading
     */
    public function isPublic() {
        $section = $this;
        while ($section->public && $section->parent > 0) {
            $section = new section($section->parent);
        }
        return (($section->public) ? 1 : 0);
    }

    /**
     * Check to see if page is top level page
     */
    public function isTopLevel() {
        return $this->parent == 0;
    }

    /**
     * Check to see if page is standalone
     */
    public function isStandalone() {
        return $this->parent == -1;
    }

    /**
     * return page parent
     */
    public  function getParent() {
        if ($this->parent != 0 && $this->parent != -1) {
            return new section($this->parent);
        } else {
            return false;
        }
    }

    /**
     * return page's top level parent
     */
    public function getTopParent() {
        $page = $this;
        do {
            $ret = $page;
            $page = $page->getParent();
        } while ($page !== false);
        return $ret;
    }

    /**
     * return sibling pages including top level and standalone
     */
    public function getSiblings($exclude_self = true) {
        if ($exclude_self) {
            $exclusion = ' AND id!=' . $this->id;
        } else {
            $exclusion = '';
        }
        $siblings = $this->find('all', 'parent=' . $this->parent . $exclusion);
        return $siblings;
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
     * Delete page and send its contents to the recycle bin
     *
     * @param $parent
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

    public static function navhierarchy($notyui=false) {
        global $sections;

        $json_array = array();
        for ($i = 0, $iMax = count($sections); $i < $iMax; $i++) {
            $obj = new stdClass();
            if ($sections[$i]->depth == 0) {
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

    public static function navtojson() {
        return json_encode(self::navhierarchy());
    }

    /**
     * Returns all subordinate sections
     *
     * @param $i            section
     * @param bool $notyui  format of return
     * @return array
     */
    public static function getChildren(&$i, $notyui=false, $singlelevel=false) {
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
                if (self::hasChildren($i) && !$singlelevel) {
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

    public static function hasChildren($i) {
        global $sections;

        if (($i + 1) >= count($sections)) return false;
        return ($sections[$i]->depth < $sections[$i + 1]->depth) ? true : false;
    }

}

?>