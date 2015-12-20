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

{uniqueid prepend="countdown" assign="name"}

{css unique="countdown" link="`$asset_path`css/countdown.css"}

{/css}

<div class="module countdown show show_circles">

    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if !$config}
        <strong style="color:red">{"To Display the 'Countdown' Module, you MUST First 'Configure Settings'"|gettext|cat:"!"}</strong>
    {else}    
        {if $config.title}<h2 class="clocktitle">{$config.title}</h2>{/if}
        {$date = explode('/',$config['date-count'])}
        {if $config['ampm-count'] == 'pm'}{$hour = $config['time-h-count'] + 12}{else}{$hour = $config['time-h-count']}{/if}
        <div id="countdown" data-date="{$date.2}-{$date.0}-{$date.1} {$hour}:{$config['time-m-count']}:00" style="max-height: 300px;"></div>
        <p id="note"></p>
    {/if}
</div>

{script unique="`$name`" jquery="TimeCircles"}
{literal}
    $(function(){
    	var note = $('#note'),
    		ts = new Date("{/literal}{$config['date-count']} {$config['time-h-count']}:{$config['time-m-count']} {$config['ampm-count']}{literal}");

        var messages = function(unit, value, total) {
            if (total > 0) {
                var message = "";
                {/literal}{if $config.displaytext}{literal}
                var days = parseInt(total / 86400);
                var tmp = total - (days * 86400);
                var hours = parseInt(tmp / 3600);
                var tmp = tmp - (hours * 3600);
                var minutes = parseInt(tmp / 60);
                var seconds = tmp - (minutes * 60);
                message += days + " day" + ( days==1 ? '':'s' ) + ", ";
                message += hours + " hour" + ( hours==1 ? '':'s' ) + ", ";
                message += minutes + " minute" + ( minutes==1 ? '':'s' ) + " and ";
                message += seconds + " second" + ( seconds==1 ? '':'s' ) + " <br />";
                {/literal}{/if}{literal}
                {/literal}{if $config.displaydate}{literal}
                message += "{/literal}{'Until'|gettext} {literal}";
                message += ts.toLocaleString() + " <br />";
                {/literal}{/if}{literal}
                message += "{/literal}{$config.body|trim}{literal}";
                note.html(message);
            } else {
                note.html("{/literal}{$config.message|trim}{literal}");
            }
        };

        // create countdown widget with listener
        $("#countdown").TimeCircles({
        }).addListener(messages);

        // auto resize based on window size
        $(window).resize(function(){
            $("#countdown").TimeCircles().rebuild();
        });
    });
{/literal}
{/script}
