{*
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
 *}

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{'File Manager'|gettext}  |  Exponent CMS</title>

    <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}exponent.js2.php"></script>
    <script src="{$smarty.const.JQUERY_SCRIPT}" type="text/javascript" charset="utf-8"></script>
    <script src="{$smarty.const.JQUERYUI_SCRIPT}" type="text/javascript" charset="utf-8"></script>

    {*<link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/jquery/css/smoothness/jquery-ui.css" type="text/css" media="screen" title="no title" charset="utf-8">*}
    <link rel="stylesheet" href="{$smarty.const.JQUERYUI_CSS}" type="text/css" media="screen" title="no title" charset="utf-8">

    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/common.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/dialog.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/toolbar.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/navbar.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/statusbar.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/contextmenu.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/cwd.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/quicklook.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/commands.css" type="text/css">
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/fonts.css" type="text/css">

    {*<link rel="stylesheet" type="text/css" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/elfinder.min.css">*}
    <link rel="stylesheet" href="{$smarty.const.PATH_RELATIVE}external/elFinder/css/theme.css" type="text/css">

    <!-- elfinder core -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.version.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/jquery.elfinder.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.resources.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.options.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.history.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elFinder.command.js"></script>

    <!-- elfinder ui -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/overlay.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/workzone.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/navbar.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/dialog.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/tree.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/cwd.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/toolbar.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/button.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/uploadButton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/viewbutton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/searchbutton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/sortbutton.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/panel.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/contextmenu.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/path.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/stat.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/ui/places.js"></script>

    <!-- elfinder commands -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/back.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/forward.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/reload.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/up.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/home.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/copy.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/cut.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/paste.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/open.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/rm.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/info.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/duplicate.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/rename.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/help.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/getfile.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/mkdir.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/mkfile.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/upload.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/download.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/edit.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/quicklook.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/quicklook.plugins.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/extract.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/archive.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/search.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/view.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/resize.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/sort.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/netmount.js"></script>
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/commands/pixlr.js"></script>

    {*<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/elfinder.min.js"></script>*}

    <!-- elfinder languages -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/i18n/elfinder.en.js"></script>

    <!-- elfinder dialog -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/jquery.dialogelfinder.js"></script>

    <!-- elfinder 1.x connector API support -->
    <script src="{$smarty.const.PATH_RELATIVE}external/elFinder/js/proxy/elFinderSupportVer1.js"></script>

    <!-- elfinder custom extenstions -->
    <!--<script src="{$smarty.const.PATH_RELATIVE}external/elFinder/extensions/jplayer/elfinder.quicklook.jplayer.js"></script>-->
    <script src="{$smarty.const.PATH_RELATIVE}external/editors/tinymce/tinymce.min.js"></script>
</head>
<body class=" exp-skin">

<div id="elfinder"></div>

{script unique="picker" jquery=jqueryui}
    {literal}
        // Helper function to get parameters from the query string for CKEditor.
        function getUrlParam(paramName) {
            var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
            var match = window.location.search.match(reParam) ;

            return (match && match.length > 1) ? match[1] : '' ;
        }

        // Helper function to get parameters from the query string for TinyMCE.
        var FileBrowserDialogue = {
            init: function() {
                // Here goes your code for setting your custom things onLoad.
            },
            mySubmit: function (URL) {
                // pass selected file path to TinyMCE
                top.tinymce.activeEditor.windowManager.getParams().setUrl(URL);

                // close popup window
                top.tinymce.activeEditor.windowManager.close();
            }
        }

        $().ready(function() {
            var funcNum = getUrlParam('CKEditorFuncNum');

            var elf = $('#elfinder').elfinder({
                url: '{/literal}{$smarty.const.PATH_RELATIVE}{literal}framework/modules/file/connector/elfinder.php',  // connector URL
                commandsOptions : {
                    edit : {
                        mimes : ['text/plain', 'text/html', 'text/javascript'],
                        editors : [
                            {
                                mimes : ['text/html'],
                                load : function(textarea) {
                                    tinyMCE.execCommand('mceAddEditor', true, textarea.id);
                                },
                                close : function(textarea, instance) {
                                    tinyMCE.execCommand('mceRemoveEditor', false, textarea.id);
                                },
                                save : function(textarea, editor) {
                                    textarea.value = tinyMCE.get(textarea.id).selection.getContent({format : 'html'});
                                    tinyMCE.execCommand('mceRemoveEditor', false, textarea.id);
                                }
                            }
                        ]
                    },
                    getfile : {
                        // allow to return multiple files info
                        multiple : {/literal}{if $smarty.get.update!='noupdate' && $smarty.get.update!='ck' && $smarty.get.update!='tiny'}true{else}false{/if}{literal},
                    },
                    // "quicklook" command options. For additional extensions
                    quicklook : {
                        autoplay : false,
                    },
                    // help dialog tabs
                    help : {
                        view : ['about', 'shortcuts']
                    }
                },
                handlers : {
					getfile : function(e) {
						console.log(e.data.files)
					}
				},
                {/literal}{if $filter=='image'}{literal}
                onlyMimes : ['image'],
                {/literal}{/if}{literal}
                defaultView : '{/literal}{if $smarty.const.FM_THUMBNAILS}icons{else}list{/if}{literal}',
                // dateFormat : '{/literal}{$smarty.const.DISPLAY_DATE_FORMAT}{literal}',
                {/literal}
              	ui : ['toolbar', 'places', 'tree', 'path', 'stat'],
                {if $update=='ck'}
                    {$w = 38}
                    {$h = 112}
                {else}
                    {$w = 18}
                    {$h = 20}
                {/if}
                {literal}
                width : {/literal}{$smarty.const.FM_WIDTH - $w}{literal},
                height : {/literal}{$smarty.const.FM_HEIGHT - $h}{literal},
                resizable: false,
                {/literal}{if $update!='noupdate'}{literal}
                getFileCallback : function(file) {
                    {/literal}{if $update=='ck'}{literal}
                    window.opener.CKEDITOR.tools.callFunction(funcNum, file.url);
                    window.close();
                    {/literal}{elseif $update=='tiny'}{literal}
                    FileBrowserDialogue.mySubmit(file.url); // pass selected file path to TinyMCE
                    {/literal}{else}{literal}
                    if ((file.length) == 1) {
                        myfile = file[0];
                        window.opener.EXPONENT.passBackFile{/literal}{$update}{literal}(myfile.path);
                    } else {
                        var batchIDs = {};
                        for (var i=0; i<file.length; i++) {
                            myfile = file[i];
                            batchIDs[i] = myfile.path;
                        }
                        window.opener.EXPONENT.passBackBatch{/literal}{$update}{literal}(batchIDs);
                    }
                    window.close();
                    {/literal}{/if}{literal}
                },
                {/literal}{/if}{literal}
            }).elfinder('instance');
        });
    {/literal}
{/script}
</body>
</html>
