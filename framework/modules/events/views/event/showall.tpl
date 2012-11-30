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

{uniqueid prepend="cal" assign="name"}

{css unique="cal" link="`$asset_path`css/calendar.css"}

{/css}

{css unique="cal-popup" link="`$asset_path`css/default.css"}

{/css}

<div class="module events default">
	<div class="module-actions">
		<span class="monthviewlink">{'Calendar View'|gettext}</span>
        &#160;&#160;|&#160;&#160;
        {icon class="listviewlink" action=showall view='showall_Monthly List' time=$time text='List View'|gettext}
		{permissions}
			{if $permissions.manage == 1}
				&#160;&#160;|&#160;&#160;
                {icon class="adminviewlink" action=showall view='showall_Administration' time=$time text='Administration View'|gettext}
                {if !$config.disabletags}
                    &#160;&#160;|&#160;&#160;
                    {icon controller=expTag class="manage" action=manage_module model='event' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    &#160;&#160;|&#160;&#160;
                    {icon controller=expCat action=manage model='event' text="Manage Categories"|gettext}
                {/if}
			{/if}
		{/permissions}
        {printer_friendly_link text='Printer-friendly'|gettext prepend='&#160;&#160;|&#160;&#160;'}
        {export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}
        {br}
	</div>
	<h1>
        {ical_link}
        {if $moduletitle && !$config.hidemoduletitle}{$moduletitle}{/if}
	</h1>
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
	{permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
		</div>
	{/permissions}
    <div id="popup">
        <a href="javascript:void(0);" id="J_popup_closeable{$__loc->src|replace:'@':'_'}">{'Go to Date'|gettext}</a>
        <div id="month-cal">
            {include 'month.tpl'}
        </div>
    </div>
</div>

{script unique=$name yui3mods=1}
{literal}

EXPONENT.YUI3_CONFIG.modules = {
	'gallery-calendar': {
		fullpath: '{/literal}{$asset_path}js/calendar.js{literal}',
		requires: ['node']
	}
}

YUI(EXPONENT.YUI3_CONFIG).use('node','gallery-calendar','io','node-event-delegate',function(Y){
	var today = new Date({/literal}{$time}{literal}*1000);
    var monthcal = Y.one('#month-cal');
    var cfg = {
                method: "POST",
                headers: { 'X-Transaction': 'Load Minical'},
                arguments : { 'X-Transaction': 'Load Minical'}
            };
    src = '{/literal}{$__loc->src}{literal}';
    var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=event&action=showall&view=month&ajax_action=1&src="+src;

	// Popup
	var cal = new Y.Calendar('J_popup_closeable{/literal}{$__loc->src|replace:'@':'_'}{literal}',{
		popup:true,
		closeable:true,
		startDay:{/literal}{$smarty.const.DISPLAY_START_OF_WEEK}{literal},
		date:today,
		action:['click'],
//        useShim:true
	}).on('select',function(d){
		var unixtime = parseInt(d / 1000);
//        window.location=eXp.PATH_RELATIVE+'index.php?controller=event&action=showall&view=month&time='+unixtime+'&ajax_action=1&src={/literal}{$__loc->src}{literal}';
        cfg.data = "time="+unixtime;
        var request = Y.io(sUrl, cfg);
        monthcal.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Month"|gettext}{literal}</div>'));
	});
    Y.one('#J_popup_closeable{/literal}{$__loc->src|replace:'@':'_'}{literal}').on('click',function(d){
        cal.show();
    });

    // ajax load new month
	var handleSuccess = function(ioId, o){
//		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "monthcal nav");

        if(o.responseText){
            monthcal.setContent(o.responseText);
        } else {
            Y.one('#month-cal.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "monthcal nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    monthcal.delegate('click', function(e){
        cfg.data = "time="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        monthcal.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Month"|gettext}{literal}</div>'));
    }, 'a.nav');
});
{/literal}
{/script}
