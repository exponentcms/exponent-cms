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

{*
 * The main 'html' file to instantiate elFinder
*}

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2">
    <title>{'File Manager'|gettext}  |  Exponent CMS</title>

    <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}exponent.js2.php"></script>
    <!--[if lt IE 9]>
        <script src="{$smarty.const.JQUERY_SCRIPT}" charset="utf-8"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
        <script src="{$smarty.const.JQUERY2_SCRIPT}" charset="utf-8"></script>
    <!--<![endif]-->
    <script src="{$smarty.const.JQUERYUI_SCRIPT}" type="text/javascript" charset="utf-8"></script>

    <link rel="stylesheet" href="{$smarty.const.JQUERY_RELATIVE}css/smoothness/jquery-ui.min.css" type="text/css" media="screen" title="no title" charset="utf-8">

    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/commands.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/common.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/contextmenu.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/cwd.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/dialog.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/fonts.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/navbar.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/places.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/quicklook.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/statusbar.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/toast.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/toolbar.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}framework/modules/file/assets/css/elfinder.css" type="text/css">

    {*<link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/theme.css" type="text/css">  *}{* default OSX theme *}
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder{$smarty.const.ELFINDER_THEME}/css/theme.css" type="text/css">

    <!-- elfinder core -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.version.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/jquery.elfinder.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.options.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.history.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.command.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.resources.js"></script>

    <!-- elfinder ui -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/button.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/contextmenu.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/cwd.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/dialog.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/fullscreenbutton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/mkdirbutton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/navbar.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/overlay.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/panel.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/path.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/places.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/searchbutton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/sortbutton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/stat.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/toast.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/toolbar.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/tree.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/uploadButton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/viewbutton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/workzone.js"></script>

    <!-- elfinder commands -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/archive.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/back.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/chmod.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/colwidth.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/copy.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/cut.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/download.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/duplicate.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/edit.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/extract.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/forward.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/fullscreen.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/getfile.js"></script>
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/help.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/help.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/home.js"></script>
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/info.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/info.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/links.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/mkdir.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/mkfile.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/netmount.js"></script>
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/open.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/open.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/opendir.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/paste.js"></script>
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/pixlr.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/pixlr.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/places.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/quicklook.js"></script>
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/quicklook.plugins.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/quicklook.plugins.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/reload.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/rename.js"></script>
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/resize.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/resize.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/rm.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/search.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/sort.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/up.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/upload.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/view.js"></script>

    <!-- elfinder languages -->
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/i18n/elfinder.{substr($smarty.const.LOCALE,0,2)}.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/i18n/elfinder.{substr($smarty.const.LOCALE,0,2)}.js"></script>

    <!-- elfinder custom extenstions -->
    <!--<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/extensions/jplayer/elfinder.quicklook.jplayer.js"></script>-->
</head>
<body{if !bs3()} class="exp-skin"{/if}>

<div id="elfinder"></div>

{script unique="picker"}
    {literal}
        // Helper function to get parameters from the url
        function getUrlParam(paramName) {
            var pathArray = window.location.pathname.split( '/' );
            if (EXPONENT.SEF_URLS && pathArray.indexOf(paramName) != -1) {
                var parm = pathArray.indexOf(paramName);
                if (parm > 0)
                    return pathArray[parm+1];
            } else {
                var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
                var match = window.location.search.match(reParam) ;
                return (match && match.length > 1) ? match[1] : '' ;
            }
        }

        // Helper function to get parameters from the query string for TinyMCE
        var FileBrowserDialogue = {
            init: function() {
                // Here goes your code for setting your custom things onLoad.
            },
            mySubmit: function (URL, alt, title) {
                // pass selected file data to TinyMCE
                top.tinymce.activeEditor.windowManager.getParams().oninsert(URL, alt, title);
                // close popup window
                top.tinymce.activeEditor.windowManager.close();
            }
        }

        $().ready(function() {
            var funcNum = getUrlParam('CKEditorFuncNum');

            var elf = $('#elfinder').elfinder({
                url: EXPONENT.PATH_RELATIVE + 'framework/modules/file/connector/elfinder.php',  // connector URL
                urlUpload: EXPONENT.URL_FULL + 'framework/modules/file/connector/elfinder.php',  // connector full URL
                // commands : [
                //     'pixlr',
                //     'open', 'opendir', 'reload', 'home', 'up', 'back', 'forward', 'getfile', 'quicklook',
                //     'download', 'rm', 'duplicate', 'rename', 'mkdir', 'mkfile', 'upload', 'copy',
                //     'cut', 'paste', 'edit', 'extract', 'archive', 'search', 'info', 'view', 'help',
                //     'resize', 'sort', 'netmount', 'netunmount', 'places', 'chmod', 'links'
                // ],
                commandsOptions : {
                    edit : {
                    {/literal}{if $smarty.const.SITE_WYSIWYG_EDITOR=="ckeditor"}{literal}
                        editors : [
                            {
                                // CKEditor for html file
                                mimes : ['text/html'],
                                exts  : ['htm', 'html', 'xhtml'],
                                load : function(textarea) {
                                    $('head').append($('<script>').attr('src', '{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/ckeditor/ckeditor.js'));
                                    return CKEDITOR.replace( textarea.id, {
                                        startupFocus : true,
                                        fullPage: true,
//                                        allowedContent: true,
                                        toolbarCanCollapse : true,
                                        toolbarStartupExpanded : false,
                                        extraPlugins : 'image2',
                                    });
                                },
                                close : function(textarea, instance) {
                                    instance.destroy();
                                },
                                save : function(textarea, instance) {
                                    textarea.value = instance.getData();
                                },
                                focus : function(textarea, instance) {
                                    instance && instance.focus();
                                }
                            } {/literal}{*,
                            {
                                // Ace editor for other text files
                                // `mimes` is not set for support everything kind of text file
                                load : function(textarea) {
                                    if (typeof ace !== 'object') {
                                        $('head').append($('<script>').attr('src', '{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/ace/src-noconflict/ace.js'));
                                        $('head').append($('<script>').attr('src', '{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/ace/src-noconflict/ext-modelist.js'));
                                        $('head').append($('<script>').attr('src', '{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/ace/src-noconflict/ext-settings_menu.js'));
                                        $('head').append($('<script>').attr('src', '{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/ace/src-noconflict/ext-language_tools.js'));
                                        $('head').append($('<script>').attr('src', '{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/ace/src-noconflict/ext-searchbox.js'));
                                    }
                                    var self = this, editor, editorBase, mode,
                                    ta = $(textarea),
                                    taBase = ta.parent(),
                                    dialog = taBase.parent(),
                                    id = textarea.id + '_ace',
                                    ext = this.file.name.replace(/^.+\.([^.]+)|(.+)$/, '$1$2').toLowerCase(),
                                    mimeMode = {
                                        'text/x-php'              : 'php',
                                        'application/x-php'       : 'php',
                                        'text/html'               : 'html',
                                        'application/xhtml+xml'   : 'html',
                                        'text/javascript'         : 'javascript',
                                        'application/javascript'  : 'javascript',
                                        'text/css'                : 'css',
                                        'text/x-c'                : 'c_cpp',
                                        'text/x-csrc'             : 'c_cpp',
                                        'text/x-chdr'             : 'c_cpp',
                                        'text/x-c++'              : 'c_cpp',
                                        'text/x-c++src'           : 'c_cpp',
                                        'text/x-c++hdr'           : 'c_cpp',
                                        'text/x-shellscript'      : 'sh',
                                        'application/x-csh'       : 'sh',
                                        'text/x-python'           : 'python',
                                        'text/x-java'             : 'java',
                                        'text/x-java-source'      : 'java',
                                        'text/x-ruby'             : 'ruby',
                                        'text/x-perl'             : 'perl',
                                        'application/x-perl'      : 'perl',
                                        'text/x-sql'              : 'sql',
                                        'text/xml'                : 'xml',
                                        'application/docbook+xml' : 'xml',
                                        'application/xml'         : 'xml'
                                    },
                                    resize = function(){
                                        dialog.height($(window).height() * 0.9).trigger('posinit');
                                        taBase.height(dialog.height() - taBase.prev().outerHeight(true) - taBase.next().outerHeight(true) - 8);
                                    };

                                    mode = ace.require('ace/ext/modelist').getModeForPath('/' + self.file.name).name;
                                    if (mode === 'text') {
                                        if (mimeMode[self.file.mime]) {
                                            mode = mimeMode[self.file.mime];
                                        }
                                    }

                                    taBase.prev().append(' (' + self.file.mime + ' : ' + mode.split(/[\/\\]/).pop() + ')');

                                    $('<div class="ui-dialog-buttonset"/>').css('float', 'left')
                                    .append(
                                        $('<button>TextArea</button>')
                                        .button()
                                        .on('click', function(){
                                            if (ta.data('ace')) {
                                                ta.data('ace', false);
                                                editorBase.hide();
                                                ta.val(editor.session.getValue()).show().focus();
                                                $(this).find('span').text('AceEditor');
                                            } else {
                                                ta.data('ace', true);
                                                editor.setValue(ta.hide().val(), -1);
                                                editorBase.show();
                                                editor.focus();
                                                $(this).find('span').text('TextArea');
                                            }
                                        })
                                    )
                                    .append(
                                        $('<button>Ace editor setting</button>')
                                        .button({
                                            icons: {
                                                primary: 'ui-icon-gear',
                                                secondary: 'ui-icon-triangle-1-e'
                                            },
                                            text: false
                                        })
                                        .on('click', function(){
                                            editor.showSettingsMenu();
                                        })
                                    )
                                    .prependTo(taBase.next());

                                    editorBase = $('<div id="'+id+'" style="width:100%; height:100%;"/>').text(ta.val()).insertBefore(ta.hide());

                                    ta.data('ace', true);
                                    editor = ace.edit(id);
                                    ace.require('ace/ext/settings_menu').init(editor);
                                    editor.$blockScrolling = Infinity;
                                    editor.setOptions({
                                        theme: 'ace/theme/monokai',
                                        mode: 'ace/mode/' + mode,
                                        wrap: true,
                                        enableBasicAutocompletion: true,
                                        enableSnippets: true,
                                        enableLiveAutocompletion: false,
                                    });
                                    editor.commands.addCommand({
                                        name : "saveFile",
                                        bindKey: {
                                            win : 'Ctrl-s',
                                            mac : 'Command-s'
                                        },
                                        exec: function(editor) {
                                            self.doSave();
                                        }
                                    });
                                    editor.commands.addCommand({
                                        name : "closeEditor",
                                        bindKey: {
                                            win : 'Ctrl-w|Ctrl-q',
                                            mac : 'Command-w|Command-q'
                                        },
                                        exec: function(editor) {
                                            self.doCancel();
                                        }
                                    });
                                    dialog.on('resize', function(){ editor.resize(); });
                                    $(window).on('resize', function(e){
                                        if (e.target !== this) return;
                                        dialog.data('resizeTimer') && clearTimeout(dialog.data('resizeTimer'));
                                        dialog.data('resizeTimer', setTimeout(function(){ resize(); }, 300));
                                    });
                                    resize();
                                    editor.resize();

                                    return editor;
                                },
                                close : function(textarea, instance) {
                                    instance.destroy();
                                    $(textarea).show();
                                },
                                save : function(textarea, instance) {
                                    if ($(textarea).data('ace')) {
                                        $(textarea).val(instance.session.getValue());
                                    }
                                },
                                focus : function(textarea, instance) {
                                    instance.focus();
                                }
                            } *}{literal}
                        ]
                        {/literal}{elseif $smarty.const.SITE_WYSIWYG_EDITOR=="tinymce"}{literal}
                        mimes : ['text/plain', 'text/html', 'text/javascript', 'text/csv', 'text/x-comma-separated-values'],
                        editors : [
                            {
                                mimes : ['text/html'],
                                exts  : ['htm', 'html', 'xhtml'],
                                load : function(textarea) {
                                    $('head').append($('<script>').attr('src', '{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/tinymce/tinymce.min.js'));
                                    tinyMCE.execCommand('mceAddEditor', true, textarea.id);
                                },
                                close : function(textarea, instance) {
                                    tinyMCE.execCommand('mceRemoveEditor', false, textarea.id);
                                },
                                save : function(textarea, editor) {
                                    textarea.value = tinyMCE.get(textarea.id).getContent({format : 'html'});
                                    tinyMCE.execCommand('mceRemoveEditor', false, textarea.id);
                                }
                            }
                        ]
                    {/literal}{/if}{literal}
                    },
                    getfile : {
                        // allow to return multiple files info
                        multiple : {/literal}{if $smarty.get.update!='noupdate' && $smarty.get.update!='ck' && $smarty.get.update!='tiny'}true{else}false{/if}{literal},
                    },
                    // "quicklook" command options. For additional extensions
                    quicklook : {
                        autoplay : false,
                        googleDocsMimes : ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                    },
                    // help dialog tabs
                    help : {
                        {/literal}{if $smarty.const.DEVELOPMENT}
                        view : ['about', 'shortcuts', 'help', 'debug'],
                        {else}
                        view : ['about', 'shortcuts', 'help', ''],
                        {/if}{literal}
                    }
                },
//                handlers : {
//                    select : function(event, elfinderInstance) {
//                        var selected = event.data.selected;
//                            if (selected.length) {
//                            // console.log(elfinderInstance.file(selected[0]))
//                            }
//                    }
//					  getfile : function(e) {
//					  	  console.log(e.data.files)
//					  }
//				  },
                {/literal}{if $filter=='image'}{literal}
                onlyMimes : ['image'],
                {/literal}{elseif $filter=='media'}{literal}
                onlyMimes : ['video'],
                {/literal}{/if}{literal}
                defaultView : '{/literal}{if $smarty.const.FM_THUMBNAILS}icons{else}list{/if}{literal}',
                // dateFormat : '{/literal}{$smarty.const.DISPLAY_DATE_FORMAT}{literal}',
                {/literal}
              	ui : ['toolbar', 'places', 'tree', 'path', 'stat'],  // we add the places/favorites
                uiOptions : {
                    // toolbar configuration
                    toolbar : [
                        ['back', 'forward'],
                        //['netmount'],       // removed
                        ['reload'],           // added
                        ['home', 'up'],       // added
                        ['mkdir', 'mkfile', 'upload'],
                        ['open', 'download', 'getfile'],
                        ['info', 'chmod'],
                        ['quicklook'],
                        ['copy', 'cut', 'paste'],
                        ['rm'],
                        ['duplicate', 'rename', 'edit', 'resize', 'pixlr'],
                        ['extract', 'archive'],
                        ['search'],
                        ['view', 'sort'],
                        ['links', 'places'],   // links added
                        ['help'],
                        ['fullscreen'],
                        // extra options
                        {
                            // auto hide on initial open
                            autoHideUA: ['Mobile']
                        }
                    ],
                    // directories tree options
                    tree : {
                        // expand current root on init
                        openRootOnLoad : true,
                        // auto load current dir parents
                        syncTree : true
                    },
                    // navbar options
                    navbar : {
                        minWidth : 150,
                        maxWidth : 500,
                        autoHideUA: ['Mobile']
                    },
                    cwd : {
                        // display parent folder with ".." name :)
                        oldSchool : false,
                        listView : {
                            // columns to be displayed
                            // default settings are:
                            // columns : ['perm', 'date', 'size', 'kind'],
                            // extra columns can be displayed if your connector supports it:
                            columns : ['date', 'size', 'kind', 'owner', 'shared'],
                            // custom columns labels:
                            columnsCustomName : {
                                owner : 'Owner',
                                shared : 'Shared',
                            }
                        }
                    }
                },
                {if $update=='ck'}
                    {$w = 38}
                    {$h = 112}
                {else}
                    {$w = 18}
                    {$h = 20}
                {/if}
                {literal}
                width : 'auto',
                height : {/literal}{$smarty.const.FM_HEIGHT - $h}{literal},
                resizable: false,
                showFiles: {/literal}{$smarty.const.FM_LIMIT}{literal},
                {/literal}{if $update!='noupdate'}{literal}
                getFileCallback : function(file) {
                    {/literal}{if $update=='ck'}{literal}
                    window.opener.CKEDITOR.tools.callFunction( funcNum, EXPONENT.PATH_RELATIVE+file.url.replace(EXPONENT.URL_FULL, ''), function() {
                        var dialog = this.getDialog();
                        if ( dialog.getName() == 'image2' ) {
                            dialog.getContentElement( 'info', 'alt' ).setValue( file.alt );
                            dialog.getContentElement( 'info', 'height' ).setValue( file.height );  //work-around
                            dialog.getContentElement( 'info', 'width' ).setValue( file.width );  //work-around
                        } else if (dialog.getName() == 'image') {
                            dialog.getContentElement( 'info', 'txtAlt' ).setValue( file.alt );
                        }
                    });
                    window.close();
                    {/literal}{elseif $update=='tiny'}{literal}
                    FileBrowserDialogue.mySubmit(EXPONENT.PATH_RELATIVE+file.url.replace(EXPONENT.URL_FULL, '')+' ', file.alt, file.title); // pass selected file data to TinyMCE
                    {/literal}{else}{literal}
                    if ((file.length) == 1) {
                        myfile = file[0];
                        window.opener.EXPONENT.passBackFile{/literal}{$update}{literal}(myfile.id);
                    } else {
                        var batchIDs = {};
                        for (var i=0; i<file.length; i++) {
                            myfile = file[i];
                            batchIDs[i] = myfile.id;
                        }
                        window.opener.EXPONENT.passBackBatch{/literal}{$update}{literal}(batchIDs);
                    }
                    window.close();
                    {/literal}{/if}{literal}
                },
                {/literal}{/if}{literal}
            }).elfinder('instance');

            // auto resize elFinder height based on window size
            $(window).resize(function(){
                var h = ($(window).height()) - 18;
                if($('#elfinder').height() != h){
                    $('#elfinder').height(h).resize();
                }
            });
        });
    {/literal}
{/script}
</body>
</html>
