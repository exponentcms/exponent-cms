EXPONENT.filemanager = function(cfg) {
    var queryString = '&results=20&output=json';
    var fck = 0;
    var myDataSource = null;
    var myDataTable = null;

    var getTerms = function(query) {
        myDataSource.sendRequest('sort=id&dir=desc&startIndex=0&fck='+fck+'&results=25&query=' + query,myDataTable.onDataReturnInitializeTable, myDataTable);
    };
    
    var oACDS = new YAHOO.util.FunctionDataSource(getTerms);
    oACDS.queryMatchContains = true;
    var oAutoComp = new YAHOO.widget.AutoComplete("dt_input","dt_ac_container", oACDS);


    // format image 
    var formImage = function(elCell, oRecord, oColumn, sData) {
        
        // if (oRecord._oData.is_image==true) {
        //     var img = '<a class="thumbnail" href="#" onclick="return openWindow(\'{/literal}{$smarty.const.PATH_RELATIVE}{literal}'+oRecord._oData.directory+'/'+oRecord._oData.filename+'\','+oRecord._oData.image_width+','+oRecord._oData.image_height+');">'
        //     + '<img width=30 height=30 src="{/literal}{$smarty.const.PATH_RELATIVE}{literal}thumb.php?id='+oRecord._oData.id+'&square=30" border="0"/></a>'
        // } else {
        //     var img = '<img src="{/literal}{$smarty.const.URL_FULL}{literal}/themes/common/skin/attachableitems/generic_22x22.png" class="filepic"/>'
        // }
        // elCell.innerHTML = img;
    };
    var formatactions = function(elCell, oRecord, oColumn, sData) {
        // var deletestring = '<a href="{/literal}{link action=delete update=$smarty.get.update id="replacewithid" controller=file}{literal}" onclick="return confirm(\'Are you sure you want to delete this file?\');"><img width=16 height=16 style="border:none;" src="{/literal}{$smarty.const.ICON_RELATIVE}{literal}delete.png" /></a>';
        // deletestring = deletestring.replace('replacewithid',oRecord._oData.id);
        // if (oRecord._oData.is_image==1){
        //     var editorstring = '<a title="Edit Image" href="{/literal}{link controller=pixidou action=editor ajax_action=1 id="replacewithid" update=$update fck=$smarty.get.fck}{literal}"><img width=16 height=16 style="border:none;" src="{/literal}{$smarty.const.ICON_RELATIVE}{literal}actions/edit-image.png" /></a>&nbsp;&nbsp;&nbsp;';
        //     editorstring = editorstring.replace('replacewithid',oRecord._oData.id);
        // } else {
        //     var editorstring = '<img width=16 height=16 style="border:none;" src="{/literal}{$smarty.const.ICON_RELATIVE}{literal}actions/cant-edit-image.png" />&nbsp;&nbsp;&nbsp;';
        // }
        // var pickerstring = {/literal}{if $smarty.get.update != "noupdate"}'<a title="Use This Image" onclick="window.opener.{if $smarty.get.fck}SetUrl(\''+EXPONENT.PATH_RELATIVE+oRecord._oData.directory+'/'+oRecord._oData.filename+'\',\''+oRecord._oData.image_width+'\',\''+oRecord._oData.image_height+'\'){else}EXPONENT.passBackFile{$update}(' + oRecord._oData.id + '){/if}; window.close(); return false;" href="#"><img width=16 height=16 style="border:none;" src="{$smarty.const.ICON_RELATIVE}actions/use.png" /></a>&nbsp;&nbsp;&nbsp;'{else}''{/if}{literal}
        // elCell.innerHTML =  pickerstring
        //                     +editorstring
        //                     +deletestring;
    };
    // Column definitions
    var myColumnDefs = [ // sortable:true enables sorting
        {key:"id", width:50,label:"File",formatter:formImage,sortable:true},
        { width:570,key:"filename",label:"File Name",sortable:true},
        { width:100,label:"Actions",sortable:false,formatter: formatactions}
        ];

    // DataSource instance
    var myDataSource = new YAHOO.util.DataSource(EXPONENT.URL_FULL+"index.php?controller=file&action=getFilesByJSON&json=1&ajax_action=1&fck="+fck+"&");
    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    myDataSource.responseSchema = {
        resultsList: "records",
        fields: [
            "id",
            {key:"filename"},
            {key:"directory"},
            "directory",
            {key:"posted"},
            {key:"image_width"},
            {key:"image_height"},
            {key:"is_image"}
        ],
        metaFields: {
            totalRecords: "totalRecords" // Access to value in the server response
        }
    };
    
    // DataTable configuration
    var myConfigs = {
        height:"400px",
        width:"800px",
        scrollable:true,
        initialRequest: "sort=id&dir=desc&startIndex=0&results=25", // Initial request for first page of data
        dynamicData: true, // Enables dynamic server-driven data
        sortedBy : {key:"id", dir:YAHOO.widget.DataTable.CLASS_DESC}, // Sets UI initial sort arrow
        paginator: new YAHOO.widget.Paginator({ rowsPerPage:25 }) // Enables pagination 
    };
    
    // DataTable instance
    var myDataTable = new YAHOO.widget.DataTable("dynamicdata", myColumnDefs, myDataSource, myConfigs);
    // Update totalRecords on the fly with value from server
    myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
        //console.debug(oPayload);
        if (oPayload == null) {
            oPayload = {};
        }
        oPayload.totalRecords = oResponse.meta.totalRecords;
        return oPayload;
    }
    
    return {
        ds: myDataSource,
        dt: myDataTable
    };
        
};
