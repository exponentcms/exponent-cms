{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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
            {icon class="monthviewlink" action=showall time=$time title='View Entire Month'|gettext text='View Month'|gettext nofollow=1}
        {/if}
        {permissions}
            {if $permissions.manage}
                {if !bs()}
                    {nbsp count=2}|{nbsp count=2}
                {/if}
                {icon class="adminviewlink" action=showall view='showall_Administration' time=$time text='Administration View'|gettext nofollow=1}
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
        <span class="input-group-append" style="display:inherit;border-radius:4px;border-left:1px solid #ccc;cursor:pointer;width:auto">
            <span class="{if $smarty.const.USE_BOOTSTRAP_ICONS}bi-calendar3{else}fas fa-calendar{/if}"></span>
            {'Go to Date'|gettext}
        </span>
        <span class="loader"></span>
    </div>
    <div class="module events viewweek" id="week-{$name}">
        {exp_include file='week.tpl'}
    </div>
</div>

{script unique=$name|cat:'-popup' jquery="moment,tempus-dominus,jquery.history" src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"}
{literal}
    $(document).ready(function() {
        var monthcal_{/literal}{$name}{literal} = $('#week-{/literal}{$name}{literal}');
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
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=event&action=showall&view=week&ajax_action=1&src={/literal}{$__loc->src}{literal}";

        // Popup calendar
        var pop_{/literal}{$__loc->src|replace:'@':'_'}{literal}element = document.getElementById('j_input{/literal}{$__loc->src|replace:'@':'_'}{literal}');
        var pop_{/literal}{$__loc->src|replace:'@':'_'}{literal} = new tempusDominus.TempusDominus(pop_{/literal}{$__loc->src|replace:'@':'_'}{literal}element,{
            localization: {
                format: 'YYYYMMDD',
                locale: '{/literal}{str_replace("_", "-", $smarty.const.LOCALE)}{literal}',
            },
    //            extraFormats: ['YYYYMMDD','MM/YYYY'],
            display: {
                viewMode: 'months',
                buttons: {
                    today: true,
    //                    clear: false,
                    close: true
                },
                components: {
                    calendar: true,
                    date: false,
                    month: true,
                    year: true,
                    decades: true,
                    clock: false,
                    hours: false,
                    minutes: false,
                    seconds: false,
                },
            },
            defaultDate: '{/literal}{$time|format_date:"%m/%d/%Y"}{literal}',
    //            allowInputToggle: true,
        });

        if ({/literal}{if $smarty.const.USE_BOOTSTRAP_ICONS}1{else}0{/if}{literal}) {
            pop_{/literal}{$__loc->src|replace:'@':'_'}{literal}.updateOptions({
                display: {
                    icons: {
                        time: 'bi bi-clock',
                        date: 'bi bi-calendar3',
                        up: 'bi bi-arrow-up',
                        down: 'bi bi-arrow-down',
                        previous: 'bi bi-chevron-left',
                        next: 'bi bi-chevron-right',
                        today: 'bi bi-calendar-check',
                        clear: 'bi bi-trash',
                        close: 'bi bi-x',
                    },
                }
            });
        }

        pop_{/literal}{$__loc->src|replace:'@':'_'}{literal}element.addEventListener(tempusDominus.Namespace.events.hide,function(e){
            if (!moment($('#month{/literal}{$__loc->src|replace:'@':'_'}{literal}')[0].value, "YYYYMMDD").isSame(e.detail.date, 'month') || !moment($('#month{/literal}{$__loc->src|replace:'@':'_'}{literal}')[0].value, "YYYYMMDD").isSame(e.detail.date, 'year')) {
                var unixtime = moment(e.detail.date).unix();
            {/literal} {if $smarty.const.AJAX_PAGING}
                {literal}
                    $.ajax({
                        type: "POST",
                        headers: { 'X-Transaction': 'Load Week'},
                        url: sUrl_{/literal}{$name}{literal},
                        data: "time=" + unixtime,
                        success: handleSuccess_{/literal}{$name}{literal}
                    });
                    // monthcal_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Week"|gettext}{literal}'));
                    monthcal_{/literal}{$name}{literal}.prev().find('.loader').html($('{/literal}{loading span=1 title="Loading Week"|gettext}{literal}'));
                {/literal}
            {else}
            {if ($smarty.const.SEF_URLS == 1)} {literal}
                window.location = eXp.PATH_RELATIVE + 'event/showall/view/showall_Week/time/' + unixtime + '/src/{/literal}{$__loc->src}{literal}';
            {/literal} {else} {literal}
                window.location = eXp.PATH_RELATIVE + 'index.php?controller=event&action=showall&view=showall_Week&time=' + unixtime + '&src={/literal}{$__loc->src}{literal}';
            {/literal} {/if}
            {/if} {literal}
            }
        });

    {/literal} {if $smarty.const.AJAX_PAGING} {literal}
        // ajax load new week
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
                $('#week-{/literal}{$name}{literal}.loadingdiv').remove();
                monthcal_{/literal}{$name}{literal}.html('Unable to load content');
                monthcal_{/literal}{$name}{literal}.css('opacity', 1);
            }
            monthcal_{/literal}{$name}{literal}.prev().find('.loader').html('');
            pop_{/literal}{$__loc->src|replace:'@':'_'}{literal}.dates.setValue($('#week{/literal}{$__loc->src|replace:'@':'_'}{literal}')[0].value);
        };

        monthcal_{/literal}{$name}{literal}.delegate('a.evnav', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, $(this)[0].title.trim(), orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new week
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Week'},
                url: sUrl_{/literal}{$name}{literal},
                data: "time=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
            // monthcal_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Week"|gettext}{literal}'));
            monthcal_{/literal}{$name}{literal}.prev().find('.loader').html($('{/literal}{loading span=1 title="Loading Week"|gettext}{literal}'));
        });

        // Watches the browser history for changes
        window.addEventListener('popstate', function(e) {
            state = History.getState();
            if (state.data.name == '{/literal}{$name}{literal}') {
                // moving to a new week
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Load Week'},
                    url: sUrl_{/literal}{$name}{literal},
                    data: "time=" + state.data.rel,
                    success: handleSuccess_{/literal}{$name}{literal}
                });
                // monthcal_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Week"|gettext}{literal}'));
                monthcal_{/literal}{$name}{literal}.prev().find('.loader').html($('{/literal}{loading span=1 title="Loading Week"|gettext}{literal}'));
            }
        });
    {/literal} {/if} {literal}
    });
{/literal}
{/script}
