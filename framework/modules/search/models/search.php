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
 * @package    Modules
 */

class search extends expRecord {

    public function beforeSave() {
        $this->body = $this->removeHTML($this->body);
    }

    public static function removeHTML($str) {
        $str = str_replace(array("\r\n", "\n"), " ", $str);
        return strip_tags(str_replace(array("<br/>", "<br>", "<br />", "</div>"), "\n", $str));
    }

    public function getSearchResults($terms, $only_best = false, $readonly = 0) {
        global $db, $user;

        // get the search terms
        //$terms = $this->params['search_string'];

        if (SAVE_SEARCH_QUERIES && $readonly == 0 && !empty($terms)) {
            if (INCLUDE_ANONYMOUS_SEARCH == 1 || $user->id <> 0) {
                $queryObj = new stdClass();
                $queryObj->user_id = $user->id;
                $queryObj->query = $terms;
                $queryObj->timestamp = time();

                $db->insertObject($queryObj, 'search_queries');
            }
        }

        //setup the sql query
        /*$sql  = "SELECT *, MATCH (s.title,s.body) AGAINST ('".$terms."') as score from ".DB_TABLE_PREFIX."_search as s ";
		$sql .= "LEFT OUTER JOIN ".DB_TABLE_PREFIX."_product p ON s.original_id = p.id WHERE MATCH(title,body) against ('".$terms."' IN BOOLEAN MODE)";
		
        SELECT *, MATCH (s.title,s.body) AGAINST ('army combat uniform') as score from exponent_search as s 
        LEFT OUTER JOIN exponent_product p ON s.original_id = p.id WHERE MATCH(s.title,s.body) against ('army combat uniform' IN BOOLEAN MODE)*/

        $sql = "SELECT *, MATCH (s.title,s.body) AGAINST ('" . $terms . "*') as score from " . DB_TABLE_PREFIX . "_search as s ";
        $sql .= "WHERE MATCH(title,body) against ('" . $terms . "*' IN BOOLEAN MODE) ";

        // look up the records.
        //eDebug($sql);
        $records = $db->selectObjectsBySql($sql);
        //eDebug($records);

        // search results due to permissions...not sure what to do about that.
        $recs = $records;
        for ($i = 0; $i < count($records); $i++) {
            if ($only_best && $records[$i]->score == 0) {
                unset($recs[$i]); // page is not available for viewing
//            } elseif ($records[$i]->ref_type == 'product') {
            } elseif ($records[$i]->ref_module == 'store') {
//                $score = $records[$i]->score;
                if (!product::canView($records[$i]->original_id)) {
                    unset($recs[$i]); // product is not available for viewing
                }
                /*else 
                {
                    $records[$i] = new product($records[$i]->original_id);
                    $records[$i]->score = $score;   
                }*/
            } else if ($records[$i]->ref_type == 'section') {
                $section = $db->selectObject('section', 'id=' . $records[$i]->original_id);
                if (empty($section) || !navigationController::canView($section)) {
                    unset($recs[$i]); // page is not available for viewing
                    //$records[$i]->canview = false;
                }
            } else {
                $rloc = unserialize($records[$i]->location_data);
                if (!empty($rloc)) {
//                    if (expModules::controllerExists($rloc->mod)) {
//                        $rloc->mod = expModules::getModuleName($rloc->mod);
//                    }
                    $sectionref = $db->selectObject("sectionref", "module='" . $rloc->mod . "' AND source='" . $rloc->src . "'");
                    if (!empty($sectionref)) {
                        $section = $db->selectObject("section", "id=" . $sectionref->section);
                        if (empty($section) || !(navigationController::canView($section) && !$db->selectObject('container', 'internal="' . $records[$i]->location_data . '" AND is_private=1'))) { // check page visibility
                            unset($recs[$i]); // item is not available for viewing
                            //$records[$i]->canview = false;
                        }
                    } else { // bad record in search index since it's not in sectionref table, don't display
//                        eDebug($recs[$i]);
                        unset($recs[$i]);
                    }
                } else { // bad record in search index, don't display
//                    eDebug($recs[$i]);
                    unset($recs[$i]);
                }
            }
        }

        return $recs;
    }
}

?>