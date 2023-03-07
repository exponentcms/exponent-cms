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

{css unique="calendar-form-add" corecss="tables"}

{/css}

<div class="module events add">
    <h1>{'Add New Event Date'|gettext}</h1>
    <h3>{$event->title}</h3>
    <blockquote>{'Add additional dates to an existing event as you choose below.'|gettext}</blockquote>
    {form action=add_selected}
        {control type=hidden name=id value=$event->id}
        {$dates = $event->eventdate}

        {control type="yuicalendarcontrol" name="eventdate" label="New Event Date"|gettext showtime=false}
        {control type="checkbox" name="is_allday" label="All Day Event?"|gettext value=1 checked=$event->is_allday readonly=1}
        {if !$event->is_allday}
        {control type="datetimecontrol" name="eventstart" label="Start Time"|gettext showdate=false value=$event->eventstart+$event->eventdate[0]->date disabled=$event->is_allday readonly=1}
        {control type="datetimecontrol" name="eventend" label="End Time"|gettext showdate=false value=$event->eventend+$event->eventdate[0]->date disabled=$event->is_allday readonly=1}
        {/if}

        {exp_include file="_recurring.tpl"}
        {control type=buttongroup submit='Add Selected'|gettext}
    {/form}
</div>
