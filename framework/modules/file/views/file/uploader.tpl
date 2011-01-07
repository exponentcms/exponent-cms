<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>File Uploader  |  Exponent CMS</title>
    <link rel="stylesheet" type="text/css" href="{$smarty.const.URL_FULL}framework/modules/file/assets/css/filemanager.css"> 

    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}yahoo-dom-event/yahoo-dom-event.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}element/element-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}uploader/uploader.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}datasource/datasource-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.YUI2_PATH}datatable/datatable-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.URL_FULL}exponent.js.php"></script>
</head>
<body class=" exp-skin">
<div id="exp-uploader">
    <h1>Upload Files</h1>
    <div id="actionbar">
    	<div id="uploaderOverlay" style="position:absolute; z-index:2"></div>
    	<a class="btn" style="z-index:1" id="selectLink" href="#"><strong><em>Select Files</em></strong></a>
        <a id="uploadLink" class="btn" href="#"><strong><em>Upload Files</em></strong></a>
        <a class="btn" href="{link action=picker update=$smarty.get.update fck=$smarty.get.fck ajax_action=1}?CKEditor={$smarty.get.CKEditor}&amp;CKEditorFuncNum={$smarty.get.CKEditorFuncNum}&amp;langCode={$smarty.get.langCode}"><strong><em>Back to Manager</em></strong></a>
    </div>
    <div id="dataTableContainer"></div>
</div>

