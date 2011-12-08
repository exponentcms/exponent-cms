<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class search extends expRecord {

	public function beforeSave() {
	    $this->body = $this->removeHTML($this->body);
	}
	
	public static function removeHTML($str) {
	    $str = str_replace(array("\r\n","\n")," ",$str);
    	return strip_tags(str_replace(array("<br/>","<br>","<br />","</div>"),"\n",$str));
	}
	
	public function getSearchResults($terms) {
	    global $db, $user;
	    
	    // get the search terms
        //$terms = $this->params['search_string'];
        
        if (SAVE_SEARCH_QUERIES)
        {
            $queryObj = new stdClass();
			$queryObj->user_id    = $user->id;
            $queryObj->query      = $terms;
            $queryObj->timestamp  = time();
            
            $db->insertObject($queryObj, 'search_queries');
        } 
        
        //setup the sql query
        /*$sql  = "SELECT *, MATCH (s.title,s.body) AGAINST ('".$terms."') as score from ".DB_TABLE_PREFIX."_search as s ";
		$sql .= "LEFT OUTER JOIN ".DB_TABLE_PREFIX."_product p ON s.original_id = p.id WHERE MATCH(title,body) against ('".$terms."' IN BOOLEAN MODE)";
		
        SELECT *, MATCH (s.title,s.body) AGAINST ('army combat uniform') as score from exponent_search as s 
        LEFT OUTER JOIN exponent_product p ON s.original_id = p.id WHERE MATCH(s.title,s.body) against ('army combat uniform' IN BOOLEAN MODE)*/
        
        $sql  = "SELECT *, MATCH (s.title,s.body) AGAINST ('".$terms."*') as score from ".DB_TABLE_PREFIX."_search as s ";
        $sql .= "WHERE MATCH(title,body) against ('".$terms."*' IN BOOLEAN MODE)";
		
        // look up the records.
        //eDebug($sql);
		$records = $db->selectObjectsBySql($sql);
		//eDebug($records);
        
        //FIXME: The page count is off when have to not show 
        // search results due to permissions...not sure what to do about that.
        $recs = $records;
        for ($i=0; $i < count($records); $i++) {
            if($records[$i]->ref_type == 'product'){ 
                $score = $records[$i]->score;               
                if (!product::canView($records[$i]->original_id))  unset($recs[$i]); 
                /*else 
                {
                    $records[$i] = new product($records[$i]->original_id);
                    $records[$i]->score = $score;   
                }*/
           }else if ($records[$i]->ref_type == 'section') {	
		        $section = $db->selectObject('section', 'id='.$records[$i]->original_id);
                if (empty($section) || !navigationmodule::canView($section)) {
                    unset($recs[$i]);
                    //$records[$i]->canview = false;
                }
	       } else {
                $rloc = unserialize($records[$i]->location_data);
                if (!empty($rloc)) {
                    $sectionref = $db->selectObject("sectionref","module='".expModules::getControllerClassName($rloc->mod)."' AND source='".$rloc->src."'");
                    if (!empty($sectionref)) {
                        $section = $db->selectObject("section","id=".$sectionref->section);
                        if (empty($section) || !navigationmodule::canView($section)) {
                            unset($recs[$i]);
                            //$records[$i]->canview = false;
                        }
                    }
                }
	        }
	    }
	    
	    return $recs;
	}
}

?>
