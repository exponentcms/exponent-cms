/*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

CKEDITOR.plugins.add('fieldinsert' ,
    {
        requires : ['richcombo'] ,
        init : function (editor) {
            //  array of fields to choose from that'll be inserted into the editor
            var fields = editor.config.fieldinsert_list;

            // add the menu to the editor
            editor.ui.addRichCombo('fieldinsert' ,
                {
                    label : 'Fields' ,
                    title : 'Fields' ,
                    voiceLabel : 'Fields' ,
                    className : 'cke_format' ,
                    multiSelect : false ,
                    panel : {
                        css : [ editor.config.contentsCss, CKEDITOR.skin.getPath('editor') ] ,
                        voiceLabel : editor.lang.panelVoiceLabel
                    } ,

                    init : function () {
                        this.startGroup("Fields");
                        //this.add('value', 'drop_text', 'drop_label');
                        for (var i in fields) {
                            this.add(escape(fields[i][0]) , fields[i][1] , fields[i][2]);
                        }
                    } ,

                    onClick : function (value) {
                        editor.focus();
                        editor.fire('saveSnapshot');
                        editor.insertHtml(unescape(value));
                        editor.fire('saveSnapshot');
                    }
                });
        }
    });

/**
 * List of fields available to insert.
 *
 * fieldinsert_list = array of fields; 'value', 'drop_text', 'drop_label'
 *
 * @member CKEDITOR.config
 */
CKEDITOR.config.fieldinsert_list = [
    ['', 'No Fields', 'There are no fields']
];
