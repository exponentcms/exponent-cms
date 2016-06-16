{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
 *}

{uniqueid prepend="text" assign="name"}
{$inline = false}

<div id="textmodule-{$name}" class="module text showall showall-inline">
    <div id="textcontent-{$name}">
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
        {permissions}
            <div class="module-actions">
                {if $permissions.create}
                    {icon action=add text="Add more text at bottom"|gettext}
                {/if}
                {if $permissions.manage}
                    {ddrerank items=$items model="text" label="Text Items"|gettext}
                {/if}
            </div>
        {/permissions}
        {if $config.moduledescription != ""}
            {$config.moduledescription}
        {/if}
        {$myloc=serialize($__loc)}
        {if ($permissions.edit || ($permissions.create && $item->poster == $user->id)) && !$preview}
            {$inline = true}
        {/if}
        {foreach from=$items item=item name=items}
            {if ($permissions.edit || ($permissions.create && $item->poster == $user->id)) && !$preview}
                {$make_edit = ' contenteditable="true" class="editable"'}
            {else}
                {$make_edit = ''}
            {/if}
            <div id="text-{$item->id}" class="item{if !$item->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}">
                {if $item->title}<{$config.item_level|default:'h2'}><div id="title-{$item->id}"{$make_edit}>{$item->title}</div></{$config.item_level|default:'h2'}>{/if}
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $smarty.const.ENABLE_WORKFLOW}
                                <span class="revisionnum approval" title="{'Viewing Revision #'|gettext}{$item->revision_id}">{$item->revision_id}</span>
                            {/if}
                            {if $myloc != $item->location_data}
                                {if $permissions.manage}
                                    {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$item}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                            {icon class=delete action=delete text='Delete'|gettext}
                        {/if}
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $item->title}
                                <a class="deletetitle" id="deletetitle-{$item->id}" href="#" title="{'Delete Title'|gettext}">{'Delete Title'|gettext}</a>
                            {else}
                                <a class="addtitle" id="addtitle-{$item->id}" href="#" title="{'Add Title'|gettext}">{'Add Title'|gettext}</a>
                            {/if}
                        {/if}
                        {if !$item->approved && $smarty.const.ENABLE_WORKFLOW && $permissions.approve && ($permissions.edit || ($permissions.create && $item->poster == $user->id))}
                            {icon action=approve record=$item}
                        {/if}
                    </div>
                {/permissions}
                <div class="bodycopy">
                    {if $config.ffloat != "Below"}
                        {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item}
                    {/if}
                    <div id="body-{$item->id}"{$make_edit}>
                        {$item->body}
                    </div>
                    {if $config.ffloat == "Below"}
                        {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item}
                    {/if}
                    {clear}
                </div>
            </div>
        {/foreach}
    </div>
    {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon action=add text="Add more text here"|gettext}
            {/if}
        </div>
    {/permissions}
</div>

