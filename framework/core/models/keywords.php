<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
/** @define "BASE" "../../.." */

/**
 * @subpackage Models
 * @package Core
 */
class keywords {

	public function getAndWeightKeywords($text) {
		if (empty($text)) return array();

		$words = explode(' ', str_ireplace(self::getExcludedWords(), ' ', $text));

		foreach($words as $word) {
        	if (!empty($word)) {
    		    $keywords[$word] = empty($keywords[$word]) ? 1 : $keywords[$word] += 1;
			}
        }

		return $keywords;
	}													

	public static function getExcludedWords() {
		$puncs = array('&#160;', '.', ',', "'", '"', ':', '!', "\r", "\n", "\t");
		$common = array(' and ', ' to ', ' the ', ' a ', ' at ', ' or ', ' it ', ' in ', ' our ', ' no ', ' yes ', ' where ', ' us', 'you', 'for');
		return array_merge($puncs, $common);
	}

	public function getTextBySection($section) {
		global $db;

		$id = is_object($section) ? $section->id : $section;
		$refs = $db->selectObjects('sectionref', 'section='.$id);
	
		ob_start();
		$mods = array();
		foreach ($refs as $ref) {
            $loc = expCore::makeLocation($ref->module,$ref->source,$ref->internal);
			if (!empty($loc->src)) {
				if ($ref->module == 'container') {
					foreach($db->selectObjects('container', "external='".serialize($loc)."'") as $mod) {
						$mods[] = $mod;
						$modloc = unserialize($mod->internal);
						expTheme::showAction($modloc->mod, 'index', $modloc->src, array('view'=>$mod->view, 'title'=>$mod->title));
					}
				} else {
					foreach($db->selectObjects('container', "internal='".serialize($loc)."'") as $mod) {
						$mods[] = $mod;
					}
				}
			}
		}

		$text = search::removeHTML(ob_get_contents());
		ob_end_clean();
		return $text;
	}

	public function getKeywordsForSection($section) {
//		global $db;

		$id = is_object($section) ? $section->id : $section;
		return self::getAndWeightKeywords(self::getTextBySection($section));
	}
}

?>