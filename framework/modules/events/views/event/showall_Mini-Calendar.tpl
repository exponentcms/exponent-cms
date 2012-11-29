{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
 
{css unique="cal" link="`$asset_path`css/calendar.css"}

{/css}

<div class="module events mini-cal">
    <div id="mini-cal">
        {include 'minical.tpl'}
    </div>
    {icon class="monthviewlink" action=showall time=$now text='Calendar View'|gettext}
	{br}
	{permissions}
		{if $permissions.create == 1}
			<div class="module-actions">
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			</div>
		{/if}
	{/permissions}
</div>

{script unique="minical" yui3mods="1"}
{literal}

YUI(EXPONENT.YUI3_CONFIG).use('node','io','node-event-delegate', function(Y) {
    var minical = Y.one('#mini-cal');
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Minical'},
    			arguments : { 'X-Transaction': 'Load Minical'}
    		};

    src = '{/literal}{$__loc->src}{literal}';
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=event&action=showall&view=minical&ajax_action=1&src="+src;

	var handleSuccess = function(ioId, o){
//		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "minical nav");

        if(o.responseText){
            minical.setContent(o.responseText);
        } else {
            Y.one('#mini-cal.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "minical nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    minical.delegate('click', function(e){
        cfg.data = "time="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        minical.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Month"|gettext}{literal}</div>'));
    }, 'a.nav');
});
{/literal}
{/script}
