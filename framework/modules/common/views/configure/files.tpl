{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

<h2>{"Configure File Display Settings"|gettext}</h2>

{control id="filedisplay" type=filedisplay-types name=filedisplay label="Display Files as" value=$config.filedisplay}
<div id="fileViewConfig">
    {if $config.filedisplay != ""}
        {assign var=themefileview value="`$smarty.const.BASE`themes/`$smarty.const.DISPLAY_THEME_REAL`/modules/common/views/file/configure/`$config.filedisplay`.tpl"}
        {if file_exists($themefileview)}
            {include file=$themefileview}
        {else}
            {include file="`$smarty.const.BASE`framework/modules/common/views/file/configure/`$config.filedisplay`.tpl"}
        {/if}
    {/if}
</div>

{script unique="fileviewconfig" yui3mods="1"}
{literal}

YUI(EXPONENT.YUI3_CONFIG).use('node','io', function(Y) {
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load File Config'}
    		};
    		
	var sUrl = EXPONENT.URL_FULL+"index.php?controller=file&action=get_view_config&ajax_action=1";

	var handleSuccess = function(ioId, o){
		Y.log(arguments);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "example");

        if(o.responseText !== undefined){
            Y.one('#fileViewConfig').setContent(o.responseText);
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "example");

        // if(o.responseText !== undefined){
        //  var s = "<li>Transaction id: " + ioId + "</li>";
        //  s += "<li>HTTP status: " + o.status + "</li>";
        //  s += "<li>Status code message: " + o.statusText + "</li>";
        //  div.set("innerHTML", s);
        // }
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    Y.one('#filedisplay').on('change',function(e){
        cfg.data = "view="+e.target.get('value');
        var request = Y.io(sUrl, cfg);
    });
});

    
    // var swapview = function(view) {    
    //     // instantiate an ajax object
    //     var ej = new EXPONENT.AjaxEvent();
    // 
    //     // handler for our ajax event
    //     // o is our returned object, either parsed as JSON if json:1 was set
    //     // if we're expecting a template (o.data), don't set json
    //     // If you don't need to do anything on the response, you don't need to subscribe
    //     ej.subscribe(function (viewConfigTemplate) {
    //          var viewConfig = YAHOO.util.Dom.get('fileViewConfig'); //get the div to update
    //          viewConfig.innerHTML = viewConfigTemplate; //put the returned markup in to the div
    //     },this);
    // 
    //     // fire the ajax event
    //     ej.fetch({action:"get_view_config",controller:"file",params:'&view='+view});
    // };
    // 
    // var viewselector = YAHOO.util.Dom.get('filedisplay');
    // YAHOO.util.Event.on(viewselector, 'change', function() {        
    //     
    //     swapview(EXPONENT.forms.getSelectValue('filedisplay'));
    // });
{/literal}
{/script}

