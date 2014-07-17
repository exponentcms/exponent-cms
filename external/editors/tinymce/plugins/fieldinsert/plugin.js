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
	var menuItems = [], lastFormat;

    // Add a button that inserts time
	editor.addButton('fieldinsert', {
		type: 'menubutton',
//        text: 'Field',
        icon: 'template',
		title: 'Insert field',
//		onclick: function() {
//            insertField(lastFormat || "%H:%M:%S");
//		},
		menu: menuItems
	});

	tinymce.each(editor.settings.fieldinsert_list || [
        '', 'No Fields', 'There are no fields'
	], function(field) {
		menuItems.push({
			text: field[1],
			onclick: function() {
//                lastFormat = field[0];
                editor.insertContent(field[0]);
			}
		});
	});

    // Adds a menu item to the insert menu
	editor.addMenuItem('fieldinsert', {
		icon: 'template',
		text: 'Insert field',
		menu: menuItems,
		context: 'insert'
	});
});
