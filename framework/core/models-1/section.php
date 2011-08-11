<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

class section {
	/*
	 * Common Form helper method
	 *
	 * This method, intended to be used solely by other methods of the
	 * section class, creates a base form that all other page types can
	 * build off of.  This form includes a name textbox, and either a rank
	 * meta field (hidden input) or a rank dropdown.
	 */
	function _commonForm(&$object) {
		// Create a new blank form.
		$form = new form();
		
		if (!isset($object->id)) {
			// This is a new section, so we need to set up some defaults.
			$object->name = '';
			$object->sef_name = '';
			$object->active = 1;
			$object->public = 1;
			$object->secured = 0;			
			$object->new_window = 0;
			$object->subtheme = '';
			
			$object->page_title = SITE_TITLE;
			$object->keywords = SITE_KEYWORDS;
			$object->description = SITE_DESCRIPTION;
			
			if (!isset($object->parent)) {
				// This is another precaution.  The parent attribute
				// should ALWAYS be set by the caller.
				//FJD - if that's the case, then we should die.
				die(SITE_403_REAL_HTML);
				//$object->parent = 0;
			}
		} else {
			// If we are editing the section, we should store the section's id
			// in a hidden value, so that it comes through when the form is
			// submitted.
			$form->meta('id',$object->id);
		}
		
		// The name of the section, as it will be linked in the section hierarchy.
		$form->register('name',gt('name'),new textcontrol($object->name));
		$form->register('sef_name',gt('SEF Name').'<p class="sefinfo">If you don\'t put in an SEF Name one will be generated based on the title provided. SEF names can only contain alpha-numeric characters, hyphens and underscores.</p>',new textcontrol($object->sef_name));
		
		if (!isset($object->id)) {
			// This is a new section, so we can add the positional dropdown
			// Pull the database object in from the global scope.
			global $db;
			// Retrieve all of the sections that are siblings of the new section
			$sections = $db->selectObjects('section','parent='.$object->parent);
			
			if (count($sections) && $object->parent >= 0) {
				// Initialize the sorting subsystem so that we can order the sections
				// by rank, ascending, and get the proper ordering.
//				usort($sections,'exponent_sorting_byRankAscending');
				$sections = expSorter::sort(array('array'=>$sections,'sortby'=>'rank', 'order'=>'ASC'));

				// Generate the Position dropdown array.
				$positions = array(gt('At the Top'));
				foreach ($sections as $section) {
					$positions[] = sprintf(gt('After "%s"'),$section->name);
				}
    			$form->meta('rank',count($positions)-1);
				//$form->register('rank',gt('Rank'),new dropdowncontrol(count($positions)-1,$positions));
			} else {
				// If there are no siblings, the new section gets the first
				// slot, with a rank of 0.
				$form->meta('rank',0);
			}
			// Store the section's parent in a hidden field, so that it comes through
			// when the form is submitted.
			$form->meta('parent',$object->parent);
		} else if ($object->parent >= 0) {
			// Allow them to change parents, but not if the section is outside of the hiearchy (parent > 0)
			$form->register('parent',gt('Parent Page'),new dropdowncontrol($object->parent,navigationmodule::levelDropdownControlArray(0,0,array($object->id),1)));
		}
		$form->register('new_window',gt('Open in New Window'),new checkboxcontrol($object->new_window,false));
		
		// Return the form to the calling scope, which should always be a
		// member method of this class.
		return $form;
	}
	
	static function moveStandaloneForm($object = null) {
		// Initialize the forms subsystem for use.
		require_once(BASE.'framework/core/subsystems-1/forms.php');
		$form = section::_commonForm($object);
		// the name and sef_name are already set in the stand-alone page
		$form->unregister('name');
		$form->unregister('sef_name');
		
		global $db;
		$standalones = array();
		foreach ($db->selectObjects('section','parent = -1') as $s) {
			$standalones[$s->id] = $s->name;
		}

		$form->register('page',gt('Standalone Page'),new dropdowncontrol(0,$standalones));
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		return $form;
	}

