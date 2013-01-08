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
    <title>{'File Manager'|gettext}  |  Exponent CMS</title>
    {css unique="picker" corecss="msgq,button,admin-global" link="`$asset_path`css/filemanager.css"}

    {/css}
    <script type="text/javascript" src="{$smarty.const.YUI3_RELATIVE}yui/yui-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}exponent.js2.php"></script>
    {script unique="picker" src="`$smarty.const.FLOWPLAYER_RELATIVE`flowplayer-`$smarty.const.FLOWPLAYER_MIN_VERSION`.min.js"}

    {/script}
</head>
<body class=" exp-skin">
<div id="filemanager">
	<h1>{'File Manager'|gettext}</h1>
    {messagequeue}
	<div class="info-header">
		<div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Managing Files"|gettext) module="file-manager"}
            <blockquote>
               {'Click the cell to change Folder, Title, or alt.'|gettext}
           </blockquote>
		</div>
		<div id="autocomplete">
			<label for="dt_input">{'Filter by Filename, Title, or alt'|gettext}:</label>
			<input id="dt_input" type="text" />
            <a id="clear_filter" href="#" title="{'Clear the Filter'|gettext}">{img src="`$smarty.const.ICON_RELATIVE`delete.png"}</a>
		</div>
		<div id="dt_ac_container"></div>
        {control type=dropdown name="select_folder" label="Select the Folder to View"|gettext items=$cats onchange="EXPONENT.switch_folder(this.value)"}
    </div>

    <div id="pagelinks">&#160;</div>
    <div id="dynamicdata">
    
    </div>
    <div id="actionbar">
        <a class="upload awesome green small" href="{link action=uploader ajax_action=1 ck=$smarty.get.ck update=$smarty.get.update fck=$smarty.get.fck}{if $smarty.const.SEF_URLS}?{else}&{/if}CKEditor={$smarty.get.CKEditor}&CKEditorFuncNum={$smarty.get.CKEditorFuncNum}&langCode={$smarty.get.langCode}"><span>{"Upload Files"|gettext}</span></a>
    </div>

    <div id="infopanel">
        <div class="hd"></div>
        <div class="bd"></div>
    </div>
    {if $permissions.manage == 1}
        {br}
        {if $smarty.get.update!='noupdate' && $smarty.get.update!='fck'}
            <a id="useselected" style="float:right;" class="use awesome medium green" href="#"><span>{'Use Selected Files'|gettext}</span></a>
        {/if}
        <a id="addlink" class="add awesome medium green" href="{link action=adder ajax_action=1 ck=$smarty.get.ck update=$smarty.get.update}"><span>{'Add Existing Files'|gettext}</span></a>&#160;&#160;
        <a id="deletelink" class="delete awesome medium red" href="{link action=deleter ajax_action=1 ck=$smarty.get.ck update=$smarty.get.update}"><span>{'Delete Missing Files'|gettext}</span></a>
        {br}{br}
    {/if}
