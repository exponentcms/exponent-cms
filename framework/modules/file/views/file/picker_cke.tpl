<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>File Manager  |  Exponent CMS</title>

    <link rel="stylesheet" type="text/css" href="{$smarty.const.URL_FULL}framework/core/assets/css/msgq.css"> 
    <link rel="stylesheet" type="text/css" href="{$smarty.const.URL_FULL}framework/modules/file/assets/css/filemanager.css"> 

    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}yahoo-dom-event/yahoo-dom-event.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}container/container-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}connection/connection-min.js"></script>

    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}json/json-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}datasource/datasource-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}autocomplete/autocomplete-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}element/element-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}paginator/paginator-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}datatable/datatable-min.js"></script>

    <script type="text/javascript" src="{$smarty.const.YUI3_PATH}yui/yui-min.js"></script>


    <script type="text/javascript" src="{$smarty.const.URL_FULL}exponent.js.php"></script>


</head>
<body class=" exp-skin">

<div id="filemanager">
    <h1>File Manager</h1>
    {messagequeue}
    <div id="autocomplete">
        <label for="dt_input">Filter by Filename, title, or alt:</label>
        <input id="dt_input" type="text" />
        <div id="dt_ac_container"></div>
    </div>

    <div id="pagelinks">&nbsp;</div>
    <div id="dynamicdata">
    
    </div>
    <div id="actionbar">
        <a class="btn" href="{link action=uploader update=$smarty.get.update fck=$smarty.get.fck CKEditor=$smarty.get.CKEditor CKEditorFuncNum=$smarty.get.CKEditorFuncNum langCode=$smarty.get.langCode ajax_action=1 }"><strong><em>Upload Files</em></strong></a>
    </div>

    <div id="infopanel">
        <div class="hd"></div>
        <div class="bd"></div>
    </div>
</div>
<script type="text/javascript">
{literal}

// this.moveTo(1,1);
// this.resizeTo(screen.width,screen.height);

