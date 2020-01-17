/*
 * Copyright (c) 2004-2020 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.allowedContent = true;
    config.protectedSource.push( /<i class[\s\S]*?\>/g );  // allow <i> opening tag
    config.protectedSource.push( /<\/i>/g );  // allow </i> closing tag

    CKEDITOR.dtd.$removeEmpty['span'] = false;  // allow empty <span> tags for font awesome
    CKEDITOR.dtd.$removeEmpty['i'] = false;  // allow empty <i> tags for font awesome
};
