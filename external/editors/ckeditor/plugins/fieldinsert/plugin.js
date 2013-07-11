/**
 * @license Copyright © 2013 Stuart Sillitoe <stuart@vericode.co.uk>
 * This work is mine, and yours. You can modify it as you wish.
 *
 * Stuart Sillitoe
 * stuartsillitoe.co.uk
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
                    voiceLabel : 'ZFields' ,
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
 * config.fieldinsert_list = array of fields;
 *  'value', 'drop_text', 'drop_label'
 *
 * @member CKEDITOR.config
 */
CKEDITOR.config.fieldinsert_list = [
    ['@@FAQ::displayList()4@@', 'FAQs5', 'FAQs6'],
    ['@@Glossary::displayList()@@', 'Glossary', 'Glossary'],
    ['@@CareerCourse::displayList()@@', 'Career Courses', 'Career Courses'],
    ['@@CareerProfile::displayList()@@', 'Career Profiles', 'Career Profiles']
];
