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

class upgrade_attachableitem_tables extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '1.99.4'; 

	static function name() { return "Upgrade the tables for attachable items."; }

	function upgrade() {
		global $db;
		
		// FIX THE TAGS TABLE
		$db->sql('DROP INDEX subtype ON '.DB_TABLE_PREFIX.'_content_expTags');
        $db->sql('ALTER TABLE '.DB_TABLE_PREFIX.'_content_expTags DROP PRIMARY KEY');
		$db->sql('ALTER TABLE '.DB_TABLE_PREFIX.'_content_expTags ADD PRIMARY KEY (exptags_id, content_id, content_type(15), subtype(15))');
		
		// FIX THE COMMENTS TABLE
		$db->sql('DROP INDEX subtype ON '.DB_TABLE_PREFIX.'_content_expComments');
		$db->sql('ALTER TABLE '.DB_TABLE_PREFIX.'_content_expComments DROP PRIMARY KEY');
		$db->sql('ALTER TABLE '.DB_TABLE_PREFIX.'_content_expComments ADD PRIMARY KEY (expcomments_id, content_id, content_type(15), subtype(15))');
		
		// FIX THE FILES TABLE
		$db->sql('DROP INDEX subtype ON '.DB_TABLE_PREFIX.'_content_expFiles');
		$db->sql('ALTER TABLE '.DB_TABLE_PREFIX.'_content_expFiles DROP PRIMARY KEY');
		$db->sql('ALTER TABLE '.DB_TABLE_PREFIX.'_content_expFiles ADD PRIMARY KEY (expfiles_id, content_id, content_type(15), subtype(15))');
	    
	    return "Complete";
	}
}

?>