	/*
	 * Content Page Form method
	 *
	 * This method returns a Form object to be used when allowing the user
	 * to create a new normal Content Page or edit an existing one.
	 *
	 * @param Object $object The section object to build the form from.
	 *
	 * @return Form A form object that can be used to create a new section, or
	 *    edit an existing one.
	 */
	static function form($object = null) {
		// Initialize the forms subsystem for use.
		require_once(BASE.'framework/core/subsystems-1/forms.php');

		// Grab the basic form that all page types share
		// This has the name and positional dropdowns registered.
		// This call also initializes the section object, if it is not an existing section.
		$form = section::_commonForm($object);
		
		// Register the 'Active?' and 'Public?' checkboxes.
		$form->register('active',gt('Active'),new checkboxcontrol($object->active));
		$form->register('public',gt('Public'),new checkboxcontrol($object->public));

		// Register the sub themes dropdown.
		$form->register('subtheme',gt('Theme Variation'),new dropdowncontrol($object->subtheme,expTheme::getSubThemes()));

		// Register the 'Secured?' checkboxes for SSL pages
		if(ENABLE_SSL) {
			$form->register('secured',"Secured?",new checkboxcontrol($object->secured));		
		}		

		$form->register(null,'',new htmlcontrol('<h2>SEO Information</h2>'));

		// Register the Page Meta Data controls.
		$form->register('page_title',gt('Page Title'),new textcontrol($object->page_title));
		$form->register('keywords',gt('Keywords'),new texteditorcontrol($object->keywords,5));
		$form->register('description',gt('Page Description'),new texteditorcontrol($object->description,5));
		
		// Add a Submit / Cancel button.
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		
		// Return the form to the calling scope (usually an action in the navigation module).
		return $form;
	}
	
	/*
	 * External Alias Form method
	 *
	 * This method returns a form object to be used when allowing the user
	 * to create a new section that is actually a link to a website outside of the
	 * Exponent-managed site.
	 *
	 * @param Object $object The section object to build the form from.
	 *
	 * @return Form A form object that can be used to create a new section, or
	 *    edit an existing one.
	 */
	static function externalAliasForm($object = null) {
		// Initialize the forms subsystem for use.
		require_once(BASE.'framework/core/subsystems-1/forms.php');

		// Grab the basic form that all page types share
		// This has the name and positional dropdowns registered.
		// This call also initializes the section object, if it is not an existing section.
		$form = section::_commonForm($object);
		// do we need an sef_name for an external page?
		//$form->unregister('sef_name');
		
		if (!isset($object->external_link)) $object->external_link = '';
		// Add a textbox the user can enter the external website's URL into.
		$form->register('external_link',gt('Page'),new textcontrol($object->external_link));
		
		// Add the'Public?' checkbox.  The 'Active?' checkbox is omitted, because it makes no sense.
		$form->register('public',gt('Public'),new checkboxcontrol($object->public));
		
		// Add a Submit / Cancel button.
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		
		// Return the form to the calling scope (usually an action in the navigation module).
		return $form;
	}
	
	/*
	 * Internal Alias Form method
	 *
	 * This method returns a form object to be used when allowing the user
	 * to create a new section that is actually a link to another page in the
	 * Exponent site hierarchy.
	 *
	 * @param Object $object The section object to build the form from.
	 *
	 * @return Form A form object that can be used to create a new section, or
	 *    edit an existing one.
	 */
	static function internalAliasForm($object = null) {
		// Initialize the forms subsystem for use.
		require_once(BASE.'framework/core/subsystems-1/forms.php');

		// Initialization
		if (!isset($object->id)) {
			$object->internal_id = 0;
		}
		
		// Grab the basic form that all page types share
		// This has the name and positional dropdowns registered.
		// This call also initializes the section object, if it is not an existing section.
		$form = section::_commonForm($object);
		// the sef_name is already set in this existing page
		$form->unregister('sef_name');
		
		// Add a dropdown to allow the user to choose an internal page.
		$form->register('internal_id',gt('Page'),new dropdowncontrol($object->internal_id,navigationmodule::levelDropDownControlArray(0,0)));
		
		// Add the'Public?' checkbox.  The 'Active?' checkbox is omitted, because it makes no sense.
		$form->register('public',gt('Public'),new checkboxcontrol($object->public));
		
		// Add a Submit / Cancel button.
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		
		// Return the form to the calling scope (usually an action in the navigation module).
		return $form;
	}
	