EXPONENT.fileManager = function() {
    var queryString = '&results=50&output=json'; //autocomplete query
    var fck = {/literal}{if $smarty.get.fck}{$smarty.get.fck}{else}0{/if}{literal}; //are we coming from FCK as the window launcher?
    var usr = {/literal}{obj2json obj=$user}{literal}; //user
    var myDataSource = null;
    var myDataTable = null;
    
    function getUrlParam(paramName) {
        var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
        var match = window.location.search.match(reParam) ;

        return (match && match.length > 1) ? match[1] : '' ;
    }
    
    routBackToSource = function (fo,fi) {
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
        
    // handler for showing file information
    var showFileInfo = function(oRecordData) {
        var owner = (oRecordData.user.username!="") ? ' owned by '+oRecordData.user.firstname+' '+oRecordData.user.lastname+' ('+oRecordData.user.username+')' : "";

        infopanel.setHeader(oRecordData.filename+owner);
        if (oRecordData.is_image==1) {
            var oFile = '<img src="'+oRecordData.url+'">';
        }else{
            var oFile = '<img src="'+EXPONENT.URL_FULL+'framework/modules/file/assets/images/general.png">' ;
        };
        
        infopanel.setBody('<table class="wrapper" border="0" cellspacing="0" cellpadding="5" width=100%>'+
            '<tr><td class="file"><div>'+
                    oFile +
            '</div></td><td class="info">'+
            '<table border="0" cellspacing="0" cellpadding="2" width=100%>'+
                    '<tr class="odd"><td><span>Title</span>'+oRecordData.title+
                    '</td></tr><tr class="even"><td><span>Alt</span>'+oRecordData.alt+
                    '</td></tr><tr class="odd"><td><span>File Type</span>'+oRecordData.mimetype+
                    '</td></tr><tr class="even"><td><span>Image Height</span>'+oRecordData.image_height+
                    '</td></tr><tr class="odd"><td><span>Image Width</span>'+oRecordData.image_width+
                    '</td></tr><tr class="even"><td><span>File Size</span>'+oRecordData.filesize+
                    '</td></tr><tr class="odd"><td><span>URL</span>'+oRecordData.url+
                '</td></tr>'+
                '</table>'+
            '</td></tr></table>'
        );
        infopanel.show();
    }
        
    //set up autocomplete
    var getTerms = function(query) {
        myDataSource.sendRequest('sort=id&dir=desc&startIndex=0&fck='+fck+'&results=25&query=' + query,myDataTable.onDataReturnInitializeTable, myDataTable);
    };
    
    var oACDS = new YAHOO.util.FunctionDataSource(getTerms);
    oACDS.queryMatchContains = true;
    var oAutoComp = new YAHOO.widget.AutoComplete("dt_input","dt_ac_container", oACDS);


    // Formatters for datatable columns

    // filename formatter
    var formatTitle = function(elCell, oRecord, oColumn, sData) {
        elCell.innerHTML = '<a href="#" class="fileinfo">'+oRecord.getData().filename+'</a>';
    };

    // alt formatter
    var formatAlt = function(elCell, oRecord, oColumn, sData) {
        if (oRecord.getData().is_image!=1) {
            elCell.innerHTML = '<em>Not an image</em>';
        } else {
            elCell.innerHTML = sData;
        };
    }

    // shared formatter
    var formatShared = function(elCell, oRecord, oColumn, sData) {
        if (oRecord._oData.shared == 0) {
            elCell.innerHTML = '<img src="'+EXPONENT.URL_FULL+'framework/modules/file/assets/images/unchecked.gif" title="Make this fie available to other users">';
        } else {
            elCell.innerHTML = '<img src="'+EXPONENT.URL_FULL+'framework/modules/file/assets/images/checked.gif" title="Make this fie available to other users">';
        };
    }
    
    var formatactions = function(elCell, oRecord, oColumn, sData) {
        var deletestring = '<a href="{/literal}{link action=delete update=$smarty.get.update id="replacewithid" controller=file}{literal}" onclick="return confirm(\'Are you sure you want to delete this file?\');"><img width=16 height=16 style="border:none;" src="{/literal}{$smarty.const.ICON_RELATIVE}{literal}delete.png" /></a>';
        deletestring = deletestring.replace('replacewithid',oRecord._oData.id);
        if (oRecord._oData.is_image==1){
            var editorstring = '<a title="Edit Image" href="{/literal}{link controller=pixidou action=editor ajax_action=1 id="replacewithid" update=$update fck=$smarty.get.fck}{literal}"><img width=16 height=16 style="border:none;" src="{/literal}{$smarty.const.ICON_RELATIVE}{literal}edit-image.png" /></a>&nbsp;&nbsp;&nbsp;';
            editorstring = editorstring.replace('replacewithid',oRecord._oData.id);
        } else {
            var editorstring = '<img width=16 height=16 style="border:none;" src="{/literal}{$smarty.const.ICON_RELATIVE}{literal}cant-edit-image.png" />&nbsp;&nbsp;&nbsp;';
        }
        var pickerstring = {/literal}{if $smarty.get.update != "noupdate"}'<a title="Use This Image" onclick="routBackToSource(\''+EXPONENT.PATH_RELATIVE+oRecord._oData.directory+oRecord._oData.filename+'\','+oRecord._oData.id+'); window.close(); return false;" href="#"><img width=16 height=16 style="border:none;" src="{$smarty.const.ICON_RELATIVE}use.png" /></a>&nbsp;&nbsp;&nbsp;'{else}''{/if}{literal}
        elCell.innerHTML =  pickerstring
                            +editorstring
                            +deletestring;
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
                callback(true, o.data.share);
            } else {
                alert(o.replyText);
                callback(true, oldValue);
            }
        },this);
        es.fetch({action:"editShare",controller:"fileController",json:1,data:'&id='+record.getData().id + '&newValue=' + escape(newValue)});
            
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
        et.fetch({action:"editTitle",controller:"fileController",json:1,data:'&id='+record.getData().id + '&newValue=' + escape(newValue)});
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
        var req = {action:"editAlt",controller:"fileController",json:1,data:'&id='+record.getData().id + '&newValue=' + escape(newValue)};
        ea.fetch(req);
    };
    
    // Column definitions
    var myColumnDefs = [ // sortable:true enables sorting
        { key:"id",label:"File Name",formatter:formatTitle,sortable:true},
        { key:"title",label:"Title",sortable:true, editor: new YAHOO.widget.TextboxCellEditor({asyncSubmitter:editTitle})},
        { key:"alt",label:"alt", sortable:true, formatter:formatAlt, editor: new YAHOO.widget.TextboxCellEditor({asyncSubmitter:editAlt})},
        { key:"shared",label:'<img src="'+EXPONENT.URL_FULL+'framework/modules/file/assets/images/public.png" title="Make File Public" />',formatter:formatShared,editor: new YAHOO.widget.CheckboxCellEditor({checkboxOptions:[{label:"Make this file public?",value:1}],asyncSubmitter:editShare})},
        { label:"Actions",sortable:false,formatter: formatactions}
        ];

    // DataSource instance
    var myDataSource = new YAHOO.util.DataSource(EXPONENT.URL_FULL+"index.php?controller=file&action=getFilesByJSON&json=1&ajax_action=1&fck="+fck+"&");
    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    myDataSource.responseSchema = {
        resultsList: "records",
        fields: [
            "id",
            {key:"filename"},
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
    
    // DataTable configuration
    var myConfigs = {
        initialRequest: "sort=id&dir=desc&startIndex=0&results=25", // Initial request for first page of data
        dynamicData: true, // Enables dynamic server-driven data
        sortedBy : {key:"id", dir:YAHOO.widget.DataTable.CLASS_DESC}, // Sets UI initial sort arrow
        paginator: new YAHOO.widget.Paginator({rowsPerPage:25,containers:"pagelinks"}) // Enables pagination 
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

        if (this.getColumn(oArgs.target).field=="id") {
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
                alert('Only Administrators can make files private again once they\'re are public.');
            }

            if ((currentColumn=='title' && currentRecord.shared==0) || (currentColumn=='title' && currentRecord.shared==1 && usr.id==currentRecord.poster)) {
                this.showCellEditor(oArgs.target);
            }

            if ((currentColumn=='title' || (currentColumn=='alt' && currentRecord.is_image==1)) && usr.id!=currentRecord.poster) {
                alert("Sorry, you must be the owner of this file in order to edit it.");
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

YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
    Y.all('.msg-queue .close').on('click',function(e){
        e.halt();
        e.target.get('parentNode').remove();
    });
});
{/literal}
</script>

</body>
</html>