<script type="text/javascript">
{literal}
(function () { 
    var uiLayer = YAHOO.util.Dom.getRegion('selectLink');
    var button = YAHOO.util.Dom.get('selectLink');
    var overlay = YAHOO.util.Dom.get('uploaderOverlay');
    var uploadlink = YAHOO.util.Dom.get('uploadLink');
    YAHOO.util.Dom.setStyle(overlay, 'width', uiLayer.right-uiLayer.left + 20 + "px");
    YAHOO.util.Dom.setStyle(overlay, 'height', uiLayer.bottom-uiLayer.top + "px");
    
    var usr = {/literal}{obj2json obj=$user}{literal}; //user
    
	// Custom URL for the uploader swf file (same folder).
	YAHOO.widget.Uploader.SWFURL = EXPONENT.YUI2_PATH+"uploader/assets/uploader.swf";

    // Instantiate the uploader and write it to its placeholder div.
	var uploader = new YAHOO.widget.Uploader( "uploaderOverlay" );
	
	// Add event listeners to various events on the uploader.
	// Methods on the uploader should only be called once the 
	// contentReady event has fired.
	
	uploader.addListener('contentReady', handleContentReady);
	uploader.addListener('fileSelect', onFileSelect)
	uploader.addListener('uploadStart', onUploadStart);
	uploader.addListener('uploadProgress', onUploadProgress);
	uploader.addListener('uploadCancel', onUploadCancel);
	uploader.addListener('uploadComplete', onUploadComplete);
	uploader.addListener('uploadCompleteData', onUploadResponse);
	uploader.addListener('uploadError', onUploadError);
    uploader.addListener('rollOver', handleRollOver);
    uploader.addListener('rollOut', handleRollOut);
    uploader.addListener('click', handleClick);
    
    YAHOO.util.Event.on(uploadlink, 'click', upload);
    	
    // Variable for holding the filelist.
	var fileList;
	
	// When the mouse rolls over the uploader, this function
	// is called in response to the rollOver event.
	// It changes the appearance of the UI element below the Flash overlay.
	function handleRollOver () {
		YAHOO.util.Dom.addClass(button,'btn-selected');
	}
	
	// On rollOut event, this function is called, which changes the appearance of the
	// UI element below the Flash layer back to its original state.
	function handleRollOut () {
		YAHOO.util.Dom.removeClass(button,'btn-selected');
	}
	
	// When the Flash layer is clicked, the "Browse" dialog is invoked.
	// The click event handler allows you to do something else if you need to.
	function handleClick () {
	}
	
	// When contentReady event is fired, you can call methods on the uploader.
	function handleContentReady () {
	    // Allows the uploader to send log messages to trace, as well as to YAHOO.log
		uploader.setAllowLogging(true);
		
		// Allows multiple file selection in "Browse" dialog.
		uploader.setAllowMultipleFiles(true);
		
		// New set of file filters.
		var ff = new Array({description:"Images", extensions:"*.jpg;*.png;*.gif"},
                            {description:"Videos", extensions:"*.avi;*.mov;*.mpg"},
                            {description:"Documents", extensions:"*pdf;*.doc;*.odt;*.zip;*.psd;*.flv;*.csv;*.xls"},
                            {description:"All Files", extensions:"*.*;"}
		                    );
		                   
		// Apply new set of file filters to the uploader.
		uploader.setFileFilters(ff);
	}

	// Actually uploads the files. In this case,
	// uploadAll() is used for automated queueing and upload 
	// of all files on the list.
	// You can manage the queue on your own and use "upload" instead,
	// if you need to modify the properties of the request for each
	// individual file.
	function upload() {
    	if (fileList != null) {
    		uploader.setSimUploadLimit(parseInt(3));
    		uploader.uploadAll(EXPONENT.URL_FULL+"index.php?controller=file&action=upload&ajax_action=1", "POST", {usrid:usr.id}, "Filedata");
    	}	
	}
	// Fired when the user selects files in the "Browse" dialog
	// and clicks "Ok".
	function onFileSelect(event) {
		if('fileList' in event && event.fileList != null) {
			fileList = event.fileList;
			createDataTable(fileList);
		}
	}

	function createDataTable(entries) {
	  rowCounter = 0;
	  this.fileIdHash = {};
	  this.dataArr = [];
	  for(var i in entries) {
	     var entry = entries[i];
		 entry["progress"] = "<div style='height:5px;width:100px;background-color:#CCC;'></div>";
	     dataArr.unshift(entry);
	  }
	
	  for (var j = 0; j < dataArr.length; j++) {
	    this.fileIdHash[dataArr[j].id] = j;
	  }
	
	    var myColumnDefs = [
	        {key:"name", label: "File Name", sortable:false},
	     	{key:"size", label: "Size", sortable:false},
	     	{key:"progress", label: "Upload progress", sortable:false}
	    ];

	  this.myDataSource = new YAHOO.util.DataSource(dataArr);
	  this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
      this.myDataSource.responseSchema = {
          fields: ["id","name","created","modified","type", "size", "progress"]
      };

	  this.singleSelectDataTable = new YAHOO.widget.DataTable("dataTableContainer",
	           myColumnDefs, this.myDataSource, {
	               caption:"Files To Upload",
	               selectionMode:"single"
	           });
	}

    // Do something on each file's upload start.
	function onUploadStart(event) {
	
	}
	
	// Do something on each file's upload progress event.
	function onUploadProgress(event) {
		rowNum = fileIdHash[event["id"]];
		prog = Math.round(100*(event["bytesLoaded"]/event["bytesTotal"]));
		progbar = "<div style='height:5px;width:100px;background-color:#CCC;'><div style='height:5px;background-color:#F00;width:" + prog + "px;'></div></div>";
		singleSelectDataTable.updateRow(rowNum, {name: dataArr[rowNum]["name"], size: dataArr[rowNum]["size"], progress: progbar});	
	}
	
	// Do something when each file's upload is complete.
	function onUploadComplete(event) {
		rowNum = fileIdHash[event["id"]];
		prog = Math.round(100*(event["bytesLoaded"]/event["bytesTotal"]));
		progbar = "<div style='height:5px;width:100px;background-color:#CCC;'><div style='height:5px;background-color:#F00;width:100px;'></div></div>";
		singleSelectDataTable.updateRow(rowNum, {name: dataArr[rowNum]["name"], size: dataArr[rowNum]["size"], progress: progbar});
	    uploader.removeFile(event["id"]);
	    fileList = event.fileList;
	}
	
	// Do something if a file upload throws an error.
	// (When uploadAll() is used, the Uploader will
	// attempt to continue uploading.
	function onUploadError(event) {

	}
	
	// Do something if an upload is cancelled.
	function onUploadCancel(event) {

	}
	
	// Do something when data is received back from the server.
	function onUploadResponse(event) {
	    //console.debug(event.data);
	    //uploader.removeFile(event["id"]);
	}
})();

{/literal}
</script>
</body>
</html>