	/*
	 * Pageset Form method
	 *
	 * This method returns a form object to be used when allowing the user
	 * to create a new section using a user-defined Pageset.
	 *
	 * @param Object $object The section object to build the form from.
	 *
	 * @return Form A form object that can be used to create a new section, or
	 *    edit an existing one.
	 */
	static function pagesetForm($object = null) {
		// Initialize the forms subsystem for use.
		require_once(BASE.'framework/core/subsystems-1/forms.php');

		// Grab the basic form that all page types share
		// This has the name and positional dropdowns registered.
		// This call also initializes the section object, if it is not an existing section.
		$form = section::_commonForm($object);
		
		// Add a dropdown to allow the user to choose which pageset they want.
		// Pull the database object in from the global scope.
		global $db;
		// A holding array, which will become the source of the dropdown
		$pagesets = array();
		foreach ($db->selectObjects('section_template','parent=0') as $pageset) {
			// Grab each pageset and store its name and id.  The id will be used when updating.
			$pagesets[$pageset->id] = $pageset->name;
		}
		$form->register('pageset',gt('Pageset'),new dropdowncontrol(0,$pagesets));
		
		// Add the'Public?' checkbox.  The 'Active?' checkbox is omitted, because it makes no sense.
		$form->register('public',gt('Public'),new checkboxcontrol($object->public));
		
		// Add a Submit / Cancel button.
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		
		// Return the form to the calling scope (usually an action in the navigation module).
		return $form;
	}
	
	// Update methods
	
	/*
	 * Update Object helper method
	 *
	 * This method is a complement to _commonForm, and updates the name
	 * and rank of the passed object using the passed values.
	 *
	 * @param array $values The data received from the form submission
	 * @param object $object The section object to update
	 *
	 * @return object The updated section object.
	 */
	function _updateCommon($values,$object) {
		$object->name = $values['name'];
		if (isset($values['sef_name'])) $object->sef_name = $values['sef_name'];
		if (isset($values['rank'])) $object->rank = $values['rank'];
		if (isset($values['parent'])) $object->parent = $values['parent'];
		$object->new_window = (isset($values['new_window']) ? 1 : 0);
		return $object;
	}
	
	/*
	 * Content Page Update method
	 *
	 * This method updates the passed section object's attributes using
	 * the passed values.
	 *
	 * @param array $values The data received from the form submission
	 * @param object $object The section object to update
	 *
	 * @return object The updated section object.
	 */
	static function update($values,$object) {
		$object = section::_updateCommon($values,$object);
		$object->subtheme = $values['subtheme'];
		$object->active = (isset($values['active']) ? 1 : 0);
		$object->public = (isset($values['public']) ? 1 : 0);
		$object->secured = (isset($values['secured']) ? 1 : 0);		
		$object->page_title = ($values['page_title'] != SITE_TITLE ? $values['page_title'] : "");
		$object->keywords = ($values['keywords'] != SITE_KEYWORDS ? $values['keywords'] : "");
		$object->description = ($values['description'] != SITE_DESCRIPTION ? $values['description'] : "");
		return $object;
	}
	
	/*
	 * External Alias Update method
	 *
	 * This method updates the passed section object's attributes using
	 * the passed values.
	 *
	 * @param array $values The data received from the form submission
	 * @param object $object The section object to update
	 *
	 * @return object The updated section object.
	 */
	static function updateExternalAlias($values,$object) {
		$object = section::_updateCommon($values,$object);
		
		$object->active = 1;
		$object->public = (isset($values['public']) ? 1 : 0);
		
		$object->alias_type = 1;
		$object->external_link = $values['external_link'];
		if (!exponent_core_URLisValid($object->external_link)) {
			$object->external_link = 'http://' . $object->external_link;
		}
		return $object;
	}
	
