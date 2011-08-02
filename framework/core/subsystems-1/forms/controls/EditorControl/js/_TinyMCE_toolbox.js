//##################################################
//#
//# Copyright (c) 2006-2007 Maxim Mueller
//#
//# This file is part of Exponent
//#
//# Exponent is free software; you can redistribute
//# it and/or modify it under the terms of the GNU
//# General Public License as published by the Free
//# Software Foundation; either version 2 of the
//# License, or (at your option) any later version.
//#
//# GPL: http://www.gnu.org/licenses/gpl.txt
//#
//##################################################

//this file provides an Array associating availiable Actions, their Icons, and, if required for this action, their plugins, with their internal ids
//TODO: determine whether the Editor provides a queryable API for that
//TODO: adjust for themes
//TODO: account for combined image files

// first = action name
// second = icon location
// third = required plugin
eXp.WYSIWYG.toolbox =	{
				"cut" : ["cut", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/cut.gif", ""],
				"paste" : ["paste", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/paste.gif", ""],
				"copy" : ["copy", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/copy.gif", ""],
				"bold" : ["bold", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/bold.gif", ""],
				"strikethrough" : ["strikethrough", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/strikethrough.gif", ""],
				"underline" : ["underline", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/underline.gif", ""],
				"anchor" : ["anchor", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/anchor.gif", ""],
				"bullist" : ["bullist", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/bullist.gif", ""],
				"backcolor" : ["backcolor", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/backcolor.gif", ""],
				"browse" : ["browse", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/browse.gif", ""],
				"charmap" : ["charmap", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/charmap.gif", ""],
				"cleanup" : ["cleanup", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/cleanup.gif", ""],
				"close" : ["close", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/close.gif", ""],
				"code" : ["code", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/code.gif", ""],
				"color" : ["color", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/color.gif", ""],
				"forecolor" : ["forecolor", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/forecolor.gif", ""],
				"help" : ["help", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/help.gif", ""],
				"hr" : ["hr", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/hr.gif", ""],
				"image" : ["image", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/image.gif", ""],
				"indent" : ["indent", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/indent.gif", ""],
				"italic" : ["italic", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/italic.gif", ""],
				"justifycenter" : ["justify center", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/justifycenter.gif", ""],
				"justifyfull" : ["justify full", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/justifyfull.gif", ""],
				"justifyleft" : ["justify left", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/justifyleft.gif", ""],
				"justifyright" : ["justify right", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/justifyright.gif", ""],
				"link" : ["link", "external/editors/TinyMCE/jscripts/tiny_mce/themes/advanced/images/link.gif", ""],
				"replace" : ["replace", "external/editors/TinyMCE/jscripts/tiny_mce/plugins/searchreplace/images/replace.gif", "searchreplace"],
				"search" : ["search", "external/editors/TinyMCE/jscripts/tiny_mce/plugins/searchreplace/images/search.gif", "searchreplace"]
			};