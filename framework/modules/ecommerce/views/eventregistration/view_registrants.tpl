{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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

{css unique="viewregistrants" corecss="tables"}

{/css}

<div class="store showall">
            <div class="form_header">
		<h1>{'Event Info'|gettext}</h1>
                <p><span class="label">{'Event Date:'|gettext} </span><span class="value">{$event->eventdate|date_format:"%A, %B %e, %Y"}</span>{br}
                <span class="label">{'Start Time:'|gettext} </span><span class="value">{$event->event_starttime|date_format:"%I:%M %p"}</span>{br}
                <span class="label">{'End Time:'|gettext} </span><span class="value">{$event->event_endtime|date_format:"%I:%M %p"}</span>{br}
                <span class="label">{'Price per person:'|gettext} </span><span class="value">{currency_symbol}{$event->base_price|number_format:2}</span>{br}
                <span class="label">{'Seats Registered:'|gettext} </span><span class="value">{$event->number_of_registrants} of {$event->quantity}</span>{br}
                <span class="label">{'Registration Closes:'|gettext} </span><span class="value">{$event->signup_cutoff|date_format:"%A, %B %e, %Y"}</span></p>{br}
            </div>

    <div class="events">
	<table class="exp-skin-table">
            <thead>
        	<tr><th>{'Registrant Name'|gettext}</th><th>{'Registrant Email'|gettext}</th><th>{'Registrant Phone'|gettext}</th></tr>
            </thead>
	    <tbody>
		{if $registrants|count > 0}
		    {foreach from=$registrants item=registrant}
                <tr class="{cycle values="odd,even"}">
                    <td>{$registrant.name}</td>
                    <td>{$registrant.email}</td>
                    <td>{$registrant.phone}</td>
                </tr>
            {/foreach}
		{else}
		<tr class="{cycle values="odd,even"}">
            <td colspan="3">{'There are currently no registrants.'|gettext}</td>
		</tr>
		{/if}
	    </tbody>
        </table>
    </div>
	<h2><a href="{link controller=eventregistration action=export id=$event->id}">{'Export This Event Data (Experimental)'|gettext}</a></h2>
</div>