{if $inline && !$preview}
    {if $smarty.const.SITE_WYSIWYG_EDITOR == "ckeditor"}
        {script unique="ckeditor" src="`$smarty.const.PATH_RELATIVE`external/editors/ckeditor/ckeditor.js"}
            CKEDITOR.disableAutoInline = true;
        {/script}
        {$contentCSS = ""}
        {$css = "themes/`$smarty.const.DISPLAY_THEME`/editors/ckeditor/ckeditor.css"}
        {if ($smarty.const.THEME_STYLE != "" && is_file("`$smarty.const.BASE`themes/`$smarty.const.DISPLAY_THEME`/editors/ckeditor/ckeditor_`$smarty.const.THEME_STYLE`.css"))}
            {$css = "themes/`$smarty.const.DISPLAY_THEME`/editors/ckeditor/ckeditor_`$smarty.const.THEME_STYLE`.css"}
        {/if}
        {if is_file($smarty.const.BASE|cat:$css)}
            {$contentCSS = "contentsCss : '`$smarty.const.PATH_RELATIVE|cat:$css`',"}
        {/if}
        {if is_file("`$smarty.const.BASE`themes/`$smarty.const.DISPLAY_THEME`/editors/ckeditor/config.js'")}
            {$configjs = "customConfig : '`$smarty.const.PATH_RELATIVE`themes/' . DISPLAY_THEME . '/editors/ckeditor/config.js',"}
        {else}
            {$configjs = ''}
        {/if}
    {elseif $smarty.const.SITE_WYSIWYG_EDITOR == "tinymce"}
        {script unique="tinymce" src="`$smarty.const.PATH_RELATIVE`external/editors/tinymce/tinymce.min.js"}
        {/script}
    {/if}

    {script unique=$name jquery="jqueryui"}
    {literal}
    $(document).ready(function(){
        var src = '{/literal}{$__loc->src}{literal}';
        var workflow = {/literal}{$smarty.const.ENABLE_WORKFLOW}{literal};

        {/literal}{if $smarty.const.SITE_WYSIWYG_EDITOR == "ckeditor"}{literal}
//        CKEDITOR.disableAutoInline = true;
        var fullToolbar = {/literal}{if empty($editor->data)}''{else}[{stripSlashes($editor->data)}]{/if}{literal};
        var titleToolbar = [['Cut','Copy','Paste',"PasteText","Undo","Redo"],["Find","Replace","SelectAll","Scayt"],['About']];
        {/literal}{elseif $smarty.const.SITE_WYSIWYG_EDITOR == "tinymce"}{literal}
        var fullToolbar = {/literal}{if empty($editor->data)}'formatselect fontselect fontsizeselect forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent '+
            'link unlink image | visualblocks localautosave'{else}[{stripSlashes($editor->data)}]{/if}{literal};
        var titleToolbar = 'cut copy paste pastetext | undo redo localautosave | searchreplace selectall';
        {/literal}{/if}{literal}

        var setContent = function(item, data) {
            {/literal}{if $smarty.const.SITE_WYSIWYG_EDITOR == "ckeditor"}{literal}
            CKEDITOR.instances[item].setData(data);
            {/literal}{elseif $smarty.const.SITE_WYSIWYG_EDITOR == "tinymce"}{literal}
            tinymce.get(item).setContent(data);
            {/literal}{/if}{literal}
        };

        var errorEditor = function() {
            var dialog = $('<p>{/literal}{'You no longer have permission to Edit'|gettext}{literal}</p>').dialog({
                width: 375,
                dialogClass: "warning",
                title: '{/literal}{'Inline Text Editor'|gettext}{literal}',
                buttons: {
                    "Ok":  function() {
                        dialog.dialog('close');
                        location.reload(true);
                    }
                }
            });
        };

        var saveEditor = function(item, data) {
            if(parseInt({/literal}{!($config.fast_save || $smarty.const.EDITOR_FAST_SAVE)}{literal})) {
                var dialog = $('<p>{/literal}{'Save these changes?'|gettext}{literal}</p>').dialog({
                    width: 375,
                    title: '{/literal}{'Text Item Updated'|gettext}{literal}',
                    buttons: {
                        "Yes": function() {
                            $.ajax({
                                type: "POST",
                                headers: { 'X-Transaction': 'Saving Text Item'},
                                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=edit_item&ajax_action=1&json=1&src="+src,
                                data: "id="+item[1] + "&type="+item[0] + "&value="+encodeURIComponent(data),
                                success:function(msg) {
                                    if (msg.replyCode == '200') {
                                        data = $.parseJSON(msg.data);
                                        if (workflow) {
                                            $('#text-' + data.id + ' span.revisionnum.approval').html(data.revision_id);
                                            if (!data.approved) {
                                                $('#text-' + data.id).addClass('unapproved');
                                            }
                                        }
                                        var title = data.title;
                                        if (title == '') {
                                            title = '{/literal}{'Untitled'|gettext}{literal}';
                                        }
                                        $('input:hidden[name=\'rerank[]\'][value=\'' + data.id + '\']').siblings('span').html(title);
                                    } else {
                                        errorEditor();
                                    }
                                }
                            });
                            dialog.dialog('close');
                        },
                        "No, Undo All Changes":  function() {
                            $.ajax({
                                type: "POST",
                                headers: { 'X-Transaction': 'Undoing Text Item'},
                                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=edit_item&ajax_action=1&json=1&src="+src,
                                data: "id="+item[1] + "&type=revert",
                                success:function(msg) {
                                    if (msg.replyCode == '200') {
                        //                var msg = $.parseJSON(data);
                                        data = $.parseJSON(msg.data);
                                        setContent('body-' + data.id, data.body);
                                        setContent('title-' + data.id, data.title);
                                    } else {
                                        errorEditor();
                                    }
                                }
                            });
                            dialog.dialog('close');
                        },
                        "Cancel":  function() {
                            dialog.dialog('close');
                        }
                    }
                });
            } else {
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Saving Text Item'},
                    url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=edit_item&ajax_action=1&json=1&src="+src,
                    data: "id="+item[1] + "&type="+item[0] + "&value="+encodeURIComponent(data),
                    success:function(msg) {
                        if (msg.replyCode == '200') {
                            data = $.parseJSON(msg.data);
                            if (workflow) {
                                $('#text-' + data.id + ' span.revisionnum.approval').html(data.revision_id);
                                if (!data.approved) {
                                    $('#text-' + data.id).addClass('unapproved');
                                }
                            }
                            var title = data.title;
                            if (title == '') {
                                title = '{/literal}{'Untitled'|gettext}{literal}';
                            }
                            $('input:hidden[name=\'rerank[]\'][value=\'' + data.id + '\']').siblings('span').html(title);
                        } else {
                            errorEditor();
                        }
                    }
                });
            }
        };

        var startEditor = function(node) {
            if ($(node).attr('id').substr(0,5) == 'title') {
                mytoolbar = titleToolbar;
                tinymenu = false;
                tinyplugins = ['searchreplace,contextmenu,paste,link,localautosave'];
            } else {
                mytoolbar = fullToolbar;
                tinymenu = true;
                tinyplugins = ['image,imagetools,searchreplace,contextmenu,paste,link,textcolor,visualblocks,code,localautosave'];
            }

            {/literal}{if $smarty.const.SITE_WYSIWYG_EDITOR == "ckeditor"}{literal}
//            var editor = CKEDITOR.instances[node.id];
//            if (editor) { CKEDITOR.remove(editor); }
            CKEDITOR.inline(node, {
                on: {
                    blur: function( event ) {
                        if (event.editor.checkDirty()) {
                            var data = event.editor.getData();
                            var item = event.editor.name.split('-');
                            saveEditor(item, data);
                        }
                    }
                },
//                instanceReady: function( event) {
//                    // Autosave but no more frequent than 5 sec.
//                    var buffer = CKEDITOR.tools.eventsBuffer( 5000, function() {
//                        console.log( 'Autosave!' );
//                        var data = event.editor.getData();
//                        var item = event.editor.name.split('-');
//                        saveEditor(item, data);
//                    } );
//                    this.on( 'change', buffer.input );
//                }

                skin : '{/literal}{$editor->skin}{literal}',
                toolbar : mytoolbar,
                scayt_autoStartup : '{/literal}{$editor->scayt_on}{literal}',
                {/literal}{$editor->paste_word}{literal}
                pasteFromWordPromptCleanup : true,
                filebrowserBrowseUrl : '{/literal}{link controller="file" action="picker" ajax_action=1 update="ck"}{literal}',
                filebrowserImageBrowseUrl : '{/literal}{link controller="file" action="picker" ajax_action=1 update="ck" filter="image"}{literal}',
                filebrowserFlashBrowseUrl : '{/literal}{link controller="file" action="picker" ajax_action=1 update="ck"}{literal}',
                {/literal}{if (!$user->globalPerm('prevent_uploads'))}
                filebrowserUploadUrl : EXPONENT.PATH_RELATIVE + 'framework/modules/file/connector/uploader.php',
                uploadUrl : EXPONENT.PATH_RELATIVE + 'framework/modules/file/connector/uploader_paste.php',
                {/if}{literal}
                filebrowserWindowWidth : {/literal}{$smarty.const.FM_WIDTH}{literal},
                filebrowserWindowHeight : {/literal}{$smarty.const.FM_HEIGHT}{literal},
                filebrowserImageBrowseLinkUrl : EXPONENT.PATH_RELATIVE + 'framework/modules/file/connector/ckeditor_link.php?update=ck',
                filebrowserLinkBrowseUrl : EXPONENT.PATH_RELATIVE + 'framework/modules/file/connector/ckeditor_link.php?update=ck',
                filebrowserLinkWindowWidth : 320,
                filebrowserLinkWindowHeight : 600,
                extraPlugins : 'autosave,tableresize,sourcedialog,image2,uploadimage,quicktable,showborders,{/literal}{stripSlashes($editor->plugins)}{literal}',
                removePlugins: 'image,forms,flash',
                image2_alignClasses: [ 'image-left', 'image-center', 'image-right' ],
                image2_captionedClass: 'image-captioned',
                {/literal}{$editor->additionalConfig}{literal}
                height : 200,
                autoGrow_minHeight : 200,
                autoGrow_maxHeight : 400,
                autoGrow_onStartup : false,
                toolbarCanCollapse : true,
                entities_additional : '',
                {/literal}{$contentCSS}{literal}
                {/literal}{$configjs}{literal}
                stylesSet : {/literal}{$editor->stylesset}{literal},
                format_tags : {/literal}{$editor->formattags}{literal},
                font_names :
                    {/literal}{$editor->fontnames}{literal},
                uiColor : '#aaaaaa',
                baseHref : EXPONENT.PATH_RELATIVE,
            });
        {/literal}{elseif $smarty.const.SITE_WYSIWYG_EDITOR == "tinymce"}{literal}
            tinymce.init({
                selector : '#'+node.id,
                plugins : tinyplugins,
                inline: true,
                document_base_url : EXPONENT.PATH_RELATIVE,
                toolbar: mytoolbar,
                menubar: tinymenu,
                toolbar_items_size: 'small',
                skin : '{/literal}{$editor->skin}{literal}',
                image_advtab: true,
                image_title: true,
                image_caption: true,
                pagebreak_separator: '<div style=\"page-break-after: always;\"><span style=\"display: none;\">&nbsp;</span></div>',
                {/literal}{$editor->upload}{literal}
                browser_spellcheck : {/literal}{$editor->scayt_on}{literal},
//                importcss_append: true,
                style_formats: [{/literal}{$editor->stylesset}{literal}],
                block_formats : {/literal}{$editor->formattags}{literal},
                font_formats :
                    {/literal}{$editor->fontnames}{literal},
                end_container_on_empty_block: true,
                file_picker_callback: function expBrowser (callback, value, meta) {
                    tinymce.activeEditor.windowManager.open({
                        file: EXPONENT.PATH_RELATIVE+'index.php?controller=file&action=picker&ajax_action=1&update=tiny&filter='+meta.filetype,
                        title: 'File Manager',
                        width: {/literal}{$smarty.const.FM_WIDTH}{literal},
                        height: {/literal}{$smarty.const.FM_HEIGHT}{literal},
                        resizable: 'yes'
                    }, {
                        oninsert: function (url, alt, title) {
                            // Provide file and text for the link dialog
                            if (meta.filetype == 'file') {
                                callback(url, {text: alt, title: title});
                            }

                            // Provide image and alt text for the image dialog
                            if (meta.filetype == 'image') {
                                callback(url, {alt: alt});
                            }

                            // Provide alternative source and posted for the media dialog
                            if (meta.filetype == 'media') {
                                callback(url, {poster: alt});
                            }
                        }
                    });
                    return false;
                },
                setup: function (theEditor) {
                    theEditor.on('blur', function (e) {
                        if (this.isDirty()) {
                            var data = this.getContent();
                            var item = this.id.split('-');
                            saveEditor(item, data);
                        }
                    });
                }
            });
            {/literal}{/if}{literal}
        };

        var killEditor = function(node) {
            {/literal}{if $smarty.const.SITE_WYSIWYG_EDITOR == "ckeditor"}{literal}
            CKEDITOR.instances[node].destroy();
            {/literal}{elseif $smarty.const.SITE_WYSIWYG_EDITOR == "tinymce"}{literal}
            tinymce.execCommand('mceRemoveControl', true, '#'+node.id);
            {/literal}{/if}{literal}
        };

        editableBlocks = $('#textmodule-{/literal}{$name}{literal} div[contenteditable="true"]');
        for (var i = 0; i < editableBlocks.length; i++) {
            startEditor(editableBlocks[i]);
        }

        // Add a text item
        $('#textmodule-{/literal}{$name}{literal}').on('click', '.add', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Adding Text Item'},
                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=edit_item&ajax_action=1&json=1&src="+src,
                data: "id=0",
                success: function(msg) {
                    if (msg.replyCode == '200') {
                        data = $.parseJSON(msg.data);
                        newItem =  '<div id="text-' + data.id + '" class="item';
                        if (workflow && !data.approved) {
                            newItem += ' unapproved';
                        }
                        newItem += '"><{/literal}{$config.item_level|default:'h2'}{literal}><div id="title-' + data.id + '" contenteditable="true" class="editable">{/literal}{'title placeholder'|gettext}{literal}</div></{/literal}{$config.item_level|default:'h2'}{literal}>';
                        newItem += '<div class="item-actions">';
                        if (workflow) {
                            newItem += '<span class="revisionnum approval" title="Viewing Revision #' + data.revision_id + '">' + data.revision_id + '</span>';
                        }
                        newItem += '<a class="edit" title="{/literal}{'Edit this text item'|gettext}{literal}" href="' + EXPONENT.PATH_RELATIVE + 'text/edit/id/' + data.id + '/src/' + src + '">{/literal}{'Edit'|gettext}{literal}</a>';
                        newItem += '<a class="delete" title="{/literal}{'Delete'|gettext}{literal}" href="' + EXPONENT.PATH_RELATIVE + 'text/delete/id/' + data.id + '/src/' + src + '">{/literal}{'Delete'|gettext}{literal}</a>';
                        newItem +='<a class="deletetitle" id="deletetitle-' + data.id + '" href="#" title="{/literal}{'Delete Title'|gettext}{literal}">{/literal}{'Delete Title'|gettext}{literal}</a></div>';
                        newItem += '<div class="bodycopy"><div id="body-' + data.id + '" contenteditable="true" class="editable">{/literal}{'content placeholder'|gettext}{literal}</div></div></div>';
                        $('#textcontent-{/literal}{$name}{literal}').append(newItem);
                        startEditor($('#title-' + data.id)[0]);
                        startEditor($('#body-' + data.id)[0]);
                        newDDItem = '<li><input type="hidden" value="' + data.id + '" name="rerank[]"><div class="fpdrag"></div><span class="label">{/literal}{'title placeholder'|gettext}{literal}</span></li>';
                        $('#listToOrder' + src.slice(1)).append(newDDItem);
                    } else {
                        errorEditor();
                    }
                }
            });
        });

        // Add a title
        $('#textmodule-{/literal}{$name}{literal}').on('click', '.addtitle', function(event) {
            event.preventDefault();
            ctrl = $(event.target).parent().parent();
            var item = ctrl.attr('id').split('-');
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Adding Text Title'},
                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=edit_item&ajax_action=1&json=1&src="+src,
                data: "id="+item[1] + "&type=title&value={/literal}{'title placeholder'|gettext|escape:'url'}{literal}",
                success: function(msg) {
                    if (msg.replyCode == '200') {
                        data = $.parseJSON(msg.data);
                        if (workflow) {
                            $('#text-' + data.id + ' span.revisionnum.approval').html(data.revision_id);
                            if (!data.approved) {
                                $('#text-' + data.id).addClass('unapproved');
                            }
                        }
                        newItem = '<{/literal}{$config.item_level|default:'h2'}{literal}><div id="title-' + data.id + '" contenteditable="true" class="editable">{/literal}{'title placeholder'|gettext}{literal}</div></{/literal}{$config.item_level|default:'h2'}{literal}>';
                        $('#text-' + data.id).prepend(newItem);
                        $('input:hidden[name=\'rerank[]\'][value=\'' + data.id + '\']').siblings('span').html('{/literal}{'title placeholder'|gettext}{literal}');
                        startEditor($('#title-' + data.id)[0]);
                        chgItem ='<a class="deletetitle" id="deletetitle-' + data.id + '" href="#" title="{/literal}{'Delete Title'|gettext}{literal}">{/literal}{'Delete Title'|gettext}{literal}</a>';
                        addparent = $('#addtitle-' + data.id).parent();
                        $('#addtitle-' + data.id).remove();
                        addparent.append(chgItem);
                    } else {
                        errorEditor();
                    }
                }
            });
        });

        // Delete a text item
        $('#textmodule-{/literal}{$name}{literal}').on('click', '.delete', function(event) {
            event.preventDefault();
            if (confirm('{/literal}{'Are you sure you want to delete this text item?'|gettext}{literal}')) {
                ctrl = $(event.target).parent().parent();
                var item = ctrl.attr('id').split('-');
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Deleting Text Item'},
                    url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=delete_item&ajax_action=1&json=1&src="+src,
                    data: "id=" + item[1],
                    success: function(msg) {
                        if (msg.replyCode == '200') {
                            $('#text-' + msg.data).remove();
                            $('input:hidden[name=\'rerank[]\'][value=\'' + msg.data + '\']').parent().remove();
                            killEditor('title-' + msg.data);
                            killEditor('body-' + msg.data);
                        } else {
                            errorEditor();
                        }
                    }
                });
            }
        });

        // Delete a title
        $('#textmodule-{/literal}{$name}{literal}').on('click', '.deletetitle', function(event) {
            event.preventDefault();
            if (confirm('{/literal}{'Are you sure you want to delete this text item title?'|gettext}{literal}')) {
                ctrl = $(event.target).parent().parent();
                var item = ctrl.attr('id').split('-');
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Deleting Text Title'},
                    url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=edit_item&ajax_action=1&json=1&src="+src,
                    data: "id="+item[1] + "&type=title",
                    success: function(msg) {
                        if (msg.replyCode == '200') {
                            if (workflow) {
                                $('#text-' + data.id + ' span.revisionnum.approval').html(data.revision_id);
                                if (!data.approved) {
                                    $('#text-' + data.id).addClass('unapproved');
                                }
                            }
                            $('#title-' + data.id).parent().remove();
                            $('input:hidden[name=\'rerank[]\'][value=\'' + data.id + '\']').siblings('span').html('{/literal}{'Untitled'|gettext}{literal}');
                            chgItem ='<a class="addtitle" id="addtitle-' + data.id + '" href="#" title="{/literal}{'Add Title'|gettext}{literal}">{/literal}{'Add Title'|gettext}{literal}</a>';
                            delparent = $('#deletetitle-' + data.id).parent();
                            killEditor('title-' + data.id);
                            $('#deletetitle-' + data.id).remove();
                            delparent.append(chgItem);
                        } else {
                            errorEditor();
                        }
                    }
                });
            }
        });
    });
    {/literal}
    {/script}
{/if}
