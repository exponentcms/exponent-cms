<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * @subpackage Controllers
 * @package Modules
 */

class textController extends expController {
	public $useractions = array(
        'showall'=>'Show all',
        'showRandom'=>'Show Random Text',
	);
	public $remove_configs = array(
        'categories',
		'comments',
        'ealerts',
        'facebook',
        'pagination',
        'rss',
		'tags',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Text"); }
    static function description() { return gt("Places text on your web pages"); }

	public function showall() {
        global $user;

	    expHistory::set('viewable', $this->params);
		$where = $this->aggregateWhereClause();
		$order = 'rank ASC';
		$items = $this->text->find('all', $where, $order);

        // now the stuff for the inline editing w/ ckeditor v4
        $level = 99;
        if (expSession::is_set('uilevel')) {
        	$level = expSession::get('uilevel');
        }
        $settings = expHTMLEditorController::getActiveEditorSettings(SITE_WYSIWYG_EDITOR);
        if (empty($settings->name)) $settings = new stdClass();
        if (SITE_WYSIWYG_EDITOR == 'ckeditor') {
    //        if (empty($settings->data)) {
    //            $settings->data = "
    //                ['htmlsource','Source','-','Preview','-','Templates'],
    //                ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print','SpellChecker','Scayt'],
    //                ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    //                ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'],
    //                '/',
    //                ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
    //                ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
    //                ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    //                ['Link','Unlink','Anchor'],
    //                '/',
    //                ['Styles','Format','Font','FontSize'],
    //                ['TextColor','BGColor'],
    //                ['Maximize', 'ShowBlocks','-','About']";
    //        }
            if (empty($settings->skin)) $settings->skin = 'kama';
            if (empty($settings->scayt_on)) $settings->scayt_on = 'true';
            if (empty($settings->paste_word)) {
                $settings->paste_word = 'forcePasteAsPlainText : true,';
            } else {
                $settings->paste_word = '';
            }
            // clean up (custom) plugins list from missing plugins
            if (!empty($settings->plugins)) {
                $plugs = explode(',',trim($settings->plugins));
                foreach ($plugs as $key=>$plug) {
                    if (empty($plug) || !is_dir(BASE . 'external/editors/ckeditor/plugins/' . $plug)) unset($plugs[$key]);
                }
                $settings->plugins = implode(',',$plugs);
            }
            if (!empty($settings->fontnames)) {
                $settings->fontnames  = stripSlashes($settings->fontnames);
            } else {
                $settings->fontnames = "'Arial/Arial, Helvetica, sans-serif;' +
                    'Comic Sans MS/Comic Sans MS, cursive;' +
                    'Courier New/Courier New, Courier, monospace;' +
                    'Georgia/Georgia, serif;' +
                    'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
                    'Tahoma/Tahoma, Geneva, sans-serif;' +
                    'Times New Roman/Times New Roman, Times, serif;' +
                    'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;' +
                    'Verdana/Verdana, Geneva, sans-serif'";
            }
            if (!empty($settings->stylesset)) {
                $settings->stylesset  = stripSlashes($settings->stylesset);
            } else {
                $settings->stylesset = "'default'";
            }
            if (!empty($settings->formattags)) {
                $settings->formattags = stripSlashes($settings->formattags);
            } else {
                $settings->formattags = "'p;h1;h2;h3;h4;h5;h6;pre;address;div'";
            }
        } elseif (SITE_WYSIWYG_EDITOR == 'tinymce') {
            if (empty($settings->skin)) $settings->skin = 'lightgray';
            if (empty($settings->scayt_on)) $settings->scayt_on = 'false';
            // clean up (custom) plugins list from missing plugins  //FIXME we don't load any custom stuff in this view except skin & plugins
            if (!empty($settings->plugins)) {
                $plugs = explode(',',trim($settings->plugins));
                foreach ($plugs as $key=>$plug) {
                    if (empty($plug) || !is_dir(BASE . 'external/editors/tinymce/plugins/' . $plug)) unset($plugs[$key]);
                }
                $settings->plugins = implode(',',$plugs);
            }
            if (!empty($settings->fontnames)) {
                $settings->fontnames  = stripSlashes($settings->fontnames);
            } else {
                $settings->fontnames = "'Andale Mono=andale mono,times;'+
                    'Arial=arial,helvetica,sans-serif;'+
                    'Arial Black=arial black,avant garde;'+
                    'Book Antiqua=book antiqua,palatino;'+
                    'Comic Sans MS=comic sans ms,sans-serif;'+
                    'Courier New=courier new,courier;'+
                    'Georgia=georgia,palatino;'+
                    'Helvetica=helvetica;'+
                    'Impact=impact,chicago;'+
                    'Symbol=symbol;'+
                    'Tahoma=tahoma,arial,helvetica,sans-serif;'+
                    'Terminal=terminal,monaco;'+
                    'Times New Roman=times new roman,times;'+
                    'Trebuchet MS=trebuchet ms,geneva;'+
                    'Verdana=verdana,geneva;'+
                    'Webdings=webdings;'+
                    'Wingdings=wingdings,zapf dingbats'
            ";
            }
            if (!empty($settings->stylesset)) {
                $settings->stylesset  = stripSlashes($settings->stylesset);
            } else {
                $settings->stylesset = "{title: 'Inline', items: [
                        {title: 'Strikethrough', inline: 'span', styles : {textDecoration : 'line-through'}, icon: 'strikethrough'},
                        {title: 'Superscript', inline: 'sup', icon: 'superscript'},
                        {title: 'Subscript', inline: 'sub', icon: 'subscript'},
                        {title: 'Marker',			inline: 'mark'},
                        {title: 'Big',				inline: 'big'},
                        {title: 'Small',			inline: 'small'},
                        {title: 'Typewriter',		inline: 'tt'},
                        {title: 'Computer Code',	inline: 'code', icon: 'code'},
                        {title: 'Keyboard Phrase',	inline: 'kbd'},
                        {title: 'Sample Text',		inline: 'samp'},
                        {title: 'Variable',		inline: 'var'},
                        {title: 'Deleted Text',	inline: 'del'},
                        {title: 'Inserted Text',	inline: 'ins'},
                        {title: 'Cited Work',		inline: 'cite'},
                        {title: 'Inline Quotation', inline: 'q'},
                    ]},
                    {title: 'Containers', items: [
                        {title: 'section', block: 'section', wrapper: true, merge_siblings: false},
                        {title: 'article', block: 'article', wrapper: true, merge_siblings: false},
                        {title: 'blockquote', block: 'blockquote', wrapper: true},
                        {title: 'hgroup', block: 'hgroup', wrapper: true},
                        {title: 'aside', block: 'aside', wrapper: true},
                        {title: 'figure', block: 'figure', wrapper: true}
                    ]},
                    {title: 'Images', items: [
                        {title: 'Styled image (left)',
                            selector: 'img',
                            classes: 'img-left'
                        },
                        {title: 'Styled image (right)',
                            selector: 'img',
                            classes: 'img-right'
                        },
                        {title: 'Styled image (center)',
                            selector: 'img',
                            classes: 'img-center'
                        },
                    ]},
                ";
            }
            if (!empty($settings->formattags)) {
                $settings->formattags = stripSlashes($settings->formattags);
            } else {
                $settings->formattags = "'Normal=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Formatted=pre;Address=address;Normal (DIV)=div'";
            }
            if (!$user->globalPerm('prevent_uploads')) {
                $settings->upload = "plupload_basepath	: './plugins/quickupload',
                    upload_url			: '" . URL_FULL . "framework/modules/file/connector/uploader_tinymce.php',
                    upload_post_params	: {
                        action:'upload',
                        ajax_action:'1',
                        json:'1'
                    },
                    upload_file_size	: '5mb',
                    upload_callback		: function(res, file, up) {
                        if (res.status == 200) {
                            var response = JSON.parse(res.response);
                            return response.data;  //image path
                        } else {
                            return false;
                        }
                    },
                    upload_error		: function(err, up) {
                        console.log(err.status);
                        console.log(err.message);
                    },
                    images_upload_url: '" . URL_FULL . "framework/modules/file/connector/uploader_paste_tinymce.php',
                    paste_data_images: true,";
            } else {
                $settings->upload = '';
            }
        }

