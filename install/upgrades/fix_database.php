<?php

##################################################
#
# Copyright (c) 2004-2017 OIC Group, Inc.
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
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class fix_database
 */
class fix_database extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.0.1';  // we no longer need to do this for every upgrade, only from OLD ones

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return gt("Repair/Replace Missing Database Table Entries"); }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return gt("Update cross-referenced entries in the containers and sectionref tables."); }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;  // run this script
	}

	/**
	 * searches the database for possible problems and attempts to fix them
	 * @return string
	 */
	function upgrade() {
	    global $db;

		print_r("<pre>");
	    print_r("<h4>(".gt("Some Conditions can NOT be repaired by this Procedure")."!)</h4><br>");
		print_r("<pre>");
	// upgrade sectionref's that have lost their originals
		print_r("<strong>".gt("Searching for sectionrefs that have lost their originals")."</strong><br><br>");
		$sectionrefs = $db->selectObjects('sectionref',"is_original=0");
		if (count($sectionrefs)) {
			print_r(gt("Found").": ".count($sectionrefs)." ".gt("copies (not originals)")."<br>");
		} else {
			print_r(" - ".gt("None Found: Good")."!<br>");
		}
		foreach ($sectionrefs as $sectionref) {
			if ($db->selectObject('sectionref',"module='".$sectionref->module."' AND source='".$sectionref->source."' AND is_original='1'") == null) {
			// There is no original for this sectionref so change it to the original
				$sectionref->is_original = 1;
				$db->updateObject($sectionref,"sectionref");
				print_r(gt("Fixed").": ".$sectionref->module." - ".$sectionref->source."<br>");
			}
		}
		print_r("</pre>");

		print_r("<pre>");
	// upgrade sectionref's that point to missing sections (pages)
		print_r("<strong>".gt("Searching for sectionrefs pointing to missing sections/pages")." <br>".gt("to fix for the Recycle Bin")."</strong><br><br>");
		$sectionrefs = $db->selectObjects('sectionref',"refcount!=0");
		$found = 0;
		foreach ($sectionrefs as $sectionref) {
			if ($db->selectObject('section',"id='".$sectionref->section."'") == null) {
			// There is no section/page for sectionref so change the refcount
				$sectionref->refcount = 0;
				$db->updateObject($sectionref,"sectionref");
				print_r(gt("Fixed").": ".$sectionref->module." - ".$sectionref->source."<br>");
				$found++;
			}
		}
		if (!$found) {
			print_r(" - ".gt("None Found: Good")."!<br>");
		}
		print_r("</pre>");

		 print_r("<pre>");
	 // delete sectionref's that have empty sources since they are dead
		 print_r("<strong>".gt("Searching for unassigned modules (no source)")."</strong><br><br>");
		 $sectionrefs = $db->selectObjects('sectionref','source=""');
		 if ($sectionrefs != null) {
			 print_r(gt("Removing").": ".count($sectionrefs)." ".gt("empty sectionrefs (no source)")."<br>");
			 $db->delete('sectionref','source=""');
		 } else {
			 print_r(" - ".gt("No Empties Found: Good")."!<br>");
		 }
		 print_r("</pre>");

		print_r("<pre>");
	// add missing sectionrefs based on existing containers (fixes aggregation problem)
		print_r("<strong>".gt("Searching for missing sectionrefs based on existing containers")."</strong><br><br>");
		$containers = $db->selectObjects('container',1);
		foreach ($containers as $container) {
			$iloc = expUnserialize($container->internal);
			if ($db->selectObject('sectionref',"module='".$iloc->mod."' AND source='".$iloc->src."'") == null) {
			// There is no sectionref for this container.  Populate sectionref
                $newSecRef = expCore::makeLocation($iloc->mod,$iloc->src);
				$newSecRef->refcount = 1;
				$newSecRef->is_original = 1;
				if ($container->external != "N;") {
					$eloc = expUnserialize($container->external);
					$section = $db->selectObject('sectionref',"module='container' AND source='".$eloc->src."'");
					if (!empty($section)) {
						$newSecRef->section = $section->id;
						$db->insertObject($newSecRef,"sectionref");
						print_r(gt("Missing sectionref for container replaced").": ".$iloc->mod." - ".$iloc->src." - PageID #".$section->id."<br>");
					} else {
						print_r(gt("Can't find the container page for container").": ".$iloc->mod." - ".$iloc->src."<br>");
					}
				}
			}
		}
		print_r("</pre>");
	}
}

?>
