{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{* The main 'html' file to instantiate elFinder *}

<!DOCTYPE html>
<html>
<head>
      {*<meta http-equiv="content-type" content="text/html; charset=utf-8" />*}
    <meta charset="{$smarty.const.LANG_CHARSET}">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2">
    <title>{'File Manager'|gettext}  |  Exponent CMS</title>
    <meta name="Generator" content="Exponent Content Management System - v{expVersion::getVersion(true)}"/>

    {if $smarty.const.USE_CDN}
    <link rel="stylesheet" href="https://code.jquery.com/ui/{$smarty.const.JQUERYUI_VERSION}/themes/smoothness/jquery-ui.css" type="text/css" media="screen" title="no title" charset="utf-8">
    {else}
    <link rel="stylesheet" href="{$smarty.const.JQUERY_RELATIVE}css/smoothness/jquery-ui.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
    {/if}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/commands.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/common.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/contextmenu.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/cwd.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/dialog.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/fonts.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/navbar.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/places.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/quicklook.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/statusbar.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/toast.css" type="text/css">*}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/toolbar.css" type="text/css">*}
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}framework/modules/file/assets/css/elfinder.css" type="text/css">

    {*<link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/theme.css" type="text/css">  *}{* default OSX theme *}
{*    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder{$smarty.const.ELFINDER_THEME}/css/theme.css" type="text/css">*}
    <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}exponent.js2.php"></script>
    <!--[if lt IE 9]>
        <script src="{$smarty.const.JQUERY_SCRIPT}" charset="utf-8"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
        {*<script src="{$smarty.const.JQUERY2_SCRIPT}" charset="utf-8"></script>*}
        <script src="{$smarty.const.JQUERY3_SCRIPT}" charset="utf-8"></script>
    <!--<![endif]-->
    <script src="{$smarty.const.JQUERYUI_SCRIPT}" type="text/javascript" charset="utf-8"></script>

    <!-- elfinder core -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.version.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/jquery.elfinder.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.mimetypes.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.options.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.options.netmount.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.history.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.command.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.resources.js"></script>

    <!-- elfinder ui -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/button.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/contextmenu.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/cwd.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/dialog.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/fullscreenbutton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/navbar.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/navdock.js"></script>
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
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/empty.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/extract.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/forward.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/fullscreen.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/getfile.js"></script>
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/help.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/help.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/hidden.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/hide.js"></script>
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
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/opennew.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/paste.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/places.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/preference.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/quicklook.js"></script>
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/quicklook.plugins.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/quicklook.plugins.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/reload.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/rename.js"></script>
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/resize.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/resize.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/restore.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/rm.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/search.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/selectall.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/selectinvert.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/selectnone.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/sort.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/undo.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/up.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/upload.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/view.js"></script>

    <!-- elfinder languages -->
    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/i18n/elfinder.{substr($smarty.const.LOCALE,0,2)}.js"></script>*}
    <script src="{$smarty.const.PATH_RELATIVE}framework/modules/file/connector/i18n/elfinder.{substr($smarty.const.LOCALE,0,2)}.js"></script>

    <!-- Extra contents editors (OPTIONAL) -->
   	<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/extras/editors.default.js"></script>
{*    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/extras/quicklook.googledocs.js"></script>*}

    <!-- elfinder custom extenstions -->
    <!--<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/extensions/jplayer/elfinder.quicklook.jplayer.js"></script>-->
</head>
<body{if !bs3() && !bs4() && !bs5()} class="exp-skin"{/if}>

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
            var $autoLoadCSS = "{/literal}{$smarty.const.URL_FULL|cat:"external/elFinder"|cat:$smarty.const.ELFINDER_THEME|cat:"/css/theme.css"}{literal}";

            var elf = $('#elfinder').elfinder({
                baseUrl: EXPONENT.PATH_RELATIVE + 'external/elFinder/',  // main URL
                url: EXPONENT.PATH_RELATIVE + 'framework/modules/file/connector/elfinder.php',  // connector URL
                urlUpload: EXPONENT.URL_FULL + 'framework/modules/file/connector/elfinder.php',  // connector full URL
                cssAutoLoad: $autoLoadCSS,
                themes : {
                    "default" : {
                      "name":"Default OS/X",
                      "cssurls":"{/literal}{$smarty.const.URL_FULL|cat:"external/elFinder/css/theme.css"}{literal}",
                      "author":"Naoki Sawada",
                      // "email":"dev@std42.ru",
                      "license":"3-clauses BSD license",
                      "link":"https://github.com/Studio-42/elFinder",
                      "image":"{/literal}{$smarty.const.PATH_RELATIVE|cat:"external/elFinder/themes/default.png"}{literal}",
                      "description":"Default theme shipped with elFinder"
                    },
                    "bootstrap" : {
                      "name":"Bootstrap",
                      "cssurls":"{/literal}{$smarty.const.URL_FULL|cat:"external/elFinder/themes/bootstrap/css/theme.css"}{literal}",
                      "author":"Dennis Suitters StudioJunkyard",
                      // "email":"Author Email",
                      "license":"MIT",
                      "link":"https://github.com/DiemenDesign/LibreICONS/tree/master/themes/elFinder",
                      "image":"{/literal}{$smarty.const.PATH_RELATIVE|cat:"external/elFinder/themes/bootstrap/bootstrap.png"}{literal}",
                      "description":"Bootstrap like theme for elFinder LibreICONS SVG Edition"
                    },
                    "dark-slim" : {
                      "name":"Dark Slim",
                      "cssurls":"{/literal}{$smarty.const.URL_FULL|cat:"external/elFinder/themes/dark-slim/css/theme.css"}{literal}",
                      "author":"John Fort",
                      "email":"support@fortcms.ru",
                      "license":"MIT",
                      "link":"https://github.com/johnfort/elFinder.themes",
                      "image":"{/literal}{$smarty.const.PATH_RELATIVE|cat:"external/elFinder/themes/dark-slim/dark-slim.png"}{literal}",
                      "description":"Dark Slim theme for elFinder"
                    },
                    "material" : {
                      "name":"Material",
                      "cssurls":"{/literal}{$smarty.const.URL_FULL|cat:"external/elFinder/themes/Material/css/theme.css"}{literal}",
                      "author":"Róbert Kelčák {RobiNN}",
                      // "email":"Author Email",
                      "license":"MIT",
                      "link":"https://github.com/RobiNN1/elFinder-Material-Theme",
                      "image":"{/literal}{$smarty.const.PATH_RELATIVE|cat:"external/elFinder/themes/Material/Material.png"}{literal}",
                      "description":"Material Theme for elFinder"
                    },
                    "material-gray" : {
                      "name":"Material Gray",
                      "cssurls":"{/literal}{$smarty.const.URL_FULL|cat:"external/elFinder/themes/Material-Gray/css/theme.css"}{literal}",
                      "author":"Róbert Kelčák {RobiNN}",
                      // "email":"Author Email",
                      "license":"MIT",
                      "link":"https://github.com/RobiNN1/elFinder-Material-Theme",
                      "image":"{/literal}{$smarty.const.PATH_RELATIVE|cat:"external/elFinder/themes/Material-Gray/Material-Gray.png"}{literal}",
                      "description":"Material (Gray) Theme for elFinder"
                    },
                    "material-light" : {
                      "name":"Material Light",
                      "cssurls":"{/literal}{$smarty.const.URL_FULL|cat:"external/elFinder/themes/Material-Light/css/theme.css"}{literal}",
                      "author":"Róbert Kelčák {RobiNN}",
                      // "email":"Author Email",
                      "license":"MIT",
                      "link":"https://github.com/RobiNN1/elFinder-Material-Theme",
                      "image":"{/literal}{$smarty.const.PATH_RELATIVE|cat:"external/elFinder/themes/Material-Light/Material-Light.png"}{literal}",
                      "description":"Material (Light) Theme for elFinder"
                    },
                    "moono" : {
                      "name":"Moono",
                      "cssurls":"{/literal}{$smarty.const.URL_FULL|cat:"external/elFinder/themes/moono/css/theme.css"}{literal}",
                      "author":"Lawrence Okoth-Odida",
                      // "email":"Author Email",
                      "license":"MIT",
                      "link":"https://github.com/lokothodida/elfinder-theme-moono",
                      "image":"{/literal}{$smarty.const.PATH_RELATIVE|cat:"external/elFinder/themes/moono/moono.png"}{literal}",
                      "description":"A theme for elFinder that mimics CKEditor's Moono skin."
                    },
                    "windows-10" : {
                      "name":"Windows 10",
                      "cssurls":"{/literal}{$smarty.const.URL_FULL|cat:"external/elFinder/themes/windows-10/css/theme.css"}{literal}",
                      "author":"Lawrence Okoth-Odida",
                      // "email":"Author Email",
                      "license":"MIT",
                      "link":"https://github.com/lokothodida/elfinder-theme-windows-10",
                      "image":"{/literal}{$smarty.const.PATH_RELATIVE|cat:"external/elFinder/themes/windows-10/windows-10.png"}{literal}",
                      "description":"An elFinder theme that mimics Windows 10's user interface."
                    },
                },
                theme : 'default',
                // commands : [
                //     'pixlr',
                //     'open', 'opendir', 'reload', 'home', 'up', 'back', 'forward', 'getfile', 'quicklook',
                //     'download', 'rm', 'duplicate', 'rename', 'mkdir', 'mkfile', 'upload', 'copy',
                //     'cut', 'paste', 'edit', 'extract', 'archive', 'search', 'info', 'view', 'help',
                //     'resize', 'sort', 'netmount', 'netunmount', 'places', 'chmod', 'links'
                // ],
                commandsOptions : {
                    getfile : {
                        // allow to return multiple files info
                        multiple : {/literal}{if $smarty.get.update!='noupdate' && $smarty.get.update!='ck' && $smarty.get.update!='tiny'}true{else}false{/if}{literal},
                    },
                    // "quicklook" command options. For additional extensions
                    quicklook : {
                        autoplay : false,
                        // to enable preview with Google Docs Viewer
                        googleDocsMimes : ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/postscript', 'application/rtf'],
                        // to enable CAD-Files and 3D-Models preview with sharecad.org
                        sharecadMimes : ['image/vnd.dwg', 'image/vnd.dxf', 'model/vnd.dwf', 'application/vnd.hp-hpgl', 'application/plt', 'application/step', 'model/iges', 'application/vnd.ms-pki.stl', 'application/sat', 'image/cgm', 'application/x-msmetafile'],
                        {/literal}{if $smarty.const.FM_MSOFFICE}
                        // to enable preview with Microsoft Office Online Viewer
                        // these MIME types override "googleDocsMimes"
                        officeOnlineMimes : ['application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.presentation']
                        {/if}{literal}
                    },
                    // help dialog tabs
                    help : {
                        {/literal}{if $smarty.const.DEVELOPMENT}
                        view : ['about', 'shortcuts', 'help', 'integrations', 'debug'],
                        {else}
                        view : ['about', 'shortcuts', 'help'],
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
                        ['home', 'back', 'forward', 'up', 'reload'],
                        //['netmount'],       // removed
                        ['mkdir', 'mkfile', 'upload'],
                        ['open', 'download', 'getfile'],
                        ['undo', 'redo'],
                        ['copy', 'cut', 'paste', 'rm', 'empty', 'hide'],
                        ['duplicate', 'rename', 'edit', 'resize', 'chmod'],
                        ['selectall', 'selectnone', 'selectinvert'],
                        ['quicklook', 'info'],
                        ['extract', 'archive'],
                        ['search'],
                        ['view', 'sort'],
                        ['links', 'places'],   // links added
                        ['preference', 'help'],
                        ['fullscreen']
                    ],
                    // toolbar extra options
//                    toolbarExtra : {
//                        // also displays the text label on the button (true / false)
//                        displayTextLabel: false,
//                        // Exclude `displayTextLabel` setting UA type
//                        labelExcludeUA: ['Mobile'],
//                        // auto hide on initial open
//                        autoHideUA: ['Mobile']
//                    },
                    // directories tree options
                    tree : {
                        // expand current root on init
//                        openRootOnLoad : true,
                        // expand current work directory on open
//                        openCwdOnOpen  : true,
                        // auto load current dir parents
//                        syncTree : true,
                        // Maximum number of display of each child trees
                        // The tree of directories with children exceeding this number will be split
//                        subTreeMax : 100,
                        // Numbar of max connctions of subdirs request
                        subdirsMaxConn : 3,
                        // Number of max simultaneous processing directory of subdirs
//                        subdirsAtOnce : 5
                    },
                    // navbar options
                    navbar : {
//                        minWidth : 150,
//                        maxWidth : 500,
                        autoHideUA: ['Mobile']
                    },
                    cwd : {
                        // display parent folder with ".." name :)
//                        oldSchool : false,
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
                soundPath: '{/literal}{$smarty.const.PATH_RELATIVE}external/elFinder/sounds{literal}',
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