        //fixme we do NOT pass toolbars, nor custom plugins in toolbar
		assign_to_template(array(
            'items'=>$items,
            'preview'=>($level == UILEVEL_PREVIEW),  // needed for inline edit to work
            'editor'=>$settings,
        ));
	}
	
	public function showRandom() {
	    expHistory::set('viewable', $this->params);
		//This is a better way to do showRandom, you can pull in random text from all over the site (if aggregated) if you need to.
		$where = $this->aggregateWhereClause();
		$limit = isset($this->params['limit']) ? $this->params['limit'] : 1;
		$order = 'RAND()';
		assign_to_template(array(
            'items'=>$this->text->find('all', $where, $order, $limit)
        ));
	}
    
    public function update() {
        // update the record.
        $this->text->update($this->params);
        
        // update the search index since text is relegated to page content.
        $nav = new navigationController();
        $nav->addContentToSearch();

        // go back to where we came from.
        expHistory::back();
    }

    /**
     * function to update the text item object sent via ajax
     * we only have to deal with a title and body which can be edited by ckeditor4 inline
     */
    public function edit_item() {
        $text = new text($this->params['id']);  // get existing data
        if ($this->params['type'] != 'revert') {
            if ($this->params['id'] != 0) {
                $prop = $this->params['type'];
                $data = !empty($this->params['value']) ? $this->params['value'] : '';
                if ($prop == 'title') $data = trim(strip_tags($data));
                $text->$prop = $data;
            } else {
                $text->title = gt('title placeholder');
                $text->body = '<p>' . gt('content placeholder') . '</p>';
                $text->location_data = serialize(expCore::makeLocation('text',$this->params['src'],''));
            }
            $text->update();
            $text->refresh();  // need to get updated database info
        }
        $ar = new expAjaxReply(200, gt('The text item was saved'), json_encode($text));
        $ar->send();
    }

    /**
     * function to delete the text item object sent via ajax
     */
    public function delete_item() {
        if (!empty($this->params['id'])) {
            $text = new text($this->params['id']);
            $text->delete();
            $ar = new expAjaxReply(200, gt('The text item was deleted'), $text->id);
            $ar->send();
        }
    }

}

?>
