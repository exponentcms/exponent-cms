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

"Source" : ["Source", "external/editors/images/FCKeditor/toolbar/source.gif", ""],
"DocProps" : ["Document Properties", "external/editors/images/FCKeditor/toolbar/docprops.gif", ""],
"Save" : ["Save", "external/editors/images/FCKeditor/toolbar/save.gif", ""],
"NewPage" : ["New Page", "external/editors/images/FCKeditor/toolbar/newpage.gif", ""],
"Preview" : ["Preview", "external/editors/images/FCKeditor/toolbar/preview.gif", ""],
"Templates" : ["Templates", "external/editors/images/FCKeditor/toolbar/templates.gif", ""],
"About" : ["About FCKEditor", "external/editors/images/FCKeditor/toolbar/about.gif", ""],

"Cut" : ["Cut", "external/editors/images/FCKeditor/toolbar/cut.gif", ""],
"Copy" : ["Copy", "external/editors/images/FCKeditor/toolbar/copy.gif", ""],
"Paste" : ["Paste", "external/editors/images/FCKeditor/toolbar/paste.gif", ""],
"PasteText" : ["Paste as plain text", "external/editors/images/FCKeditor/toolbar/pastetext.gif", ""],
"PasteWord" : ["Paste from Word", "external/editors/images/FCKeditor/toolbar/pasteword.gif", ""],
"Print" : ["Print", "external/editors/images/FCKeditor/toolbar/print.gif", ""],
"SpellCheck" : ["Check Spelling", "external/editors/images/FCKeditor/toolbar/spellcheck.gif", ""],
"Undo" : ["Undo", "external/editors/images/FCKeditor/toolbar/undo.gif", ""],
"Redo" : ["Redo", "external/editors/images/FCKeditor/toolbar/redo.gif", ""],
"SelectAll" : ["Select All", "external/editors/images/FCKeditor/toolbar/selectall.gif", ""],
"RemoveFormat" : ["Remove Format", "external/editors/images/FCKeditor/toolbar/removeformat.gif", ""],
//Missing image... #66 in strip
//"FitWindow" : ["Fit Window", "external/editors/images/FCKeditor/toolbar/fitwindow.gif", ""],

"Bold" : ["Bold", "external/editors/images/FCKeditor/toolbar/bold.gif", ""],
"Italic" : ["Italic", "external/editors/images/FCKeditor/toolbar/italic.gif", ""],
"Underline" : ["Underline", "external/editors/images/FCKeditor/toolbar/underline.gif", ""],
"StrikeThrough" : ["Strike Through", "external/editors/images/FCKeditor/toolbar/strikethrough.gif", ""],
"Subscript" : ["Subscript", "external/editors/images/FCKeditor/toolbar/subscript.gif", ""],
"Superscript" : ["Superscript", "external/editors/images/FCKeditor/toolbar/superscript.gif", ""],

"OrderedList" : ["Insert/Remove Numbered List", "external/editors/images/FCKeditor/toolbar/insertorderedlist.gif", ""],
"UnorderedList" : ["Insert/Remove Bulleted List", "external/editors/images/FCKeditor/toolbar/insertunorderedlist.gif", ""],
//"orderedlist" : ["Ordered List", "external/editors/images/FCKeditor/toolbar/numberedlist.gif", ""],
//"unorderedlist" : ["Bulleted List", "external/editors/images/FCKeditor/toolbar/unorderedlist.gif", ""],
//"bulletedlist" : ["Bulleted List", "external/editors/images/FCKeditor/toolbar/bulletedlist.gif", ""],
"Outdent" : ["Decrease Indent", "external/editors/images/FCKeditor/toolbar/outdent.gif", ""],
"Indent" : ["Increase Indent", "external/editors/images/FCKeditor/toolbar/indent.gif", ""],

"Link" : ["Insert/Edit Link", "external/editors/images/FCKeditor/toolbar/link.gif", ""],
"Unlink" : ["Remove Link", "external/editors/images/FCKeditor/toolbar/unlink.gif", ""],
"Anchor" : ["Insert/Edit Anchor", "external/editors/images/FCKeditor/toolbar/anchor.gif", ""],

"Image" : ["Image", "external/editors/images/FCKeditor/toolbar/image.gif", ""],
"Flash" : ["Insert/Edit Flash", "external/editors/images/FCKeditor/toolbar/flash.gif", ""],
"Table" : ["Insert/Edit Table", "external/editors/images/FCKeditor/toolbar/table.gif", ""],
"SpecialChar" : ["Insert Special Character", "external/editors/images/FCKeditor/toolbar/specialchar.gif", ""],
"Smiley" : ["Insert Smiley", "external/editors/images/FCKeditor/toolbar/smiley.gif", ""],
"PageBreak" : ["Insert Page Break", "external/editors/images/FCKeditor/toolbar/pagebreak.gif", ""],
//"UniversalKey" : ["Universal Keyboard", "external/editors/images/FCKeditor/toolbar/universalkey.gif", ""],

