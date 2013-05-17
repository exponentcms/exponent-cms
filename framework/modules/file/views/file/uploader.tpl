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

<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{'File Uploader'|gettext}  |  Exponent CMS</title>
    {css unique="uploader" corecss="msgq,button,tables,common,admin-global" link="`$asset_path`css/filemanager.css"}

    {/css}
    <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}exponent.js2.php"></script>
    <script type="text/javascript" src="{$smarty.const.YUI3_RELATIVE}yui/yui-min.js"></script>
    {script unique="picker" src="`$smarty.const.JS_RELATIVE`exp-flashdetector.js"}

    {/script}
</head>
<body class="exp-skin">
    <div id="exp-uploader">
        <h1>{"Upload Files"|gettext}</h1>
        <div id="actionbar">
            <div id="selectFilesButtonContainer"></div>
            <a id="selectLink" class="select awesome small green" href="#"><span>{'Select Files'|gettext}</span></a>
            <a id="uploadLink" class="upload awesome small green" href="#"><span>{"Upload Files"|gettext}</span></a>
            <a id="backlink" class="back awesome small green" href="{link action=picker ajax_action=1 ck=$smarty.get.ck update=$smarty.get.update fck=$smarty.get.fck}{if $smarty.const.SEF_URLS}?{else}&{/if}CKEditor={$smarty.get.CKEditor}&CKEditorFuncNum={$smarty.get.CKEditorFuncNum}&langCode={$smarty.get.langCode}"><span>{'Back to Manager'|gettext}</span></a>
        </div>
        <div class="info-header clearfix">
            <div id="noflash"></div>
            <div class="related-actions">
                {help text="Get Help with"|gettext|cat:" "|cat:("Uploading Files"|gettext) module="upload-files"}
            </div>
            {control type=dropdown name="select_folder" label="Select the Upload Folder"|gettext items=$cats}
            <input id="folder" type="text" name="folder" value="" size="20" class="control text" style="display: inline;margin-left: 12px;" onkeydown="if (event.keyCode == 13) document.getElementById('createLink').click()" />
            <a id="createLink" class="add awesome small green" style="height: 18px;display:inline;margin-left: 5px;" href="#"><span>{'Create New Folder'|gettext}</span></a>{br}
            <div id="resizeControl" class="control checkbox">
                <input id="resize" type="checkbox" name="resize" value="1" class="checkbox" />
                <div class="label" style="width:auto; display:inline;">{'Downsize Images to a Max Height or Width of'|gettext}:</div>
                <input id="max_width" type="text" name="max_width" size="4" class="text " value="{$smarty.const.UPLOAD_WIDTH}" />
            </div>
        </div>
        {messagequeue}
        <div id="filelist">
            <table id="filenames2" class="exp-skin-table">
                <thead>
                    <tr><th>{'File name'|gettext}</th><th>{'File size'|gettext}</th><th>{'Percent uploaded'|gettext}</th><th>&#160;</th></tr>
                    <tr id="nofiles">
                        <td colspan="4" id="ddmessage">
                            <strong>{'No files selected.'|gettext}</strong>
                        </td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div id="uploaderContainer">
        <span id="btnProgress"></span>
        <span id="btnStop"></span>
        <span id="overallProgress"></span>
    </div>

