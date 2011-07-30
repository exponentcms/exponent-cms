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

/**
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class fix_database
 */
class fix_database extends upgradescript {
	protected $from_version = '0.96.3';
//	protected $to_version = '2.0.1';

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	function name() { return "Replace Missing Database Table Entries"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Update cross-referenced entries in the containers, locationref, and sectionref tables."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;  // run this script
	}
	
	/**
	 * searches the database for possible problems and attempts to fix them
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		print_r("<pre>");
	    print_r("<h4>(Some Conditions can NOT be repaired by this Procedure!)</h4><br>");
		print_r("<pre>");
	// upgrade sectionref's that have lost their originals
		print_r("<b>Searching for sectionrefs that have lost their originals</b><br><br>");
		$sectionrefs = $db->selectObjects('sectionref',"is_original=0");
		if (count($sectionrefs)) {
			print_r("Found: ".count($sectionrefs)." copies (not originals)<br>");
		} else {
			print_r(" - None Found: Good!<br>");
		}
		foreach ($sectionrefs as $sectionref) {
			if ($db->selectObject('sectionref',"module='".$sectionref->module."' AND source='".$sectionref->source."' AND is_original='1'") == null) {
			// There is no original for this sectionref so change it to the original
				$sectionref->is_original = 1;
				$db->updateObject($sectionref,"sectionref");
				print_r("Fixed: ".$sectionref->module." - ".$sectionref->source."<br>");
			}
		}
		print_r("</pre>");

		print_r("<pre>");
	// upgrade sectionref's that point to missing sections (pages)
		print_r("<b>Searching for sectionrefs pointing to missing sections/pages <br>to fix for the Recycle Bin</b><br><br>");
		$sectionrefs = $db->selectObjects('sectionref',"refcount!=0");
		$found = 0;
		foreach ($sectionrefs as $sectionref) {
			if ($db->selectObject('section',"id='".$sectionref->section."'") == null) {
			// There is no section/page for sectionref so change the refcount
				$sectionref->refcount = 0;
				$db->updateObject($sectionref,"sectionref");
				print_r("Fixed: ".$sectionref->module." - ".$sectionref->source."<br>");
				$found += 1;
			}
		}
		if (!$found) {
			print_r(" - None Found: Good!<br>");
		}
		print_r("</pre>");

// FIXME Not needed when locationrefs are removed
//		 print_r("<pre>");
//	 // add missing locationref's based on existing sectionref's
//		 print_r("<b>Searching for detached modules with no original (no matching locationref)</b><br><br>");
//		 $sectionrefs = $db->selectObjects('sectionref',1);
//		 $found = 0;
//		 foreach ($sectionrefs as $sectionref) {
//			 if ($db->selectObject('locationref',"module='".$sectionref->module."' AND source='".$sectionref->source."'") == null) {
//			 // There is no locationref for sectionref.  Populate reference
//				 $newLocRef = null;
//				 $newLocRef->module   = $sectionref->module;
//				 $newLocRef->source   = $sectionref->source;
//				 $newLocRef->internal = $sectionref->internal;
//				 $newLocRef->refcount = $sectionref->refcount;
//				 $db->insertObject($newLocRef,'locationref');
//				 print_r("Copied: ".$sectionref->module." - ".$sectionref->source."<br>");
//				 $found += 1;
//			 }
//		 }
//		 if (!$found) {
//			 print_r(" - None Found: Good!<br>");
//		 }
//		 print_r("</pre>");

		 print_r("<pre>");
	 // delete sectionref's & locationref's that have empty sources since they are dead
		 print_r("<b>Searching for unassigned modules (no source)</b><br><br>");
		 $sectionrefs = $db->selectObjects('sectionref','source=""');
		 if ($sectionrefs != null) {
			 print_r("Removing: ".count($sectionrefs)." empty sectionref's (no source)<br>");
			 $db->delete('sectionref','source=""');
		 } else {
			 print_r(" - No Empties Found: Good!<br>");
		 }
// FIXME Not needed when locationrefs are removed
		 $locationrefs = $db->selectObjects('locationref','source=""');
		 if ($locationrefs != null) {
			 print_r("Removing: ".count($locationrefs)." empty locationref's (no source)<br>");
			 $db->delete('locationref','source=""');
		 } else {
			 print_r(" - No Empties Found: Good!<br>");
		 }
		 print_r("</pre>");

		print_r("<pre>");
	// add missing sectionrefs based on existing containers (fixes aggregation problem)
		print_r("<b>Searching for missing sectionref's based on existing container's</b><br><br>");
		$containers = $db->selectObjects('container',1);
		foreach ($containers as $container) {
			$iloc = expUnserialize($container->internal);
			if ($db->selectObject('sectionref',"module='".$iloc->mod."' AND source='".$iloc->src."'") == null) {
			// There is no sectionref for this container.  Populate sectionref
				$newSecRef = null;
				$newSecRef->module   = $iloc->mod;
				$newSecRef->source   = $iloc->src;
				$newSecRef->internal = '';
				$newSecRef->refcount = 1;
				$newSecRef->is_original = 1;
				if ($container->external != "N;") {
					$eloc = expUnserialize($container->external);
					$section = $db->selectObject('sectionref',"module='containermodule' AND source='".$eloc->src."'");
					if (!empty($section)) {
						$newSecRef->section = $section->id;
						$db->insertObject($newSecRef,"sectionref");
						print_r("Missing sectionref for container replaced: ".$iloc->mod." - ".$iloc->src." - PageID #".$section->id."<br>");
					} else {
						print_r("Cant' find the container page for container: ".$iloc->mod." - ".$iloc->src."<br>");
					}
				}
			}
		}
		print_r("</pre>");
	}
}

?>