	/*
	 * Internal Alias Update method
	 *
	 * This method updates the passed section object's attributes using
	 * the passed values.
	 *
	 * @param array $values The data received from the form submission
	 * @param object $object The section object to update
	 *
	 * @return object The updated section object.
	 */
	static function updateInternalAlias($values,$object) {
		$object = section::_updateCommon($values,$object);
		
		$object->active = 1;
		$object->public = (isset($values['public']) ? 1 : 0);
		
		$object->alias_type = 2;
		global $db;
		// We need to make sure we don't point to another link
		$section = $db->selectObject('section','id='.$values['internal_id']);
		while ($section->alias_type == 2) {
			// Find what it is pointing to.
			$section = $db->selectObject('section','id='.$section->internal_id);
		}
		// Pull the destination section's id into the internal_id field.  This works because
		// if the while loop didn't execute, we had a 'normal' page to begin with.  This check
		// doesn't guard against pointing an internal link to a section that is set up to
		// an external link -- that check will need to be done in the navigation module itself.
		$object->internal_id = $section->id;
		// Set the active state of the new section from the linked section.  The caller is
		// expected to catch this if a link to an inactive section is made, and that behaviour
		// is undesired.
		$object->active = $section->active;
		// Set the sef_name the new section from the linked section.
		$object->sef_name = $section->sef_name;
		return $object;
	}
	
	/*
	 * Pageset Update method
	 *
	 * This method updates the passed section object's attributes using
	 * the passed values.
	 *
	 * @param array $values The data received from the form submission
	 * @param object $object The section object to update
	 *
	 * @return object The updated section object.
	 */
	static function updatePageset($values,$object) {
		$object = section::_updateCommon($values,$object);
		
		$object->active = 1;
		$object->public = (isset($values['public']) ? 1 : 0);
		
		// Can't really do much with pageset updating, because we
		// need to save the section before we can add subsections or copy
		// any content.
		return $object;
	}
	
	// The following are helper functions for dealing with the Section datatype.
	
	/*
	 * Determine Section Depth
	 *
	 * This method looks at a section ID, and figures out how deep in the
	 * site hierarchy it is, and returns that number.  A top-level section has a
	 * depth of 0, it's children all have a depth of 1, and so on.
	 *
	 * @param integer $id The id of the section to find the depth count for.
	 *
	 * @return integer The depth of the section.
	 */
	function depth($id) {
		// To calculate a section's depth, we query its parents
		// until we find a parent with no parent (a top-level section).
		// The number of parents is the depth of the section.  For
		// instance, a top-level section has no parents and a depth
		// of 0.
	
		// Pull in the database object form the global scope.
		global $db;
		
		// Start out at depth 0.  The while loop will not execute if
		// the passed $id was that of a top-level section, so $depth
		// will still be set properly.
		$depth = 0;
		// Grab the section we were passed.
		$s = $db->selectObject("section","id=$id");
		while ($s->parent != 0) {
			// Section still has a parent.  Increment the depth counter.
			$depth++;
			// Get the section's parent's parent (grandparent)
			$s = $db->selectObject("section","id=".$s->parent);
		}
		return $depth;
	}
	
	static function changeParent($section,$old_parent,$new_parent) {
		global $db;
		// Store new parent.
		$section->parent = $new_parent;
		
		$db->decrement('section','rank',1,'parent='.$old_parent . ' AND rank > ' . $section->rank);
		// Need to place this item at the end of the list of children for the new parent.
		$section->rank = $db->max('section','rank','parent','parent='.$new_parent);
		if ($section->rank === null) {
			$section->rank = 0;
		} else {
			$section->rank++;
		}
		
		return $section;
	}

	public static function isValidName($name=null) {
		if (empty($name)) return false;

		$match = array();
		$pattern = "/([^0-9a-z-_\+\.])/i";
		if (preg_match($pattern, $name, $match, PREG_OFFSET_CAPTURE)) {
			return false;
		} else {
			return true;
		}
	}

	public static function isDuplicateName($section=null) {
		if (empty($section)) return false;
		global $db;
		if (is_object($section)) {
			if (!empty($section->id)) {
				$res = $db->selectValue('section', 'id', 'id != '.$section->id.' AND sef_name="'.$section->sef_name.'"');
			} else {
				$res = $db->selectValue('section', 'id', 'sef_name="'.$section->sef_name.'"');
			}
		} elseif(is_string($section)) {
			$res = $db->selectValue('section', 'id', 'sef_name="'.$section.'"');
		}

		return empty($res) ? false : true;
	}
}

?>
