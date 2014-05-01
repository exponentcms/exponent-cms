{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{uniqueid prepend="countdown" assign="name"}

{css unique="countdown" link="`$asset_path`css/countdown.css"}

{/css}

<div class="module countdown show">

    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if !$config}
        <strong style="color:red">{"To Display the 'Countdown' Module, you MUST First 'Configure Settings'"|gettext|cat:"!"}</strong>
    {else}    
        {if $config.title}<h2 class="clocktitle">{$config.title}</h2>{/if}

        {*<script type="text/javascript">*}
            {*TargetDate = "{$config.count}";*}
            {*BackColor = "";*}
            {*ForeColor = "";*}
            {*CountActive = true;*}
            {*CountStepper = -1;*}
            {*LeadingZero = true;*}
            {*DisplayFormat = "D:%%D%% H:%%H%% M:%%M%% S:%%S%%";*}
            {*FinishMessage = "{$config.message}";*}
        {*</script>*}
        {*<script type="text/javascript" src="{$asset_path}/js/countdown.js"></script>*}
        <div id="countdown"{if $config.light} class="light"{/if}></div>
        <p id="note"></p>
        {*<div class="bodycopy">*}
            {*{$config.body}*}
        {*</div>*}
    {/if}
</div>

{script unique="`$name`" jquery="jquery.countdown"}
{literal}
    $(function(){
    	var note = $('#note'),
    		ts = new Date("{/literal}{$config['date-count']} {$config['time-h-count']}:{$config['time-m-count']} {$config['ampm-count']}{literal}");

    	$('#countdown').countdown({
    		timestamp	: ts,
    		callback	: function(days, hours, minutes, seconds){
    			var message = "";
                {/literal}{if $config.displaytext}{literal}
    			message += days + " day" + ( days==1 ? '':'s' ) + ", ";
    			message += hours + " hour" + ( hours==1 ? '':'s' ) + ", ";
    			message += minutes + " minute" + ( minutes==1 ? '':'s' ) + " and ";
    			message += seconds + " second" + ( seconds==1 ? '':'s' ) + " <br />";
                {/literal}{/if}{literal}
                {/literal}{if $config.displaydate}{literal}
                message += "{/literal}{'Until'|gettext} {literal}";
                message += ts.toLocaleString() + " <br />";
//                message += "{/literal}{$config['date-count']} "{literal};
//                message += "{/literal}{$config['time-h-count']}:{$config['time-m-count']} {$config['ampm-count']}{literal}" + " <br />";
                {/literal}{/if}{literal}
                message += "{/literal}{$config.body|trim}{literal}";
    			note.html(message);
    		},
    		finishedCallback	: function(){
    			note.html("{/literal}{$config.message|trim}{literal}");
    		},
    	});
    });
{/literal}
{/script}

{* see http://tutorialzine.com/2011/12/countdown-jquery/ for more info on this script/ *}
