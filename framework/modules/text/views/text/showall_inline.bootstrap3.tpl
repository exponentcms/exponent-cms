{*
 * Copyright (c) 2004-2015 OIC Group, Inc.
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
{if $smarty.const.BTN_SIZE == 'large'}
    {$btn_size = ''}
    {$icon_size = 'fa-lg'}
{elseif $smarty.const.BTN_SIZE == 'small'}
    {$btn_size = 'btn-xs'}
    {$icon_size = ''}
{else}
    {$btn_size = 'btn-sm'}
    {$icon_size = 'fa-lg'}
{/if}

<div id="textmodule-{$name}" class="module text showall showall-inline">
    <div id="textcontent-{$name}">
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
        {permissions}
            <div class="module-actions">
                {if $permissions.create}
                    <a class="add-body btn btn-success {$btn_size}" href="{link action=add}" title="{'Add more text at bottom'|gettext}"><i class="fa fa-plus-circle {$icon_size}"></i> {'Add more text at bottom'|gettext}</a>
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

        {foreach from=$items item=item name=items}
            {if ($permissions.edit || ($permissions.create && $item->poster == $user->id)) && !$preview}
                {$make_edit = ' contenteditable="true" class="editable"'}
                {$inline = true}
            {else}
                {$make_edit = ''}
            {/if}
            <div id="text-{$item->id}" class="item{if !$item->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}">
                {if $item->title}<{$config.item_level|default:'h2'}><div id="title-{$item->id}"{$make_edit}>{$item->title}</div></{$config.item_level|default:'h2'}>{/if}
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $item->revision_id > 1 && $smarty.const.ENABLE_WORKFLOW}<span class="revisionnum approval" title="{'Viewing Revision #'|gettext}{$item->revision_id}">{$item->revision_id}</span>{/if}
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
                            <a class="delete btn btn-danger {$btn_size}" href="{link action=delete}" title="{'Delete this text item'|gettext}"><i class="fa fa-times-circle {$icon_size}"></i> {'Delete'|gettext}</a>
                        {/if}
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $item->title}
                                <a class="delete-title btn btn-danger {$btn_size}" id="deletetitle-{$item->id}" href="#" title="{'Delete Title'|gettext}"><i class="fa fa-times-circle {$icon_size}"></i> {'Delete Title'|gettext}</a>
                            {else}
                                <a class="add-title btn btn-success {$btn_size}" id="addtitle-{$item->id}" href="#" title="{'Add Title'|gettext}"><i class="fa fa-plus-circle {$icon_size}"></i> {'Add Title'|gettext}</a>
                            {/if}
                        {/if}
                        {if !$item->approved && $smarty.const.ENABLE_WORKFLOW && $permissions.approve && ($permissions.edit || ($permissions.create && $record->poster == $user->id))}
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
                <a class="add-body btn btn-success {$btn_size}" href="{link action=add}" title="{'Add more text here'|gettext}"><i class="fa fa-plus-circle {$icon_size}"></i> {'Add more text here'|gettext}</a>
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

    {script unique=$name jquery="bootstrap-dialog" bootstrap="modal,transition"}
    {literal}
    $(document).ready(function(){
        var src = '{/literal}{$__loc->src}{literal}';

        {/literal}{if $smarty.const.SITE_WYSIWYG_EDITOR == "ckeditor"}{literal}
        CKEDITOR.disableAutoInline = true;
        var fullToolbar = {/literal}{if empty($editor->data)}''{else}[{stripSlashes($editor->data)}]{/if}{literal};
        var titleToolbar = [['Cut','Copy','Paste',"PasteText","Undo","Redo"],["Find","Replace","SelectAll","Scayt"],['About']];
        {/literal}{elseif $smarty.const.SITE_WYSIWYG_EDITOR == "tinymce"}{literal}
        var fullToolbar = {/literal}{if empty($editor->data)}''{else}[{stripSlashes($editor->data)}]{/if}{literal};
        var titleToolbar = 'cut copy paste pastetext | undo redo | searchreplace selectall';
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
                BootstrapDialog.show({
                    title: '{/literal}{'Text Item Updated'|gettext}{literal}',
                    message: '{/literal}{'Save these changes?'|gettext}{literal}',
                    buttons: [{
                        label: "{/literal}{'Yes'|gettext}{literal}",
                        action: function(dialog) {
                            $.ajax({
                                type: "POST",
                                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=saveItem&ajax_action=1&json=1&src="+src,
                                data: "id="+item[1] + "&type="+item[0] + "&value="+data,
                            });
                            $('input:hidden[name=\'rerank[]\'][value=\'' + item[1] + '\']').siblings('span').html(data);
                            dialog.close();
                        }
                    }, {
                        label: "{/literal}{'No, Undo All Changes'|gettext}{literal}",
                        action: function(dialog) {
                            $.ajax({
                                type: "POST",
                                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=saveItem&ajax_action=1&json=1&src="+src,
                                data: "id="+item[1] + "&type=revert",
                                success:function(msg) {
                                    data = $.parseJSON(msg.data);
                                    setContent('body-' + data.id, data.body);
                                    setContent('title-' + data.id, data.title);
                                }
                            });
                            dialog.close();
                        }
                    }, {
                        label: "{/literal}{'Cancel'|gettext}{literal}",
                        action: function(dialog) {
                            dialog.close();
                        }
                    }]
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
                extraPlugins : 'stylesheetparser,tableresize,sourcedialog,image2,{/literal}{stripSlashes($editor->plugins)}{literal}',  //FIXME we don't check for missing plugins
                removePlugins: 'image',
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
                plugins : ['image,searchreplace,contextmenu,paste,link'],
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

        $('#textmodule-{/literal}{$name}{literal}').on('click', '.add-body', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=saveItem&ajax_action=1&json=1&src="+src,
                data: "id=0",
    //            success:function(data) {
                success:function(msg) {
    //                var msg = $.parseJSON(data);
                    newItem = '<div id="text-' + msg.data + '" class="item"><{/literal}{$config.item_level|default:'h2'}{literal}><div id="title-' + msg.data + '" contenteditable="true" class="editable">title placeholder</div></{/literal}{$config.item_level|default:'h2'}{literal}>';
                    newItem += '<div class="item-actions"><a class="btn btn-default {/literal}{$btn_size}{literal}" title="{/literal}{'Edit this text item'|gettext}{literal}" href="' + EXPONENT.PATH_RELATIVE + 'text/edit/id/' + msg.data + '/src/' + src + '"><i class="fa fa-edit {/literal}{$icon_size}{literal}"></i> {/literal}{'Edit'|gettext}{literal}</a>';
                    newItem += '<a class="delete btn btn-danger {/literal}{$btn_size}{literal}" title="{/literal}{'Delete'|gettext}{literal}" href="' + EXPONENT.PATH_RELATIVE + 'text/delete/id/' + msg.data + '/src/' + src + '"><i class="fa fa-times-circle {/literal}{$icon_size}{literal}"></i> {/literal}{'Delete'|gettext}{literal}</a>';
                    newItem +='<a class="delete-title btn btn-danger {/literal}{$btn_size}{literal}" id="deletetitle-' + msg.data + '" href="#" title="{/literal}{'Delete Title'|gettext}{literal}"><i class="fa fa-times-circle {/literal}{$icon_size}{literal}"></i> {/literal}{'Delete Title'|gettext}{literal}</a></div>';
                    newItem += '<div class="bodycopy"><div id="body-' + msg.data + '" contenteditable="true" class="editable">content placeholder</div></div></div>';
                    $('#textcontent-{/literal}{$name}{literal}').append(newItem);
                    startEditor($('#title-' + msg.data)[0]);
                    startEditor($('#body-' + msg.data)[0]);
                    newDDItem = '<li><input type="hidden" class="form-control" value="' + msg.data + '" name="rerank[]"><div class="fpdrag"></div><span class="label">title placeholder</span></li>';
                    $('#listToOrder' + src.slice(1)).append(newDDItem);
                }
            });
        });

        $('#textmodule-{/literal}{$name}{literal}').on('click', '.add-title', function(event) {
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
                    chgItem ='<a class="delete-title btn btn-danger {/literal}{$btn_size}{literal}" id="deletetitle-' + msg.data + '" href="#" title="{/literal}{'Delete Title'|gettext}{literal}"><i class="fa fa-times-circle {/literal}{$icon_size}{literal}"></i> {/literal}{'Delete Title'|gettext}{literal}</a>';
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

        $('#textmodule-{/literal}{$name}{literal}').on('click', '.delete-title', function(event) {
            event.preventDefault();
            if (confirm('{/literal}{'Are you sure you want to delete this text item title?'|gettext}{literal}')) {
                ctrl = $(event.target).parent().parent();
                var item = ctrl.attr('id').split('-');
                $.ajax({
                    type: "POST",
                    url: EXPONENT.PATH_RELATIVE+"index.php?controller=text&action=saveItem&ajax_action=1&json=1&src="+src,
                    data: "id="+item[1] + "&type=title",
    //                success: function(data) {
                    success: function(msg) {
    //                msg = $.parseJSON(data);
                        $('#title-' + msg.data).parent().remove();
                        $('input:hidden[name=\'rerank[]\'][value=\'' + msg.data + '\']').siblings('span').html('{/literal}{'Untitled'|gettext}{literal}');
                        chgItem ='<a class="add-title btn btn-success {/literal}{$btn_size}{literal}" id="addtitle-' + msg.data + '" href="#" title="{/literal}{'Add Title'|gettext}{literal}"><i class="fa fa-plus-circle {/literal}{$icon_size}{literal}"></i> {/literal}{'Add Title'|gettext}{literal}</a>';
                        delparent = $('#deletetitle-' + msg.data).parent();
    //                    CKEDITOR.instances['title-' + msg.data].destroy();
                        killEditor('title-' + msg.data);
                        $('#deletetitle-' + msg.data).remove();
                        delparent.append(chgItem);
                    }
                });
            }
        });
    });
    {/literal}
    {/script}
{/if}
