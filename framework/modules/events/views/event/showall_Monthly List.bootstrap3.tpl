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

<div class="module events">
	<div class="module-actions">
        {if !$config.disable_links}
    		{icon class="monthviewlink" action=showall time=$time text='Calendar View'|gettext}
            {if !bs()}
                {nbsp count=2}|{nbsp count=2}
            {/if}
            {*<span class="listviewlink"></span>{'List View'|gettext}*}
            {icon class="listviewlink" text='List View'|gettext}
        {/if}
		{permissions}
			{if $permissions.manage}
                {if !bs()}
                    {nbsp count=2}|{nbsp count=2}
                {/if}
                {icon class="adminviewlink" action=showall view=showall_Administration time=$time text='Administration View'|gettext}
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
    <div class='input-group module-actions' id='j_input{$__loc->src|replace:'@':'_'}' style="left:40%">
        <input type='hidden' class="form-control" />
        <span class="input-group-addon" style="border-radius:4px;border-left:1px solid #ccc;cursor:pointer;width:auto">
            <span class="glyphicon glyphicon-calendar"></span>
            {'Go to Date'|gettext}
        </span>
        <span class="loader"></span>
    </div>
    <div class="module events monthly" id="month-{$name}">
        {exp_include file='monthlist.tpl'}
    </div>
</div>

{script unique=$name|cat:'-popup' jquery="moment,bootstrap-datetimepicker,jquery.history"}
{literal}
    $(document).ready(function() {
        var monthcal_{/literal}{$name}{literal} = $('#month-{/literal}{$name}{literal}');
        var page_parm_{/literal}{$name}{literal} = '';
        if (EXPONENT.SEF_URLS) {
            page_parm_{/literal}{$name}{literal} = '/time/';
        } else {
            page_parm_{/literal}{$name}{literal} = '&time=';
        }
        var History = window.History;
        History.pushState({name:'{/literal}{$name}{literal}', rel:'{/literal}{$params.time}{literal}'});
        {/literal}
            {$orig_params = ['controller' => 'event', 'action' => 'showall', 'view' => $params.view, 'src' => $params.src]}
        {literal}
        var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=event&action=showall&view=monthlist&ajax_action=1&src={/literal}{$__loc->src}{literal}";

        // Popup calendar
        $('#j_input{/literal}{$__loc->src|replace:'@':'_'}{literal}').datetimepicker({
            format: 'MM/YYYY',
            extraFormats: ['YYYYMMDD','MM/YYYY'],
            locale: '{/literal}{$smarty.const.LOCALE}{literal}',
            showTodayButton: true,
            viewMode: 'months',
            showClose: true,
            defaultDate: '{/literal}{$time|format_date:"%Y%m%d"}{literal}',
//            allowInputToggle: true
        }).on('dp.hide',function(e){
            if (!moment($('#month{/literal}{$__loc->src|replace:'@':'_'}{literal}')[0].value, "YYYYMMDD").isSame(e.date, 'month') || !moment($('#month{/literal}{$__loc->src|replace:'@':'_'}{literal}')[0].value, "YYYYMMDD").isSame(e.date, 'year')) {
                var unixtime = e.date.unix();
            {/literal} {if $smarty.const.AJAX_PAGING}
                {literal}
                    $.ajax({
                        type: "POST",
                        headers: { 'X-Transaction': 'Load Month'},
                        url: sUrl_{/literal}{$name}{literal},
                        data: "time=" + unixtime,
                        success: handleSuccess_{/literal}{$name}{literal}
                    });
                    // monthcal_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Month"|gettext}{literal}'));
                    monthcal_{/literal}{$name}{literal}.prev().find('.loader').html($('{/literal}{loading span=1 title="Loading Month"|gettext}{literal}'));
                {/literal}
            {else}
            {if ($smarty.const.SEF_URLS == 1)} {literal}
                window.location = eXp.PATH_RELATIVE + 'event/showall/view/showall_Monthly+List/time/' + unixtime + '/src/{/literal}{$__loc->src}{literal}';
            {/literal} {else} {literal}
                window.location = eXp.PATH_RELATIVE + 'index.php?controller=event&action=showall&view=showall_Monthly+List&time=' + unixtime + '&src={/literal}{$__loc->src}{literal}';
            {/literal} {/if}
            {/if} {literal}
            }
        });

    {/literal} {if $smarty.const.AJAX_PAGING} {literal}
        // ajax load new month
        var handleSuccess_{/literal}{$name}{literal} = function(o, ioId){
            if(o){
                monthcal_{/literal}{$name}{literal}.html(o);
                monthcal_{/literal}{$name}{literal}.find('script').each(function(k, n){
                    if(!$(n).attr('src')){
                        eval($(n).html);
                    } else {
                        $.getScript($(n).attr('src'));
                    };
                });
                monthcal_{/literal}{$name}{literal}.find('link').each(function(k, n){
                    $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                });
            } else {
                $('#month-{/literal}{$name}{literal}.loadingdiv').remove();
                monthcal_{/literal}{$name}{literal}.html('Unable to load content');
                monthcal_{/literal}{$name}{literal}.css('opacity', 1);
            }
            monthcal_{/literal}{$name}{literal}.prev().find('.loader').html('');
            $('#j_input{/literal}{$__loc->src|replace:'@':'_'}{literal}').data("DateTimePicker").date($('#month{/literal}{$__loc->src|replace:'@':'_'}{literal}')[0].value);
        };

        monthcal_{/literal}{$name}{literal}.delegate('a.evnav', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, $(this)[0].title.trim(), orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new month
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Month'},
                url: sUrl_{/literal}{$name}{literal},
                data: "time=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
            // monthcal_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Month"|gettext}{literal}'));
            monthcal_{/literal}{$name}{literal}.prev().find('.loader').html($('{/literal}{loading span=1 title="Loading Month"|gettext}{literal}'));
        });

        // Watches the browser history for changes
        window.addEventListener('popstate', function(e) {
            state = History.getState();
            if (state.data.name == '{/literal}{$name}{literal}') {
                // moving to a new month
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Load Month'},
                    url: sUrl_{/literal}{$name}{literal},
                    data: "time=" + state.data.rel,
                    success: handleSuccess_{/literal}{$name}{literal}
                });
                // monthcal_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Month"|gettext}{literal}'));
                monthcal_{/literal}{$name}{literal}.prev().find('.loader').html($('{/literal}{loading span=1 title="Loading Month"|gettext}{literal}'));
            }
        });
    {/literal} {/if} {literal}
    });
{/literal}
{/script}
