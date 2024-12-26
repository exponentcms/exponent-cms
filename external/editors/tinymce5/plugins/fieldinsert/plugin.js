/*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

/**
 * plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */

tinymce.PluginManager.add('fieldinsert', function(editor) {
    'use strict';

    var items = [];

    tinymce.each(editor.settings.fieldinsert_list || [
        '', 'No Fields', 'There are no fields'
    ], function (field) {
        items.push({
            type: 'menuitem',
            text: field[1],
            onAction: function () {
                editor.insertContent(field[0]);
            }
        });
    });


    // Add a menu button to the toolbar
    editor.ui.registry.addMenuButton('fieldinsert', {
        text: 'Insert Field',
        icon: 'template',
		title: 'Insert field',
        fetch: function (callback) {
            callback(items);
        }
	});

    // Adds a menu item to the insert menu
    editor.ui.registry.addNestedMenuItem('fieldinsert', {
		icon: 'template',
		text: 'Insert field',
        context: 'insert',
        getSubmenuItems: () => {
            return items;
        }
	});
});
