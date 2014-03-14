{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
        {foreach from=$items item=text name=items}
            {if ($permissions.edit || ($permissions.create && $text->poster == $user->id)) && !$preview}
                {$make_edit = ' contenteditable="true" class="editable"'}
                {$inline = true}
            {else}
                {$make_edit = ''}
            {/if}
            <div id="text-{$text->id}" class="item">
                {if $text->title}<{$config.item_level|default:'h2'}><div id="title-{$text->id}"{$make_edit}>{$text->title}</div></{$config.item_level|default:'h2'}>{/if}
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $text->poster == $user->id)}
                            {if $myloc != $text->location_data}
                                {if $permissions.manage}
                                    {icon action=merge id=$text->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$text}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $text->poster == $user->id)}
                            {icon class=delete action=deleter text='Delete'|gettext}
                        {/if}
                        {if $permissions.edit || ($permissions.create && $text->poster == $user->id)}
                            {if $text->title}
                                <a class="deletetitle" id="deletetitle-{$text->id}" href="#" title="{'Delete Title'|gettext}">{'Delete Title'|gettext}</a>
                            {else}
                                <a class="addtitle" id="addtitle-{$text->id}" href="#" title="{'Add Title'|gettext}">{'Add Title'|gettext}</a>
                            {/if}
                        {/if}
                    </div>
                {/permissions}
                <div class="bodycopy">
                    {if $config.ffloat != "Below"}
                        {filedisplayer view="`$config.filedisplay`" files=$text->expFile record=$text}
                    {/if}
                    <div id="body-{$text->id}"{$make_edit}>
                        {$text->body}
                    </div>
                    {if $config.ffloat == "Below"}
                        {filedisplayer view="`$config.filedisplay`" files=$text->expFile record=$text}
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
        {/script}
    {elseif $smarty.const.SITE_WYSIWYG_EDITOR == "tinymce"}
        {script unique="tinymce" src="`$smarty.const.PATH_RELATIVE`external/editors/tinymce/tinymce.min.js"}
        {/script}
    {/if}

    {script unique=$name jquery="jqueryui"}
    {literal}
        src = '{/literal}{$__loc->src}{literal}';

        {/literal}{if $smarty.const.SITE_WYSIWYG_EDITOR == "ckeditor"}{literal}
        CKEDITOR.disableAutoInline = true;
        var fullToolbar = {/literal}{if empty($editor->data)}''{else}[{stripSlashes($editor->data)}]{/if}{literal};
        var titleToolbar = [['Cut','Copy','Paste',"PasteText","Undo","Redo"],["Find","Replace","SelectAll","Scayt"],['About']];
        {/literal}{elseif $smarty.const.SITE_WYSIWYG_EDITOR == "tinymce"}{literal}
        var fullToolbar = {/literal}{if empty($editor->data)}''{else}[{stripSlashes($editor->data)}]{/if}{literal};
        var titleToolbar = 'cut copy paste pastetext | undo redo | find replace selectall scayt';
        {/literal}{/if}{literal}

        var setContent = function(item, data) {
            {/literal}{if $smarty.const.SITE_WYSIWYG_EDITOR == "ckeditor"}{literal}
            CKEDITOR.instances[item].setData(data);
            {/literal}{elseif $smarty.const.SITE_WYSIWYG_EDITOR == "tinymce"}{literal}
            tinymce.get(item).setContent(data);
            {/literal}{/if}{literal}
        };

        var saveEditor = function(item, data) {
            if(parseInt({/literal}{!$config.fast_save}{literal}) && parseInt({/literal}{$smarty.const.SITE_WYSIWYG_EDITOR == 'ckeditor'}{literal})) {
                var dialog = $('<p>{/literal}{'Save these changes?'|gettext}{literal}</p>').dialog({
                    width: 375,
                    title: '{/literal}{'Text Item Updated'|gettext}{literal}',
                    buttons: {
                        "Yes": function() {
                            $.ajax({
                                type: "POST",
                                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=saveItem&ajax_action=1&json=1&src="+src,
                                data: "id="+item[1] + "&type="+item[0] + "&value="+data,
                            });
                            $('input:hidden[name=\'rerank[]\'][value=\'' + item[1] + '\']').siblings('span').html(data);
                            dialog.dialog('close');
                        },
                        "No, Undo All Changes":  function() {
                            $.ajax({
                                type: "POST",
                                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=saveItem&ajax_action=1&json=1&src="+src,
                                data: "id="+item[1] + "&type=revert",
                    //            success:function(data) {
                                success:function(msg) {
                    //                var msg = $.parseJSON(data);
                                    data = $.parseJSON(msg.data);
    //                                    CKEDITOR.instances['body-' + data.id].setData(data.body);
                                    setContent('body-' + data.id, data.body);
    //                                    CKEDITOR.instances['title-' + data.id].setData(data.title);
                                    setContent('title-' + data.id, data.title);
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
                    url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=saveItem&ajax_action=1&json=1&src="+src,
                    data: "id="+item[1] + "&type="+item[0] + "&value="+data,
                });
                $('input:hidden[name=\'rerank[]\'][value=\'' + item[1] + '\']').siblings('span').html(data);
            }
        };

        var startEditor = function(node) {
            if ($(node).attr('id').substr(0,5) == 'title') {
                mytoolbar = titleToolbar;
            } else {
                mytoolbar = fullToolbar;
            }

            {/literal}{if $smarty.const.SITE_WYSIWYG_EDITOR == "ckeditor"}{literal}
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

                skin : '{/literal}{$editor->skin}{literal}',
                toolbar : mytoolbar,
                scayt_autoStartup : '{/literal}{$editor->scayt_on}{literal}',
                {/literal}{$editor->paste_word}{literal}
                pasteFromWordPromptCleanup : true,
                filebrowserBrowseUrl : '{/literal}{link controller="file" action="picker" ajax_action=1 update="ck"}{literal}',
                filebrowserImageBrowseUrl : '{/literal}{link controller="file" action="picker" ajax_action=1 update="ck" filter="image"}{literal}',
                filebrowserFlashBrowseUrl : '{/literal}{link controller="file" action="picker" ajax_action=1 update="ck"}{literal}',
                {/literal}{if (!$user->globalPerm('prevent_uploads'))}filebrowserUploadUrl : EXPONENT.PATH_RELATIVE + 'framework/modules/file/connector/uploader.php',{/if}{literal}
                filebrowserWindowWidth : {/literal}{$smarty.const.FM_WIDTH}{literal},
                filebrowserWindowHeight : {/literal}{$smarty.const.FM_HEIGHT}{literal},
                filebrowserImageBrowseLinkUrl : EXPONENT.PATH_RELATIVE + 'framework/modules/file/connector/ckeditor_link.php',
                filebrowserLinkBrowseUrl : EXPONENT.PATH_RELATIVE + 'framework/modules/file/connector/ckeditor_link.php',
                filebrowserLinkWindowWidth : 320,
                filebrowserLinkWindowHeight : 600,
                extraPlugins : 'stylesheetparser,tableresize,sourcedialog,{/literal}{stripSlashes($editor->plugins)}{literal}',
                {/literal}{$editor->additionalConfig}{literal}
                height : 200,
                autoGrow_minHeight : 200,
                autoGrow_maxHeight : 400,
                autoGrow_onStartup : false,
                toolbarCanCollapse : true,
                entities_additional : '',
        //            " . $contentCSS . "
        //            stylesSet : " . $stylesset . ",
        //            format_tags : " . $formattags . ",
        //            font_names :
        //                " . $fontnames . ",
                uiColor : '#aaaaaa',
                baseHref : EXPONENT.PATH_RELATIVE,

            });
            {/literal}{elseif $smarty.const.SITE_WYSIWYG_EDITOR == "tinymce"}{literal}
            tinymce.init({
                selector : '#'+node.id,
                plugins : ['image,searchreplace,contextmenu,paste'],
                inline: true,
                document_base_url : EXPONENT.PATH_RELATIVE,
                toolbar: mytoolbar,
                menubar: false,
                toolbar_items_size: 'small',
                image_advtab: true,
                skin : '{/literal}{$editor->skin}{literal}',
                importcss_append: true,
                end_container_on_empty_block: true,
                file_browser_callback: function expBrowser (field_name, url, type, win) {
                    tinymce.activeEditor.windowManager.open({
                        file: EXPONENT.PATH_RELATIVE+'index.php?controller=file&action=picker&ajax_action=1&update=tiny&filter='+type,
                        title: 'File Manager',
                        width: {/literal}{$smarty.const.FM_WIDTH}{literal},
                        height: {/literal}{$smarty.const.FM_HEIGHT}{literal},
                        resizable: 'yes'
                    }, {
                        setUrl: function (url) {
                            win.document.getElementById(field_name).value = url;
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

        $('#textmodule-{/literal}{$name}{literal}').on('click', '.add', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=saveItem&ajax_action=1&json=1&src="+src,
                data: "id=0",
    //            success: function(data) {
                success: function(msg) {
    //                msg = $.parseJSON(data);
                    newItem = '<div id="text-' + msg.data + '" class="item"><{/literal}{$config.item_level|default:'h2'}{literal}><div id="title-' + msg.data + '" contenteditable="true" class="editable">title placeholder</div></{/literal}{$config.item_level|default:'h2'}{literal}>';
                    newItem += '<div class="item-actions"><a class="edit" title="{/literal}{'Edit this text item'|gettext}{literal}" href="http://localhost/exp2/text/edit/id/' + msg.data + '/src/' + src + '">{/literal}{'Edit'|gettext}{literal}</a>';
                    newItem += '<a class="delete" title="{/literal}{'Delete'|gettext}{literal}" href="#">{/literal}{'Delete'|gettext}{literal}</a>';
                    newItem +='<a class="deletetitle" id="deletetitle-' + msg.data + '" href="#" title="{/literal}{'Delete Title'|gettext}{literal}">{/literal}{'Delete Title'|gettext}{literal}</a></div>';
                    newItem += '<div class="bodycopy"><div id="body-' + msg.data + '" contenteditable="true" class="editable">content placeholder</div></div></div>';
                    $('#textcontent-{/literal}{$name}{literal}').append(newItem);
                    startEditor($('#title-' + msg.data)[0]);
                    startEditor($('#body-' + msg.data)[0]);
                    newDDItem = '<li><input type="hidden" value="' + msg.data + '" name="rerank[]"><div class="fpdrag"></div><span class="label">title placeholder</span></li>';
                    $('#listToOrder' + src.slice(1)).append(newDDItem);
                }
            });
        });

        $('#textmodule-{/literal}{$name}{literal}').on('click', '.addtitle', function(event) {
            event.preventDefault();
            ctrl = $(event.target).parent().parent();
            var item = ctrl.attr('id').split('-');
            $.ajax({
                type: "POST",
                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=saveItem&ajax_action=1&json=1&src="+src,
                data: "id="+item[1] + "&type=title&value=title+placeholder",
    //            success: function(data) {
                success: function(msg) {
    //                msg = $.parseJSON(data);
                    newItem = '<{/literal}{$config.item_level|default:'h2'}{literal}><div id="title-' + msg.data + '" contenteditable="true" class="editable">title placeholder</div></{/literal}{$config.item_level|default:'h2'}{literal}>';
                    $('#text-' + msg.data).prepend(newItem);
                    $('input:hidden[name=\'rerank[]\'][value=\'' + msg.data + '\']').siblings('span').html('title placeholder');
                    startEditor($('#title-' + msg.data)[0]);
                    chgItem ='<a class="deletetitle" id="deletetitle-' + msg.data + '" href="#" title="{/literal}{'Delete Title'|gettext}{literal}">{/literal}{'Delete Title'|gettext}{literal}</a>';
                    addparent = $('#addtitle-' + msg.data).parent();
                    $('#addtitle-' + msg.data).remove();
                    addparent.append(chgItem);
                }
            });
        });

        $('#textmodule-{/literal}{$name}{literal}').on('click', '.delete', function(event) {
            event.preventDefault();
            if (confirm('{/literal}{'Are you sure you want to delete this text item?'|gettext}{literal}')) {
                ctrl = $(event.target).parent().parent();
                var item = ctrl.attr('id').split('-');
                $.ajax({
                    type: "POST",
                    url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=deleteItem&ajax_action=1&json=1&src="+src,
                    data: "id=" + item[1],
        //            success: function(data) {
                    success: function(msg) {
        //                msg = $.parseJSON(data);
                        $('#text-' + msg.data).remove();
                        $('input:hidden[name=\'rerank[]\'][value=\'' + msg.data + '\']').parent().remove();
    //                    CKEDITOR.instances['title-' + msg.data].destroy();
                        killEditor('title-' + msg.data);
    //                    CKEDITOR.instances['body-' + msg.data].destroy();
                        killEditor('body-' + msg.data);
                    }
                });
            }
        });

        $('#textmodule-{/literal}{$name}{literal}').on('click', '.deletetitle', function(event) {
            event.preventDefault();
            if (confirm('{/literal}{'Are you sure you want to delete this text item title?'|gettext}{literal}')) {
                ctrl = $(event.target).parent().parent();
                var item = ctrl.attr('id').split('-');
                $.ajax({
                    type: "POST",
                    url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=saveItem&ajax_action=1&json=1&src="+src,
                    data: "id="+item[1] + "&type=title",
        //            success: function(data) {
                    success: function(msg) {
        //                msg = $.parseJSON(data);
                        $('#title-' + msg.data).parent().remove();
                        $('input:hidden[name=\'rerank[]\'][value=\'' + msg.data + '\']').siblings('span').html('{/literal}{'Untitled'|gettext}{literal}');
                        chgItem ='<a class="addtitle" id="addtitle-' + msg.data + '" href="#" title="{/literal}{'Add Title'|gettext}{literal}">{/literal}{'Add Title'|gettext}{literal}</a>';
                        delparent = $('#deletetitle-' + msg.data).parent();
    //                    CKEDITOR.instances['title-' + msg.data].destroy();
                        killEditor('title-' + msg.data);
                        $('#deletetitle-' + msg.data).remove();
                        delparent.append(chgItem);
                    }
                });
            }
        });
    {/literal}
    {/script}
{/if}