"Rule" : ["Insert Horizontal Line", "external/editors/images/FCKeditor/toolbar/inserthorizontalrule.gif", ""],

"JustifyLeft" : ["Left Justify", "external/editors/images/FCKeditor/toolbar/justifyleft.gif", ""],
"JustifyCenter" : ["Center Justify", "external/editors/images/FCKeditor/toolbar/justifycenter.gif", ""],
"JustifyRight" : ["Right Justify", "external/editors/images/FCKeditor/toolbar/justifyright.gif", ""],
"JustifyFull" : ["Block Justify", "external/editors/images/FCKeditor/toolbar/justifyfull.gif", ""],
"Blockquote" : ["Blockquote", "external/editors/images/FCKeditor/toolbar/blockquote.gif", ""],

//STYLE
//FONTNAME
//FONTSIZE
//FONTFORMAT

"TextColor" : ["Text Color", "external/editors/images/FCKeditor/toolbar/textcolor.gif", ""],
"BGColor" : ["Background Color", "external/editors/images/FCKeditor/toolbar/bgcolor.gif", ""],

"Find" : ["Find", "external/editors/images/FCKeditor/toolbar/find.gif", ""],
"Replace" : ["Replace", "external/editors/images/FCKeditor/toolbar/replace.gif", ""],

"Form" : ["Form", "external/editors/images/FCKeditor/toolbar/form.gif", ""],
"Checkbox" : ["Checkbox", "external/editors/images/FCKeditor/toolbar/checkbox.gif", ""],
"Radio" : ["Radio Button", "external/editors/images/FCKeditor/toolbar/radio.gif", ""],
"TextField" : ["Text Field", "external/editors/images/FCKeditor/toolbar/textfield.gif", ""],
"Textarea" : ["Text Area", "external/editors/images/FCKeditor/toolbar/textarea.gif", ""],
"HiddenField" : ["Hidden Field", "external/editors/images/FCKeditor/toolbar/hiddenfield.gif", ""],
"Button" : ["Button", "external/editors/images/FCKeditor/toolbar/button.gif", ""],
"Select" : ["Select Field", "external/editors/images/FCKeditor/toolbar/select.gif", ""],
"ImageButton" : ["Image Button", "external/editors/images/FCKeditor/toolbar/imagebutton.gif", ""],

"FontName" : ["Font Family", "external/editors/images/FCKeditor/toolbar/fontfamily.gif", ""],
"FontSize" : ["Font Size", "external/editors/images/FCKeditor/toolbar/fontsize.gif", ""],
"FontFormat" : ["Format", "external/editors/images/FCKeditor/toolbar/format.gif", ""],
"Style" : ["Style", "external/editors/images/FCKeditor/toolbar/style.gif", ""],

// Extra image?
//"showdetails" : ["showdetails", "external/editors/images/FCKeditor/toolbar/showdetails.gif", ""],

//NOTE:THESE ARE ONLY AVAILABLE ON RIGHT CLICK ON A TABLE
//"ShowBorders" : ["Show Table Borders", "external/editors/images/FCKeditor/toolbar/showtableborders.gif", ""],
//"TableCell" : ["Cell", "external/editors/images/FCKeditor/toolbar/tablecell.gif", ""],
"TableDeleteCells" : ["Delete Cells", "external/editors/images/FCKeditor/toolbar/tabledeletecells.gif", ""],
"TableDeleteColumns" : ["Delete Columns", "external/editors/images/FCKeditor/toolbar/tabledeletecolumns.gif", ""],
"TableDeleteRows" : ["Delete Rows", "external/editors/images/FCKeditor/toolbar/tabledeleterows.gif", ""],
"TableInsertCell" : ["Insert Cell", "external/editors/images/FCKeditor/toolbar/tableinsertcell.gif", ""],
"TableInsertColumn" : ["Insert Column", "external/editors/images/FCKeditor/toolbar/tableinsertcolumn.gif", ""],
"TableInsertRow" : ["Insert Row", "external/editors/images/FCKeditor/toolbar/tableinsertrow.gif", "tablecommands"],
"TableMergeCells" : ["Merge Cells", "external/editors/images/FCKeditor/toolbar/tablemergecells.gif", ""],
"TableSplitCell" : ["Split Cell", "external/editors/images/FCKeditor/toolbar/tablesplitcell.gif", ""],
"FitWindow" : ["Split Cell", "external/editors/images/FCKeditor/toolbar/fillscreen.gif", ""]
};
