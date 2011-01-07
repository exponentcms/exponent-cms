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

{*control type=checkbox name=usesfiles label="This module uses Files" value=1 checked=$config.usesfiles*}
{*control type=checkbox name=allowdownloads label="Allow file downloads" value=1 checked=$config.allowdownloads*}

{control id="filedisplay" type=filedisplay-types name=filedisplay label="Display Files as" value=$config.filedisplay}
<div id="fileViewConfig">
    {if $config.filedisplay != ""}
        {include file="../file/configure/`$config.filedisplay`.tpl"}
    {/if}
</div>

{script unique="fileviewconfig" yuimodules="json"}
{literal}


	/*
		This function will grab the name of the value listed in the drop down "filedisplay" control
		and pass that name off to the index.php file via an AJAX request. 
		
		swapview(EXPONENT.forms.getSelectValue('filedisplay'));
		
		When we change the dropdown, this fires the swapview, which 
		makes a new AJAX object and then does two things:
		
		1) It sets the SUBSCRIBE of the object. That is, when we get data back from 
		our request, this is the function that will be exectued with that data. 
		
		In our subscribe, we have a function that accepts the received data, that is, the
		config view we want to display, as a variable called viewConfigTemplate.
		
		The function then takes that data and injects it into the page, replacing all 
		previous HTML.
		
		You can see where we fire the AJAX event here:
		
		ej.fetch({action:"get_view_config",controller:"file",params:'&view='+view});
		
		We ask the object to perform the FETCH function which will call the index.php file
		on our site.		
		
		The index.php will route our request to the proper place via the post variables that 
		we provde it. We told it that we wanted the "file" controller and the 
		"get_view_config" action. That action will look at the params that we send it, 
		get the view, and then generate the HTML we need. It will then return the HTML we need
		and when we receive it, that is when the function created in the SUBSCRIBE is called.
		


	*/

    var swapview = function(view) {    
        // instantiate an ajax object
        var ej = new EXPONENT.AjaxEvent();

        // handler for our ajax event
        // o is our returned object, either parsed as JSON if json:1 was set
        // if we're expecting a template (o.data), don't set json
        // If you don't need to do anything on the response, you don't need to subscribe
        ej.subscribe(function (viewConfigTemplate) {
             var viewConfig = YAHOO.util.Dom.get('fileViewConfig'); //get the div to update
             viewConfig.innerHTML = viewConfigTemplate; //put the returned markup in to the div
        },this);

        // fire the ajax event
        ej.fetch({action:"get_view_config",controller:"file",params:'&view='+view});
    };
    
    var viewselector = YAHOO.util.Dom.get('filedisplay');
    YAHOO.util.Event.on(viewselector, 'change', function() {        
        
        swapview(EXPONENT.forms.getSelectValue('filedisplay'));
    });
{/literal}
{/script}
