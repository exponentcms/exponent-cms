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
 * @package    Modules
 */

class search extends expRecord {

    public function beforeSave() {
        $this->body = self::removeHTML($this->body);
    }

    public static function removeHTML($str) {
        if (empty($str))
            return $str;
        $str = str_replace(array("\r\n", "\n"), " ", $str);
        return strip_tags(str_replace(array("<br/>", "<br>", "<br />", "</div>"), "\n", $str));
    }

    public function getSearchResults($terms, $only_best = false, $readonly = 0, $eventlimit = null) {
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
//        $sql = "SELECT *, MATCH (s.title, s.body, s.keywords) AGAINST ('" . $terms . "*') AS score FROM " . $db->tableStmt('search') . " AS s ";
//        $sql .= "WHERE ";
//        if (ECOM) {
//            $search_type = ecomconfig::getConfig('ecom_search_results');
//            if ($search_type === 'ecom') {
//                $sql .= "ref_module = 'product' AND ";
//            } elseif ($search_type === 'products') {
//                $sql .= "ref_type = 'product' AND ";
//            }
//        }
//        $sql .= "MATCH (title, body, keywords) AGAINST ('" . $terms . "*' IN BOOLEAN MODE) ";

        // look up the records.
//        $records = $db->selectObjectsBySql($sql);
        $records = $db->selectSearch($terms);

        // modify search results based on permissions
        $recs = $records;
        for ($i = 0, $iMax = count($records); $i < $iMax; $i++) {
            if ($only_best && $records[$i]->score == 0) {
                unset($recs[$i]); // page is not available for viewing
//            } elseif ($records[$i]->ref_type == 'product') {
            } elseif ($records[$i]->ref_module === 'product' || $records[$i]->ref_module === 'store') {  //fixme for base code
//                $score = $records[$i]->score;
                if (!product::canView($records[$i]->original_id)) {
                    unset($recs[$i]); // product is not available for viewing
                }
                /*else
                {
                    $records[$i] = new product($records[$i]->original_id);
                    $records[$i]->score = $score;
                }*/
            } else if ($records[$i]->ref_type === 'section') {
//                $section = $db->selectObject('section', 'id=' . $records[$i]->original_id);
                $section = new section($records[$i]->original_id);
                if (empty($section) || !$section->canView()) {
                    unset($recs[$i]); // page is not available for viewing
                    //$records[$i]->canview = false;
                }
            } else if ($records[$i]->ref_module === 'event') {  // add (closest) date to title/link
                $event = $db->selectObject('eventdate', 'event_id=' . $records[$i]->original_id . ' ORDER BY ABS( DATEDIFF( date, NOW() ) )');
                if (!empty($event)) {
                    if (!empty($eventlimit) && $event->date < time()-($eventlimit*24*60*60))
                        unset($recs[$i]);
                    if ($event->date < time()) {  // emphasize past events
                        $records[$i]->title = "<em>" . $records[$i]->title . "</em>";
                    }
                    $records[$i]->title .= ' - ' . expDateTime::format_date($event->date);
                    $loc = expUnserialize($event->location_data);
                    $records[$i]->view_link = str_replace(URL_FULL, '', makeLink(array('controller' => 'event', 'action' => 'show', 'id' => $records[$i]->original_id, 'event_id' => $event->id, 'src' => $loc->src)));
                }
            } else if ($records[$i]->ref_module === 'forms') {
                //fixme here's where we remove search hit based on ???, but shouldn't be in here if not viewable
//                if (empty($section) || !$section->canView()) {
//                    unset($recs[$i]); // page is not available for viewing
//                    //$records[$i]->canview = false;
//                }
            } else {
                $rloc = unserialize($records[$i]->location_data);
                if (!empty($rloc)) {
//                    if (expModules::controllerExists($rloc->mod)) {
//                        $rloc->mod = expModules::getModuleName($rloc->mod);
//                    }
                    $sectionref = $db->selectObject("sectionref", "module='" . $rloc->mod . "' AND source='" . $rloc->src . "'");
                    if (!empty($sectionref)) {
//                        $section = $db->selectObject("section", "id=" . $sectionref->section);
                        $section = new section($sectionref->section);
                        if (empty($section) || !($section->canView($section) && !$db->selectObject('container', 'internal=\'' . $records[$i]->location_data . '\' AND is_private=1'))) { // check page visibility
                            unset($recs[$i]); // item is not available for viewing
                            continue; // skip rest of checks for this record
                            //$records[$i]->canview = false;
                        }
                        $controller = expModules::getController($recs[$i]->ref_module);
                        if (method_exists($controller, 'searchHit')) {
    //                        if (!$controller::searchHit($recs[$i])) {
                            if (!$controller->searchHit($recs[$i])) {
                                unset($recs[$i]); // item is not available for viewing
                            }
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