{script unique="uploader2" yui3="1"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use("uploader","io",'json-parse', function(Y) {
    Y.one("#overallProgress").set("text", "Uploader type: " + Y.Uploader.TYPE);
    var usr = {/literal}{obj2json obj=$user}{literal}; //user
    var uploadBtn = Y.one("#uploadLink");
    var createBtn = Y.one("#createLink");
    var simLimit = '{/literal}{$smarty.const.FM_SIMLIMIT}{literal}';
    if (simLimit == 0) simLimit = 3;

    if (Y.Uploader.TYPE != "none" && !Y.UA.ios) {
        var uploader = new Y.Uploader({
                                      width: "78px",
//                                      height: "35px",
                                      multipleFiles: true,
//                                      swfURL: EXPONENT.YUI3_RELATIVE + "uploader/assets/flashuploader.swf?t=" + Math.random(),
                                      swfURL: EXPONENT.YUI3_URL + "uploader/assets/flashuploader.swf",
                                      uploadURL: EXPONENT.PATH_RELATIVE + "index.php?controller=file&action=upload&ajax_action=1",
                                      simLimit: simLimit,
                                      withCredentials: false,
                                      selectFilesButton: Y.one("#selectLink")
                                     });
        var uploadDone = false;

        if (Y.Uploader.TYPE == "html5") {
            uploader.set("dragAndDropArea", "body");

            Y.one("#ddmessage").setHTML("<strong>{/literal}{'Drag and drop files here.'|gettext}{literal}</strong>");

            uploader.on(["dragenter", "dragover"], function (event) {
                var ddmessage = Y.one("#ddmessage");
                if (ddmessage) {
                    ddmessage.setHTML("<strong>{/literal}{'Files detected, drop them here!'|gettext}{literal}</strong>");
                    ddmessage.addClass("yellowBackground");
                }
            });

            uploader.on(["dragleave", "drop"], function (event) {
                var ddmessage = Y.one("#ddmessage");
                if (ddmessage) {
                    ddmessage.setHTML("<strong>{/literal}{'Drag and drop files here.'|gettext}{literal}</strong>");
                    ddmessage.removeClass("yellowBackground");
                }
            });
        }

        uploader.render("#selectFilesButtonContainer");

        var rowcolor = 'odd';
        uploader.after("fileselect", function (event) {
            var fileList = event.fileList;
            var fileTable = Y.one("#filenames2 tbody");
            if (fileList.length > 0 && Y.one("#nofiles") && Y.Uploader.TYPE != "html5") {
                Y.one("#nofiles").remove();
            }

            if (uploadDone) {
                uploadDone = false;
                fileTable.setHTML("");
            }

            var perFileVars = {};

            Y.each(fileList, function (fileInstance) {
//                fileTable.append("<tr id='" + fileInstance.get("id") + "_row" + "'>" +
//                                    "<td class='filename'>" + fileInstance.get("name") + "</td>" +
//                                    "<td class='filesize'>" + fileInstance.get("size") + "</td>" +
//                                    "<td class='percentdone'>{/literal}{'Hasn\'t started yet'|gettext}{literal}</td>" +
//                                    "<td class='serverdata'>&nbsp;</td>");
                    rowcolor = (rowcolor=='odd')?'even':'odd';
                    var output = "<tr class=\""+rowcolor+"\" id='" + fileInstance.get("id") + "_row" + "'><td class='filename'>" + fileInstance.get("name") + "</td><td class='filesize'>" +
                                    (Math.round(fileInstance.get("size")/1048576*100000)/100000).toFixed(2) + "</td><td class='percentdone'><div id='div_" +
                                fileInstance.get("id") + "' class='progressbars'></div></td><td class='serverdata'><a href='#' class='delete' id='" + fileInstance.get("id") + "_delete" + "' title='{/literal}{'Remove file from the upload list'|gettext}{literal}'>{/literal}{'Remove'|gettext}{literal}</a></td></tr>";
                fileTable.append(output);
                var cat = Y.one('#select_folder');
                if (cat == null) {
                    catvalue = 0;
                } else {
                    catvalue = cat.get('value');
                }
                var resize = Y.one('#resize').get('value');
                var widthtxt = Y.one('#max_width');
                if (widthtxt == null) {
                    width = 0;
                } else {
                    width = widthtxt.get('value');
                }
                perFileVars[fileInstance.get("id")] = {filename:fileInstance.get("name"),usrid:usr['id'],cat:catvalue,resize:resize,max_width:width};
            });
            uploader.set("postVarsPerFile", Y.merge(uploader.get("postVarsPerFile"), perFileVars));
        });

        uploader.on("uploadprogress", function (event) {
            var fileRow = Y.one("#" + event.file.get("id") + "_row");
            var prog = Math.round(100 * (event.bytesLoaded / event.bytesTotal));
            var progbar = "<div style='width:90%;background-color:#CCC;'><div style='height:12px;padding:3px;font-size:10px;color:#fff;background-color:"+((prog>90)?'#fad00e':'#b30c0c')+";width:" + prog + "%;'>" + prog + "%</div></div>";
            fileRow.one(".percentdone").setHTML(progbar);
            fileRow.one(".serverdata").setHTML('');
        });

        uploader.on("uploadstart", function (event) {
            uploader.set("enabled", false);
            uploadBtn.addClass("yui3-button-disabled");
            uploadBtn.detach("click");
            Y.all("#filenames2 tbody .delete").remove();  // we can't use the remove file action at this point
            Y.all("#filenames2 tbody .serverdata").setHTML("<a href='#' class='stop-one' id='stop-one' title='{/literal}{'Cancel File Uploading'|gettext}{literal}'>{/literal}{'Cancel'|gettext}{literal}</a>");
            Y.one("#btnProgress").setHTML("<a href='#' class='pause' id='pause-upload' title='{/literal}{'Pause File Uploading'|gettext}{literal}'>{/literal}{'Pause'|gettext}{literal}</a>");
            Y.one("#btnStop").setHTML("<a href='#' class='stop' id='stop-upload' title='{/literal}{'Cancel File Uploading'|gettext}{literal}'>{/literal}{'Cancel All'|gettext}{literal}</a>");
        });

        uploader.on("uploadcomplete", function (event) {
            var fileRow = Y.one("#" + event.file.get("id") + "_row");
            var progbar = "<div style='width:90%;background-color:#CCC;'><div style='height:12px;padding:3px;font-size:10px;color:#fff;background-color:#2f840a;width:100%;'><img src='"+EXPONENT.PATH_RELATIVE+"framework/core/assets/images/accepted.png' style=\"float:right; margin:-3px -24px 0 0\">100%</div></div>";
            fileRow.one(".percentdone").setHTML(progbar);
            fileRow.one(".serverdata").setHTML(event.data);
        });

        uploader.on("totaluploadprogress", function (event) {
            Y.one("#overallProgress").setHTML("{/literal}{'Total uploaded:'|gettext}{literal} <strong>" + event.percentLoaded + "%" + "</strong>");
        });

        uploader.on("alluploadscomplete", function (event) {
            uploader.set("enabled", true);
            uploader.set("fileList", []);
            uploadBtn.removeClass("yui3-button-disabled");
            uploadBtn.on("click", function () {
            if (!uploadDone && uploader.get("fileList").length > 0) {
                    uploader.uploadAll();
                }
            });
            Y.one("#overallProgress").set("text", "{/literal}{'Uploads complete!'|gettext}{literal}");
            Y.one("#btnProgress").setHTML("");
            Y.one("#btnStop").setHTML("");
            uploadDone = true;
        });

        uploadBtn.on("click", function () {
            if (!uploadDone && uploader.get("fileList").length > 0) {
               uploader.uploadAll();
            }
        });

        Y.one("#filenames2 tbody").delegate('click', function(e) {
            e.halt();
            var fileRow = e.target.ancestor('tr').remove();
        },'.delete');

        Y.one("#btnProgress").delegate('click', function(e) {
            e.halt();
            uploader.queue.pauseUpload();
            Y.one("#btnProgress").setHTML("<a href='#' class='resume' id='resume-upload' title='{/literal}{'Resume File Uploading'|gettext}{literal}'>{/literal}{'Resume'|gettext}{literal}</a>");
            Y.log('All File Uploading Paused');
        },'a.pause');

        Y.one("#filenames2 tbody").delegate('click', function(e) {
            e.halt();
            var fileRow = e.target.ancestor('tr');
            theFilename = fileRow.one('.filename').get('text');
            theQ = uploader.queue.queuedFiles;
            if (theQ != null) {
                for (i=0; i<theQ.length; i++) {
                    if (theQ[i].get('name') == theFilename) {
                        theFile = theQ[i];
                        break;
                    }
                }
            }
            uploader.queue.cancelUpload(theFile);
            fileRow.one(".percentdone").setHTML("");
            fileRow.one(".serverdata").setHTML("{/literal}{'Cancelled'|gettext}{literal}");
            Y.log('File Upload Cancelled');
        },'a.stop-one');

        Y.one("#btnProgress").delegate('click', function(e) {
            e.halt();
            uploader.queue.restartUpload();
            Y.one("#btnProgress").setHTML("<a href='#' class='pause' id='pause-upload' title='{/literal}{'Pause File Uploading'|gettext}{literal}'>{/literal}{'Pause'|gettext}{literal}</a>");
            Y.log('All File Uploading Resumed');
        },'a.resume');

        Y.one("#btnStop").delegate('click', function(e) {
            e.halt();
            uploadDone = true;
            uploader.queue.cancelUpload();
            uploader.set("enabled", true);
            uploader.set("fileList", []);
            uploadBtn.removeClass("yui3-button-disabled");
            Y.one("#overallProgress").set("text", "{/literal}{'Uploading Cancelled!'|gettext}{literal}");
            Y.one("#btnProgress").setHTML("");
            Y.one("#btnStop").setHTML("");
            Y.log('All File Uploading Cancelled');
        },'a.stop');

    } else {
        Y.one("#uploaderContainer").set("text", "{/literal}{'We are sorry, but to use the uploader, you either need a browser that support HTML5 or have the Flash player installed on your computer.'|gettext}{literal}");
    }

    Y.all('.msg-queue .close').on('click',function(e){
        e.halt();
        e.target.get('parentNode').remove();
    });

    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Create Virtual Folder'},
    			arguments : { 'X-Transaction': 'Create Virtual Folder'}
    		};

	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=file&action=createFolder&ajax_action=1";

	var handleSuccess = function(ioId, o){
//		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "sermon nav");

        if(o.responseText){
            var folderlist = Y.one('#select_folder'); // the views dropdown
            var folders = Y.JSON.parse(o.responseText);
            folderlist.set('innerHTML', '');
            el = Y.Node.create('<option value="0">{/literal}{"Select a Folder"|gettext}{literal}</option>');
            for (var folder in folders) {
                el = document.createElement('option');
                el.appendChild(document.createTextNode(folders[folder]));
                el.setAttribute('value', folder);
                folderlist.appendChild(el);
            }
            for (var folder in folders) {
                if (folders[folder].toLowerCase() == Y.one('#folder').get('value').toLowerCase()) {
                    folderlist.set('value',folder);
                }
            }

            Y.one('#folder').set('value','');
        } else {
//            Y.one('#sermon.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "sermon nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    createBtn.on("click", function (e) {
        e.halt();
        cfg.data = "folder="+Y.one('#folder').get('value');
        var request = Y.io(sUrl, cfg);
//        sermon.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Sermon"|gettext}{literal}</div>'));
    });

    if(!FlashDetect.installed) {
        Y.one('#noflash').append('{/literal}{'You need to have Adobe Flash Player installed in your browser to upload files.'|gettext}{literal}<br /><a href="http://get.adobe.com/flashplayer/" target="_blank">{/literal}{'Download it from Adobe.'|gettext}{literal}</a>');
    }

});
{/literal}
{/script}

</body>
</html>
