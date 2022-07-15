{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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

    {$myloc=serialize($__loc)}
	<table id="calendar" summary="{$moduletitle|default:'Calendar'|gettext}">
        <div class="caption">
            <span class="d-none d-sm-inline">&laquo;</span>&#160;
            <a class="evnav module-actions" href="{link action=showall time=$prevmonth3}" rel="{$prevmonth3}" title="{$prevmonth3|format_date:"%B %Y"}">{$prevmonth3|format_date:"%b"}</a><span class="d-none d-sm-inline">&#160;</span>&#160;&laquo;&#160;
            <a class="evnav module-actions" href="{link action=showall time=$prevmonth2}" rel="{$prevmonth2}" title="{$prevmonth2|format_date:"%B %Y"}">{$prevmonth2|format_date:"%b"}</a><span class="d-none d-sm-inline">&#160;</span>&#160;&laquo;&#160;
            <a class="evnav module-actions" href="{link action=showall time=$prevmonth}" rel="{$prevmonth}" title="{$prevmonth|format_date:"%B %Y"}">{$prevmonth|format_date:"%b"}</a><span class="d-none d-sm-inline">&#160;</span>&#160;&laquo;&#160;<span class="d-none d-sm-inline">&#160;&#160;&#160;&#160;</span>
            <strong><span class="d-none d-sm-inline">{$time|format_date:"%B %Y"}</span><span class="d-inline d-sm-none">{$time|format_date:"%b %Y"}</span></strong><span class="d-none d-sm-inline">&#160;</span>&#160;{printer_friendly_link view='showall' text=''}{export_pdf_link view='showall' text=''}<span class="d-none d-sm-inline">&#160;&#160;&#160;</span>&#160;&raquo;<span class="d-none d-sm-inline">&#160;&#160;</span>
            <input type='hidden' id='month{$__loc->src|replace:'@':'_'}' value="{$time|format_date:"%Y%m%d"}"/>
            <a class="evnav module-actions" href="{link action=showall time=$nextmonth}" rel="{$nextmonth}" title="{$nextmonth|format_date:"%B %Y"}">{$nextmonth|format_date:"%b"}</a><span class="d-none d-sm-inline">&#160;</span>&#160;&raquo;&#160;
            <a class="evnav module-actions" href="{link action=showall time=$nextmonth2}" rel="{$nextmonth2}" title="{$nextmonth2|format_date:"%B %Y"}">{$nextmonth2|format_date:"%b"}</a><span class="d-none d-sm-inline">&#160;</span>&#160;&raquo;&#160;
            <a class="evnav module-actions" href="{link action=showall time=$nextmonth3}" rel="{$nextmonth3}" title="{$nextmonth3|format_date:"%B %Y"}">{$nextmonth3|format_date:"%b"}</a>&#160;<span class="d-none d-sm-inline">&#160;&raquo;</span>
        </div>
		<tr class="daysoftheweek">
            {if $config.show_weeks}<th></th>{/if}
			{if $smarty.const.DISPLAY_START_OF_WEEK == 0}
			<th scope="col" abbr="{$daynames.med.0}" title="{$daynames.long.0}"><span class="d-none d-sm-inline">{$daynames.long.0}</span><span class="d-inline d-sm-none">{$daynames.med.0}</span></th>
			{/if}
			<th scope="col" abbr="{$daynames.med.1}" title="{$daynames.long.1}"><span class="d-none d-sm-inline">{$daynames.long.1}</span><span class="d-inline d-sm-none">{$daynames.med.1}</span></th>
            <th scope="col" abbr="{$daynames.med.2}" title="{$daynames.long.2}"><span class="d-none d-sm-inline">{$daynames.long.2}</span><span class="d-inline d-sm-none">{$daynames.med.2}</span></th>
            <th scope="col" abbr="{$daynames.med.3}" title="{$daynames.long.3}"><span class="d-none d-sm-inline">{$daynames.long.3}</span><span class="d-inline d-sm-none">{$daynames.med.3}</span></th>
            <th scope="col" abbr="{$daynames.med.4}" title="{$daynames.long.4}"><span class="d-none d-sm-inline">{$daynames.long.4}</span><span class="d-inline d-sm-none">{$daynames.med.4}</span></th>
            <th scope="col" abbr="{$daynames.med.5}" title="{$daynames.long.5}"><span class="d-none d-sm-inline">{$daynames.long.5}</span><span class="d-inline d-sm-none">{$daynames.med.5}</span></th>
            <th scope="col" abbr="{$daynames.med.6}" title="{$daynames.long.6}"><span class="d-none d-sm-inline">{$daynames.long.6}</span><span class="d-inline d-sm-none">{$daynames.med.6}</span></th>
			{if $smarty.const.DISPLAY_START_OF_WEEK != 0}
            <th scope="col" abbr="{$daynames.med.0}" title="{$daynames.long.0}"><span class="d-none d-sm-inline">{$daynames.long.0}</span><span class="d-inline d-sm-none">{$daynames.med.0}</span></th>
			{/if}
		</tr>
        {$dayts=$now}
        {$dst=false}
		{foreach from=$monthly item=week key=weeknum}
            {*{$moredata=0}*}
			{*{foreach name=w from=$week key=day item=events}*}
                {*{$number=$counts[$weeknum][$day]}*}
                {*{if $number > -1}{$moredata=1}{/if}*}
			{*{/foreach}*}
			{*{if $moredata == 1}*}
                <tr class="week{if $currentweek == $weeknum} currentweek{/if}">
                    {if $config.show_weeks}
                        <td class="week{if $currentweek == $weeknum} currentweek{/if}">{$weeknum}</td>
                    {/if}
                    {foreach name=w from=$week key=day item=items}
                        {$number=$counts[$weeknum][$day]}
                        <td class="{if $number == -1}notinmonth{elseif $dayts == $today}today{else}oneday{/if}">
                            {if $number > -1}
                                {if $number == 0}
                                    <span class="number{if $dayts == $today} today{/if}">
                                        {$day}
                                    </span>
                                {else}
                                    <a class="number" href="{link action=showall view=showall_Day time=$dayts}" title="{$dayts|format_date:'%A, %B %e, %Y'}">{$day}</a>
                                {/if}
                            {/if}
                            {foreach name=e from=$items item=item}
                                {if !empty($item->color)}
                                    {$style = " style=\"background:`$item->color`;color:`$item->color|contrast`;"}
                                {else}
                                    {$style = ''}
                                {/if}
                                {$alldaystyle = ''}
                                {if $config.show_allday && $item->is_allday}
                                    {if empty($style)}
                                        {$alldaystyle = ' style="'}
                                    {else}
                                        {$alldaystyle = $style}
                                    {/if}
                                    {$alldaystyle = "`$alldaystyle` border-color:`$item->color|brightness:-75`;border-style:solid;padding-left:2px;border-top:0;border-bottom:0;border-right:0;\""}
                                {/if}
                                {if !empty($style)}
                                    {$style = "`$style`\""}
                                {/if}
                                {if $item->is_allday}
                                    {$title = 'All Day'|gettext}
                                {elseif $item->eventstart != $item->eventend}
                                    {$title = $item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
                                    {$title = $title|cat:' '}
                                    {$title = $title|cat:'to'|gettext}
                                    {$title = $title|cat:' '}
                                    {$title = "`$title``$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT`"}
                                {else}
                                    {$title = $item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
                                {/if}
                                {$title = "`$title` - \n `$item->body|summarize:"html":"para"`"}
                                {if $item->is_cancelled}{$title = 'Event Cancelled'|gettext|cat:"\n"|cat:$title}{/if}
                                {*<div class="calevent{if $dayts == $today} today{/if}"{$style}>*}
                                    <a class="calevent{* if $dayts == $today} today{/if *}{if $item->is_cancelled} cancelled{/if}{if $config.lightbox && $item->location_data != 'eventregistration' && substr($item->location_data,1,8) != 'calevent'} calpopevent{elseif $config.lightbox && substr($item->location_data,1,8) == 'calevent'} icalpopevent{/if}"
                                        {$alldaystyle}
                                        {if substr($item->location_data,1,8) != 'calevent'}href="{if $item->location_data != 'eventregistration'}{link action=show date_id=$item->date_id}{else}{link controller=eventregistration action=show title=$item->title}{/if}"
                                            {if $item->date_id}id={$item->date_id}{/if}
                                        {/if}
                                        {if $config.lightbox && substr($item->location_data,1,8) == 'calevent'}rel="{$item->eventdate->date|format_date:'%A, %B %e, %Y'}"{/if}
                                        title="{$title}"{$style}>
                                        {if $item->expFile[0]->url != ""}
                                            <div class="image">
                                                {if $item->date_id}
                                                    {img file_id=$item->expFile[0]->id title=$title id=$item->date_id w=92 class="img-responsive"}
                                                {else}
                                                    {img file_id=$item->expFile[0]->id title=$title w=92 class="img-responsive"}
                                                {/if}
                                                {clear}
                                            </div>
                                        {/if}
                                        {$item->title}
                                    </a>
                                    {permissions}
                                        {if substr($item->location_data,0,3) == 'O:8'}
                                        <div class="calevent item-actions"{$style}>
                                                {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                                    {if $myloc != $item->location_data}
                                                        {if $permissions.manage}
                                                            {icon img='arrow_merge.png' action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                                        {else}
                                                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                                        {/if}
                                                    {/if}
                                                    {icon img="edit.png" action=edit record=$item date_id=$item->date_id title="Edit this Event"|gettext}
                                                    {icon img="copy.png" action=copy record=$item date_id=$item->date_id title="Copy this Event"|gettext}
                                                {/if}
                                                {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                                                    {if $item->is_recurring == 0}
                                                        {icon img="delete.png" action=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                                    {else}
                                                        {icon img="delete.png" action=delete_recurring record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                                    {/if}
                                                {/if}
                                            </div>
                                        {/if}
                                    {/permissions}
                                {*</div>*}
                            {/foreach}
                            {if $number != -1}{$dayts=$dayts+86400}
                                {if !$dst}
                                    {if (date('I',$now) && !date('I',$dayts))}
                                        {$dayts=$dayts+3600}
                                        {$dst=true}
                                    {elseif (!date('I',$now) && date('I',$dayts))}
                                        {$dayts=$dayts-3600}
                                        {$dst=true}
                                    {/if}
                                {/if}
                            {/if}
                        </td>
                    {/foreach}
                </tr>
			{*{/if}*}
		{/foreach}
	</table>

{if $config.lightbox}
{script unique="shadowbox-`$__loc->src`" jquery='jquery.colorbox'}
{literal}
    $('.events.default a.calpopevent').click(function(e) {
        target = e.target;
        $.colorbox({
            href: EXPONENT.PATH_RELATIVE+"index.php?controller=event&action=show&view=show&ajax_action=1&date_id="+target.id+"&src={/literal}{$__loc->src}{literal}",
            title: target.text + ' - ' + '{/literal}{'Event'|gettext}{literal}',
            maxWidth: "100%",
            onComplete : function() {
                $('img').on('load', function() {
                    $(this).colorbox.resize();
                });
            },
            close:'<i class="fas fa-close" aria-label="close modal"></i>',
            previous:'<i class="fas fa-chevron-left" aria-label="previous photo"></i>',
            next:'<i class="fas fa-chevron-right" aria-label="next photo"></i>',
            slideshow:'<i class="fas fa-picture-o" aria-label="slideshow"></i>',
        });
        e.preventDefault();
    });
    $('.events.default a.icalpopevent').click(function(e) {
        target = e.target;
        $.colorbox({
            html: '<h2>' + target.text + '</h2><p>' + target.rel +  '</p><p>'  + Linkify(target.title.replace(/\n/g,'<br />')) + '</p>',
            title: target.text + ' - ' + '{/literal}{'Event'|gettext}{literal}',
            maxWidth: "100%",
            close:'<i class="fas fa-fw fa-close" aria-label="close modal"></i>',
            previous:'<i class="fas fa-fw fa-chevron-left" aria-label="previous photo"></i>',
            next:'<i class="fas fa-fw fa-chevron-right" aria-label="next photo"></i>',
            slideshow:'<i class="fas fa-fw fa-picture-o" aria-label="slideshow"></i>',
        });
        e.preventDefault();
    });

    function Linkify(inputText) {
        //URLs starting with http://, https://, or ftp://
        var replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
        var replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

        //URLs starting with www. (without // before it, or it'd re-link the ones done above)
        var replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
        var replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

        //Change email addresses to mailto:: links
        var replacePattern3 = /(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
        var replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

        return replacedText
    }
{/literal}
{/script}
{/if}