</div>

    {*FIXME convert to yui3*}
{script unique="picker"}
{literal}
// this.moveTo(1,1);
// this.resizeTo(screen.width,screen.height);
YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event','yui2-container','yui2-json','yui2-datasource','yui2-connection','yui2-autocomplete','yui2-element','yui2-paginator','yui2-datatable', 'yui2-calendar', function(Y) {
    var YAHOO=Y.YUI2;
    EXPONENT.fileManager = function() {
//        var queryString = '&results=50&output=json'; //autocomplete query
        var fck = {/literal}{if $smarty.get.fck}{$smarty.get.fck}{else}0{/if}{literal}; //are we coming from FCK as the window launcher?
        var usr = {/literal}{obj2json obj=$user}{literal}; //user
        var update = "{/literal}{if $smarty.get.update}{$smarty.get.update}{else}0{/if}{literal}"; //user
        var thumbnails = {/literal}{$smarty.const.FM_THUMBNAILS}{literal};
        var myDataSource = null;
        var myDataTable = null;

        var batchIDs = {};

        function getUrlParam(paramName) {
            var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
            var match = window.location.search.match(reParam) ;

            return (match && match.length > 1) ? match[1] : '' ;
        }

    	routBackToSource = function (fo, fi) {
    		var funcNum = getUrlParam('CKEditorFuncNum');
    		var fileUrl = fo;
    		var ck = getUrlParam('ck');
    		{/literal}
    		{if $update|strstr:"fck"}
    		window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
    		{else}
    		window.opener.EXPONENT.passBackFile{$update}(fi);
    		{/if}
    		{literal}
    	}
    

        batchBack = function () {
            window.opener.EXPONENT.batchAddFiles.{/literal}{$update}{literal}(batchIDs);
            window.close();
        }

        updateBatch = function (e) {
            if (e.target.get('checked')) {
                batchIDs[e.target.get('id').substring(2)] = myDataTable.getRecord(e.target.ancestor('tr')._node).getData();
            } else {
                delete batchIDs[e.target.get('id').substring(2)];
            }
            Y.log(batchIDs);
        }

//        Y.on('click',updateBatch,'.batchcheck');
        Y.one('#dynamicdata').delegate('click',updateBatch,'.batchcheck');
        Y.on('click', batchBack, '#useselected');

        // set up the info panel
        var infopanel =  new YAHOO.widget.Panel(
            "infopanel", 
            { 
                width:"800px", 
                height:"500px", 
                fixedCenter:true,
                modal:true,
                close:true,
                visible:false, 
                constraintoviewport:true 
            } 
        );
        infopanel.render();
        infopanel.subscribe('hide',function(event){
            flowplayer("a.player", EXPONENT.FLOWPLAYER_RELATIVE+"flowplayer-"+EXPONENT.FLOWPLAYER_VERSION+".swf",
         				{
         					wmode: 'opaque',
         					clip: {
         						autoPlay: false
                             }
         				}
         			).close();
        });
        // handler for showing file information
        var showFileInfo = function(oRecordData) {
            var owner = (oRecordData.user.username!="") ? ' '+"{/literal}{"owned by"|gettext}{literal}"+' '+oRecordData.user.firstname+' '+oRecordData.user.lastname+' ('+oRecordData.user.username+')' : "";

            infopanel.setHeader(oRecordData.filename+owner);
            filetype = oRecordData.filename.replace(/^\s|\s$/g, "");
            ismedia = filetype.match(/([^\/\\]+)\.(mp3|flv|f4v)$/i)
            if (oRecordData.is_image==1) {
    	        var oFile = '<img src="'+oRecordData.url+'" onError="this.src=\''+EXPONENT.PATH_RELATIVE+'/framework/core/assets/images/default_preview_notfound.gif\'">';
            } else if (ismedia){
                var oFile = '<a href="'+oRecordData.url+'" style="display:block;width:450px;height:360px;" class="player"></a>';
            } else {
                var oFile = '<img src="'+EXPONENT.PATH_RELATIVE+'framework/modules/file/assets/images/general.png">' ;
            };
            if (oRecordData.cat==null) {
                foldercat = 'Root';
            } else {
                foldercat = oRecordData.cat;
            }
            var posteddate = new Date(oRecordData.posted*1000);
            if (oRecordData.is_image==1) {
                var imageInfo = '</td></tr><tr class="even"><td><span>{/literal}{"Image Height"|gettext}{literal}:</span>'+oRecordData.image_height+
                                '</td></tr><tr class="odd"><td><span>{/literal}{"Image Width"|gettext}{literal}:</span>'+oRecordData.image_width;
            } else {
                var imageInfo = '</td></tr><tr class="even"><td><span>{/literal}{"Image Height"|gettext}{literal}:</span>{/literal}{"Not an image"|gettext}{literal}'+
                                '</td></tr><tr class="odd"><td><span>{/literal}{"Image Width"|gettext}{literal}:</span>{/literal}{"Not an image"|gettext}{literal}';
            }

            infopanel.setBody('<table class="wrapper" border="0" cellspacing="0" cellpadding="5" style="100%;">'+
                '<tr><td class="file"><div>'+
                        oFile +
                '</div></td><td class="info">'+
                '<table border="0" cellspacing="0" cellpadding="2" style="width:100%;">'+
                        '<tr class="odd"><td><span>{/literal}{"Title"|gettext}{literal}:</span>'+oRecordData.title+
                        '</td></tr><tr class="even"><td><span>{/literal}{"Alt"|gettext}{literal}:</span>'+oRecordData.alt+
                        '</td></tr><tr class="odd"><td><span>{/literal}{"File Type"|gettext}{literal}:</span>'+oRecordData.mimetype+
                        imageInfo +
                        '</td></tr><tr class="even"><td><span>{/literal}{"File Size"|gettext}{literal}:</span>'+oRecordData.filesize+
                        '</td></tr><tr class="odd"><td><span>{/literal}{"URL"|gettext}{literal}:</span>'+oRecordData.url+
                        '</td></tr><tr class="even"><td><span>{/literal}{"Folder"|gettext}{literal}:</span>'+foldercat+
                        '</td></tr><tr class="odd"><td><span>{/literal}{"Date Uploaded"|gettext}{literal}:</span>'+posteddate+
                    '</td></tr>'+
                    '</table>'+
                '</td></tr></table>'
            );
            infopanel.show();
			flowplayer("a.player", EXPONENT.FLOWPLAYER_RELATIVE+"flowplayer-"+EXPONENT.FLOWPLAYER_VERSION+".swf",
				{
					wmode: 'opaque',
					clip: {
						autoPlay: false
                    },
					plugins:  {
						controls: {
							play: true,
							scrubber: true,
							fullscreen: false,
							autoHide: false
						}
					}
				}
			);
        }
        
        //set up autocomplete
        var getTerms = function(query) {
            var cat = Y.one('#select_folder');
            if (cat == null) {
                catvalue = 0;
            } else {
                catvalue = cat.get('value');
            }
            myDataSource.sendRequest('sort=id&dir=desc&startIndex=0&fck='+fck+'&results={/literal}{$smarty.const.FM_LIMIT}{literal}&query=' + query + '&cat=' + catvalue,myDataTable.onDataReturnInitializeTable, myDataTable);
        };
    
        var oACDS = new YAHOO.util.FunctionDataSource(getTerms);
        oACDS.queryMatchContains = true;
        var oAutoComp = new YAHOO.widget.AutoComplete("dt_input","dt_ac_container", oACDS);
		oAutoComp.minQueryLength = 0;
        Y.one('#clear_filter').on('click',function(e){
            e.halt();
            Y.one('#dt_input').set('value','');
            getTerms("");
        });

        EXPONENT.switch_folder = function(id){
            Y.one('#dt_input').set('value','');
            getTerms("");
        }

        // Formatters for datatable columns

        // filename formatter
        var formatFilename = function(elCell, oRecord, oColumn, sData) {
            if (oRecord.getData().is_image==1 && thumbnails) {
                elCell.innerHTML = '<a title="{/literal}{"Display File Details"|gettext}{literal}" href="#" class="fileinfo"><img src="'+EXPONENT.PATH_RELATIVE+'thumb.php?&id='+oRecord.getData().id+'&w={/literal}{$smarty.const.FM_THUMB_SIZE}{literal}&h={/literal}{$smarty.const.FM_THUMB_SIZE}{literal}"> '+oRecord.getData().filename+'</a>';
            } else {
                elCell.innerHTML = '<a title="{/literal}{"Display File Details"|gettext}{literal}" href="#" class="fileinfo">'+oRecord.getData().filename+'</a>';
            }
        };

        // date formatter
        var formatDate = function(elCell, oRecord, oColumn, sData) {
            var a = new Date(sData*1000);
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            var year = a.getFullYear();
            var month = months[a.getMonth()];
            var date = a.getDate();
            var hour = a.getHours();
            var min = a.getMinutes();
            var sec = a.getSeconds();
//            var time = date+' '+month+', '+year+' '+hour+':'+min+':'+sec ;
            var time = date+' '+month+', '+year;
            elCell.innerHTML = time;
//            elCell.innerHTML = a;
        };

        // cat formatter
        var formatCat = function(elCell, oRecord, oColumn, sData) {
            if (oRecord.getData().cat==null) {
                elCell.innerHTML = '<em title="{/literal}{"Change Folder"|gettext}{literal}">{/literal}{"Root"|gettext}{literal}</em>';
            } else {
                elCell.innerHTML = '<span title="{/literal}{"Change Folder"|gettext}{literal}">' + sData + '</span>';
            };
        }

        // Title formatter
        var formatTitle = function(elCell, oRecord, oColumn, sData) {
            elCell.innerHTML = '<span title="{/literal}{"Change Title"|gettext}{literal}">' + sData + '</span>';
        }

        // alt formatter
        var formatAlt = function(elCell, oRecord, oColumn, sData) {
            if (oRecord.getData().is_image!=1) {
                elCell.innerHTML = '<em>{/literal}{"Not an image"|gettext}{literal}</em>';
            } else {
                elCell.innerHTML = '<span title="{/literal}{"Change alt"|gettext}{literal}">' + sData + '</span>';
            };
        }

        // shared formatter
        var formatShared = function(elCell, oRecord, oColumn, sData) {
            if (oRecord.getData().shared == 0) {
                elCell.innerHTML = '<img src="'+EXPONENT.PATH_RELATIVE+'framework/modules/file/assets/images/unchecked.gif" title="{/literal}{"Make this file available to other users"|gettext}{literal}">';
            } else {
                elCell.innerHTML = '<img src="'+EXPONENT.PATH_RELATIVE+'framework/modules/file/assets/images/checked.gif" title="{/literal}{"Make this file available to other users"|gettext}{literal}">';
            };
        }

        var formatactions = function(elCell, oRecord, oColumn, sData) {
            var deletestring = '<a title="{/literal}{"Delete this File"|gettext}{literal}" href="{/literal}{link action=delete update=$smarty.get.update id="replacewithid" controller=file}{literal}" onclick="return confirm(\'{/literal}{"Are you sure you want to delete this file?"|gettext}{literal}\');"><img width=16 height=16 style="border:none;" src="{/literal}{$smarty.const.ICON_RELATIVE}{literal}delete.png" /></a>';
            deletestring = deletestring.replace('replacewithid',oRecord._oData.id);
            if (oRecord._oData.is_image==1){
                var editorstring = '<a title="{/literal}{"Edit Image"|gettext}{literal}" href="{/literal}{link controller=pixidou action=editor ajax_action=1 id="replacewithid" update=$update fck=$smarty.get.fck}{literal}"><img width=16 height=16 style="border:none;" src="{/literal}{$smarty.const.ICON_RELATIVE}{literal}edit-image.png" /></a>&#160;&#160;&#160;';
                editorstring = editorstring.replace('replacewithid',oRecord._oData.id);
            } else {
                var editorstring = '<img width=16 height=16 style="border:none;" src="{/literal}{$smarty.const.ICON_RELATIVE}{literal}cant-edit-image.png" />&#160;&#160;&#160;';
            }
            var pickerstring = {/literal}{if $smarty.get.update != "noupdate"}'<a title="{"Use This Image"|gettext}" onclick="routBackToSource(\''+EXPONENT.PATH_RELATIVE+oRecord._oData.directory+oRecord._oData.filename+'\','+oRecord._oData.id+'); window.close(); return false;" href="#"><img width=16 height=16 style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'use.png'}" /></a>&#160;&#160;&#160;'{else}''{/if}{literal};
            elCell.innerHTML =  pickerstring
                                +editorstring
                                +deletestring;
        };
    
        var formatBatch = function(elCell, oRecord, oColumn, sData) {
            var checked = (batchIDs[oRecord.getData()['id']]) ? 'checked="checked" ' : '';
            var pickerstring = '<input id="id'+oRecord.getData()['id']+'" class="batchcheck" '+ checked +'type="checkbox">';
            elCell.innerHTML =  pickerstring;
        };
    
        // request to share
        var editShare = function (callback, newValue) {
            var record = this.getRecord(),
                column = this.getColumn(),
                oldValue = this.value,
                datatable = this.getDataTable();
            
            var es = new EXPONENT.AjaxEvent();
            es.subscribe(function (o) {
                if(o.replyCode<299) {
                    callback(true, o.data.shared);
                } else {
                    alert(o.replyText);
                    callback(true, oldValue);
                }
            },this);
            es.fetch({action:"editShare",controller:"fileController",json:1,data:'&id='+record.getData().id + '&newValue=' + encodeURIComponent(newValue)});
        };
    
        // request to change the title
        var editTitle = function (callback, newValue) {
            var record = this.getRecord(),
                column = this.getColumn(),
                oldValue = this.value,
                datatable = this.getDataTable();
            var et = new EXPONENT.AjaxEvent();
            et.subscribe(function (o) {
                if(o.replyCode<299) {
                    callback(true, o.data.title);
                } else {
                    alert(o.replyText);
                    callback(true, oldValue);
                }
            },this);
            et.fetch({action:"editTitle",controller:"fileController",json:1,data:'&id='+record.getData().id + '&newValue=' + encodeURIComponent(newValue)});
        };
    
        // request to change the alt
        var editAlt = function (callback, newValue) {
            var record = this.getRecord(),
                column = this.getColumn(),
                oldValue = this.value,
                datatable = this.getDataTable();
            var ea = new EXPONENT.AjaxEvent();
            ea.subscribe(function (o) {
                if(o.replyCode<299) {
                    callback(true, o.data.alt);
                } else {
                    alert(o.replyText);
                    callback(true, oldValue);
                }
            },this);
            var req = {action:"editAlt",controller:"fileController",json:1,data:'&id='+record.getData().id + '&newValue=' + encodeURIComponent(newValue)};
            ea.fetch(req);
        };

        // request to change the cat
        var editCat = function (callback, newValue) {
            var record = this.getRecord(),
                column = this.getColumn(),
                oldValue = this.value,
                datatable = this.getDataTable();
            var et = new EXPONENT.AjaxEvent();
            et.subscribe(function (o) {
                if(o.replyCode<299) {
                    callback(true, o.data.cat);
                } else {
                    alert(o.replyText);
                    callback(true, oldValue);
                }
            },this);
            et.fetch({action:"editCat",controller:"fileController",json:1,data:'&id='+record.getData().id + '&newValue=' + encodeURIComponent(newValue)});
        };

        // Column definitions
        var myColumnDefs = [ // sortable:true enables sorting
            { key:"filename",label:"{/literal}{"File Name"|gettext}{literal}",formatter:formatFilename,sortable:true},
            { key:"posted",label:"{/literal}{"Dated"|gettext}{literal}",formatter:formatDate,sortable:true},
            { key:"cat",label:"{/literal}{"Folder"|gettext}{literal}",formatter:formatCat,editor: new YAHOO.widget.DropdownCellEditor({dropdownOptions:{/literal}{$jscats}{literal},asyncSubmitter:editCat})},
            { key:"title",label:"{/literal}{"Title"|gettext}{literal}",sortable:true,formatter:formatTitle,editor: new YAHOO.widget.TextboxCellEditor({asyncSubmitter:editTitle})},
            { key:"alt",label:"{/literal}{"alt"|gettext}{literal}",sortable:true,formatter:formatAlt,editor: new YAHOO.widget.TextboxCellEditor({asyncSubmitter:editAlt})},
            { key:"shared",label:'<img src="'+EXPONENT.PATH_RELATIVE+'framework/modules/file/assets/images/public.png" title="{/literal}{"Make File Public"|gettext}{literal}" />',formatter:formatShared,editor: new YAHOO.widget.CheckboxCellEditor({checkboxOptions:[{label:"{/literal}{"Make this file public?"|gettext}{literal}",value:1}],asyncSubmitter:editShare})},
            { label:"{/literal}{"Actions"|gettext}{literal}",sortable:false,formatter: formatactions}
            
        ];

        if (update != 'noupdate' && update !='fck') {
            myColumnDefs.push({ label:"{/literal}{"Select"|gettext}{literal}",sortable:false,formatter: formatBatch})
        };

        // DataSource instance
        var myDataSource = new YAHOO.util.DataSource(EXPONENT.PATH_RELATIVE+"index.php?controller=file&action=getFilesByJSON&json=1&ajax_action=1&fck="+fck+"&");
        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myDataSource.responseSchema = {
            resultsList: "records",
            fields: [
                "id",
                {key:"filename"},
                {key:"cat"},
                {key:"title"},
                {key:"alt"},
                {key:"shared"},
                "directory",
                "posted",
                "poster",
                "user",
                "image_width",
                "image_height",
                "mimetype",
                "filesize",
                "url",
                "is_image"
            ],
            metaFields: {
                totalRecords: "totalRecords" // Access to value in the server response
            }
        };

        var myRequestBuilder = function(oState, oSelf) {
            // Get states or use defaults
            oState = oState || { pagination: null, sortedBy: null };
            var sort = (oState.sortedBy) ? oState.sortedBy.key : "posted";
            var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "false" : "true";
            var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;

            cat = Y.one('#select_folder');
            if (cat == null) {
                catvalue = 0;
            } else {
                catvalue = cat.get('value');
            }
            query = Y.one('#dt_input');
            if (query.get('value') == null) {
                queryvalue = '';
            } else {
                queryvalue = query.get('value');
            }
            // Build custom request
            return "sort=" + sort +
                "&dir=" + dir +
                "&results=" + {/literal}{$smarty.const.FM_LIMIT}{literal} +
                "&startIndex=" + startIndex +
                "&query=" + queryvalue +
                "&cat=" + catvalue;
        };

        // DataTable configuration
        var myConfigs = {
//            initialRequest: "sort=id&dir=desc&startIndex=0&results={/literal}{$smarty.const.FM_LIMIT}{literal}", // Initial request for first page of data
            initialRequest: "sort=posted&dir=desc&startIndex=0&results={/literal}{$smarty.const.FM_LIMIT}{literal}", // Initial request for first page of data
            dynamicData: true, // Enables dynamic server-driven data
//            sortedBy : {key:"id", dir:YAHOO.widget.DataTable.CLASS_DESC}, // Sets UI initial sort arrow
            sortedBy : {key:"posted", dir:YAHOO.widget.DataTable.CLASS_DESC}, // Sets UI initial sort arrow
            paginator: new YAHOO.widget.Paginator({rowsPerPage:{/literal}{$smarty.const.FM_LIMIT}{literal},containers:"pagelinks"}), // Enables pagination,
            generateRequest: myRequestBuilder
        };
    
        // DataTable instance
        var myDataTable = new YAHOO.widget.DataTable("dynamicdata", myColumnDefs, myDataSource, myConfigs);
    
        // Update totalRecords on the fly with value from server
        myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
            if (oPayload == null) {
                oPayload = {};
            }
            oPayload.totalRecords = oResponse.meta.totalRecords;
            return oPayload;
        }
    
        // handling what to do depending on what cell we are clicking on
        myDataTable.onEventShowCellEditor = function(oArgs) {
            var currentColumn = this.getColumn(oArgs.target).field;
            var currentRecord = this.getRecord(oArgs.target).getData();
            var selectedValue = currentRecord[currentColumn];

//            if (this.getColumn(oArgs.target).field=="id") {
              if (this.getColumn(oArgs.target).field=="filename") {
                showFileInfo(this.getRecord(oArgs.target).getData());
            };
            if (usr.is_acting_admin==0) {
                if (currentColumn == "shared" && currentRecord.shared==0) {
                    this.showCellEditor(oArgs.target);
                }
                if ((currentColumn=='alt' && currentRecord.is_image==1 && currentRecord.shared==0) || (currentColumn=='alt' && currentRecord.is_image==1 && currentRecord.shared==1 && usr.id==currentRecord.poster)) {
                    this.showCellEditor(oArgs.target);
                }
                if (currentColumn=='shared' && currentRecord.shared==1){
                    alert('{/literal}{"Only Administrators can make files private again once they\'re are public."|gettext}{literal}');
                }
                if ((currentColumn=='title' && currentRecord.shared==0) || (currentColumn=='title' && currentRecord.shared==1 && usr.id==currentRecord.poster)) {
                    this.showCellEditor(oArgs.target);
                }
                if ((currentColumn=='title' || (currentColumn=='alt' && currentRecord.is_image==1)) && usr.id!=currentRecord.poster) {
                    alert("{/literal}{"Sorry, you must be the owner of this file in order to edit it."|gettext}{literal}");
                }
            } else {
                this.showCellEditor(oArgs.target);
            }
        }
        myDataTable.subscribe("cellClickEvent", myDataTable.onEventShowCellEditor);

        return {
            ds: myDataSource,
            dt: myDataTable
        };
    }();

    Y.all('.msg-queue .close').on('click',function(e){
        e.halt();
        e.target.get('parentNode').remove();
    });

    // select all
    // Y.one('#sa-batch').on('click',function(e){
    //     // e.halt();
    //     if (e.target.get('checked')) {
    //         Y.all('.batchcheck').set('checked', 'checked');;
    //     } else {
    //         Y.all('.batchcheck').set('checked', false);;
    //     }

    // });


	// YUI 2 ajax helper method. This is much easier in YUI 3. Should also migrate.
	EXPONENT.AjaxEvent = function() {
	    var obj;
	    var data = "";

	    var gatherURLInfo = function (obj){
	        json = obj.json ? "&json=1" : "";
	        if (obj.form){
	            data = YAHOO.util.Connect.setForm(obj.form);
	            //slap a date in there so IE doesn't cache
	            var dt = new Date().valueOf();
	            var sUri = EXPONENT.PATH_RELATIVE + "index.php?ajax_action=1" + json + "&yaetime=" + dt;
	            return sUri;
	        } else if (!obj.action || (!obj.controller && !obj.module)) {
	            alert("{/literal}{"If you don\'t pass the ID of a form, you need to specify both a module/controller AND and a corresponding action."|gettext}{literal}");
	        } else {
	            //slap a date in there so IE doesn't cache
	            var dt = new Date().valueOf();
	            var modcontrol = (obj.controller) ? "&controller="+obj.controller : "&module="+obj.module;
	            var sUri = EXPONENT.PATH_RELATIVE + "index.php?ajax_action=1" + modcontrol + "&action=" + obj.action + json + "&yaetime=" + dt + obj.params;
	            return sUri;
	        }
	    }

	    return {
	        json:0,
	        subscribe: function(fn, oScope) {
	            if (!this.oEvent) {
	                this.oEvent = new YAHOO.util.CustomEvent("ajaxevent", this, false, YAHOO.util.CustomEvent.FLAT);
	            }
	            if (oScope) {
	                this.oEvent.subscribe(fn, oScope, true);
	            } else {
	                this.oEvent.subscribe(fn);
	            }
	        },
	        fetch: function(obj) {
	            if (typeof obj == "undefined" || !obj){
	                alert('{/literal}{"EXPONENT.ajax requires a single object parameter."|gettext}{literal}');
	                return false;
	            } else {
	                if (typeof(obj.json)!=="undefined"){
	                    this.json = obj.json;
	                } else {
	                    this.json = false;
	                }
	                var sUri = gatherURLInfo(obj);
	                //Y.log(sUri);
	                YAHOO.util.Connect.asyncRequest("POST", sUri, {
	                success: function (o) {
	                    //if we're just sending a request and not needing to do
	                    //anything on the completion, we can skip firing the custom event
	                    if (typeof(this.oEvent)!=="undefined") {

	                        //otherwise, we check if we've got JSON coming back to parse
	                        if (this.json!==false) {
	                            //if so, parse it
	                            var oParse = YAHOO.lang.JSON.parse(o.responseText);
	                        } else {
	                            //if not, it's probably HTML we're going to update a view with
	                            var oParse = o.responseText;
	                        }
	                        //fire off the custom event to do some more stuff with
	                        this.oEvent.fire(oParse);
	                    }
	                },
	                    scope: this
	                },obj.data);
	            }
	        }
	    }
	}

});
{/literal}
{/script}
</body>
</html>
