{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="module events default">
	<div class="module-actions">
        {if !$config.disable_links}
            {*<span class="monthviewlink">{'Calendar View'|gettext}</span>*}
            {icon class="monthviewlink" text='Calendar View'|gettext}
            {if !bs()}
                {nbsp count=2}|{nbsp count=2}
            {/if}
            {icon class="listviewlink" action=showall view='showall_Monthly List' time=$time text='List View'|gettext}
        {/if}
		{permissions}
			{if $permissions.manage}
                {if !bs()}
                    {nbsp count=2}|{nbsp count=2}
                {/if}
                {icon class="adminviewlink" action=showall view='showall_Administration' time=$time text='Administration View'|gettext}
                {if !$config.disabletags}
                    {if !bs()}
                        {nbsp count=2}|{nbsp count=2}
                    {/if}
                    {icon controller=expTag class="manage" action=manage_module model='event' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {if !bs()}
                        {nbsp count=2}|{nbsp count=2}
                    {/if}
                    {icon controller=expCat action=manage model='event' text="Manage Categories"|gettext}
                {/if}
			{/if}
		{/permissions}
        {*{printer_friendly_link text='Printer-friendly'|gettext prepend='&#160;&#160;|&#160;&#160;'}*}
        {*{export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}*}
        {br}
	</div>
	<{$config.heading_level|default:'h1'}>
        {ical_link}
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}{/if}
	</{$config.heading_level|default:'h1'}>
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
	{permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
		</div>
	{/permissions}
    <div id="popup">
        <a href="javascript:void(0);" class="evnav module-actions" id="J_popup_closeable{$__loc->src|replace:'@':'_'}">{'Go to Date'|gettext}</a>
        <div id="lb-bg" style="display:none;">
        </div>
        <div id="month-{$name}">
            {exp_include file='month.tpl'}
        </div>
    </div>
</div>

{script unique=$name|cat:'-popup' yui3mods="node,gallery-calendar,io,node-event-delegate" jquery="jquery.history"}
{literal}
EXPONENT.YUI3_CONFIG.modules = {
    'gallery-calendar': {
        fullpath: '{/literal}{$asset_path}js/calendar.js{literal}',
        requires: ['node','calendar-css']
    },
    'calendar-css': {
        fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/events/assets/css/default.css',
        type: 'css'
    }
}
YUI(EXPONENT.YUI3_CONFIG).use('*',function(Y){
	var today = new Date({/literal}{$time}{literal} * 1000);
    var monthcal_{/literal}{$name}{literal} = Y.one('#month-{/literal}{$name}{literal}');
    var page_parm_{/literal}{$name}{literal} = '';
    if (EXPONENT.SEF_URLS) {
        page_parm_{/literal}{$name}{literal} = '/time/';
    } else {
        page_parm_{/literal}{$name}{literal} = '&time=';
    }
    var History = window.History;
    History.pushState({name:'{/literal}{$name}{literal}', rel:'{/literal}{$params.time}{literal}'});
    {/literal}
        {$orig_params = ['controller' => 'event', 'action' => 'showall', 'src' => $params.src]}
    {literal}
    var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
    var cfg = {
                method: "POST",
                headers: { 'X-Transaction': 'Load Month'},
                arguments : { 'X-Transaction': 'Load Month'}
            };
    var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=event&action=showall&view=month&ajax_action=1&src={/literal}{$__loc->src}{literal}";

	// Popup calendar
	var cal = new Y.Calendar('J_popup_closeable{/literal}{$__loc->src|replace:'@':'_'}{literal}',{
		popup:true,
		closeable:true,
		startDay:{/literal}{$smarty.const.DISPLAY_START_OF_WEEK}{literal},
		date:today,
		action:['click'],
//        useShim:true
	}).on('select',function(d){
		var unixtime = parseInt(d / 1000);
        {/literal}
        {if $smarty.const.AJAX_PAGING}
            {literal}
                cfg.data = "time="+unixtime;
                var request = Y.io(sUrl_{/literal}{$name}{literal}, cfg);
                monthcal_{/literal}{$name}{literal}.setContent(Y.Node.create('{/literal}{loading title="Loading Month"|gettext}{literal}'));
            {/literal}
        {else}
            {if ($smarty.const.SEF_URLS == 1)} {literal}
                window.location = eXp.PATH_RELATIVE + 'event/showall/time/' + unixtime + '/src/{/literal}{$__loc->src}{literal}';
            {/literal} {else} {literal}
                window.location = eXp.PATH_RELATIVE + 'index.php?controller=event&action=showall&time=' + unixtime + '&src={/literal}{$__loc->src}{literal}';
            {/literal} {/if}
        {/if}
        {literal}
	});
    Y.one('#J_popup_closeable{/literal}{$__loc->src|replace:'@':'_'}{literal}').on('click',function(d){
        cal.show();
    });

    // ajax load new month
	var handleSuccess = function(ioId, o){
        if(o.responseText){
            monthcal_{/literal}{$name}{literal}.setContent(o.responseText);
            monthcal_{/literal}{$name}{literal}.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    Y.Get.script(n.get('src'));
                };
            });
            monthcal_{/literal}{$name}{literal}.all('link').each(function(n){
                Y.Get.css(n.get('href'));
            });
            Y.one('#lb-bg').setStyle('display', 'none');
//            monthcal_{/literal}{$name}{literal}.setStyle('opacity', 1);
        } else {
            Y.one('#month-{/literal}{$name}{literal}.loadingdiv').remove();
            monthcal_{/literal}{$name}{literal}.setContent('Unable to load content');
            monthcal_{/literal}{$name}{literal}.setStyle('opacity', 1);
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "monthcal nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

{/literal}
{if $smarty.const.AJAX_PAGING}
    {literal}
    monthcal_{/literal}{$name}{literal}.delegate('click', function(e){
        e.halt();
        History.pushState({name:'{/literal}{$name}{literal}', rel:e.currentTarget.get('rel')}, e.currentTarget.get('title').trim(), orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + e.currentTarget.get('rel'));
        cfg.data = "time="+e.currentTarget.get('rel');
        var request = Y.io(sUrl_{/literal}{$name}{literal}, cfg);
        monthcal_{/literal}{$name}{literal}.setContent(Y.Node.create('{/literal}{loading title="Loading Month"|gettext}{literal}'));
//        monthcal_{/literal}{$name}{literal}.setStyle('opacity', 0.5);
//        Y.one('#lb-bg').setStyle('display', 'block');
    }, 'a.evnav');

    // Watches the browser history for changes
    window.addEventListener('popstate', function(e) {
        state = History.getState();
        if (state.data.name == '{/literal}{$name}{literal}') {
            // moving to a new month
            cfg.data = "time="+state.data.rel;
            var request = Y.io(sUrl_{/literal}{$name}{literal}, cfg);
            monthcal_{/literal}{$name}{literal}.setContent(Y.Node.create('{/literal}{loading title="Loading Month"|gettext}{literal}'));
        }
    });
    {/literal}
{/if}
{literal}

});
{/literal}
{/script}
