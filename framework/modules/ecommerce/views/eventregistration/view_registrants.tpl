{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{css unique="event-show1" link="`$asset_path`css/eventregistration.css"}

{/css}

<div class="store  store show event-registration">
    <div class="form_header">
        {permissions}
            <div class="module-actions">
                {if $permissions.create == true || $permissions.edit == true}
                    {icon class="add" controller=store action=edit product_type=eventregistration text="Add an event"|gettext}
                {/if}
                {if $permissions.manage == 1}
                     {icon action=manage text="Manage Events"|gettext}
                {/if}
            </div>
        {/permissions}
        <h1>{'Event Info'|gettext}</h1>
        <h2>{$event->title}</h2>
        {permissions}
            <div class="item-actions">
                {if $permissions.edit == true}
                    {icon controller="store" action=edit record=$event}
                {/if}
                {if $permissions.delete == true}
                    {icon controller="store" action=delete record=$event}
                {/if}
            </div>
        {/permissions}

        <div id="eventregform">
            <span class="label">{'Event Date'|gettext}: </span>
            <span class="value">{$event->eventdate|date_format:"%A, %B %e, %Y"}</span>{br}
            <span class="label">{'Start Time'|gettext}: </span>
            <span class="value">{($event->eventdate+$event->event_starttime)|date_format:"%l:%M %p"}</span>{br}
            <span class="label">{'End Time'|gettext}: </span>
            <span class="value">{($event->eventdate+$event->event_endtime)|date_format:"%l:%M %p"}</span>{br}
            <span class="label">{'Price per person:'|gettext} </span>
            <span class="value">{if $event->base_price}{currency_symbol}{$event->base_price|number_format:2}{else}{'No Cost'|gettext}{/if}</span>{br}
            <span class="label">{'Seats Registered:'|gettext} </span>
            <span class="value">{$registrants|count} of {$event->quantity}</span>{br}
            <span class="label">{'Registration Closes:'|gettext} </span>
            <span class="value">{$event->signup_cutoff|date_format:"%A, %B %e, %Y"}</span>
        </div>
    </div>
    {br}
    <div class="events">
        <table class="exp-skin-table">
            <thead>
                <tr>
                    <th>{'Registrant Name:'|gettext}</th>
                    <th>{'Registrant Email:'|gettext}</th>
                    <th>{'Registrant Phone:'|gettext}</th>
                </tr>
            </thead>
            <tbody>
                {if $registrants|count > 0}
                    {foreach from=$registrants item=registrant}
                        {*{get_user user=$user assign=registrant}*}
                        <tr class="{cycle values="odd,even"}">
                            <td>{$registrant.name}</td>
                            <td>{$registrant.email}</td>
                            <td>{$registrant.phone}</td>
                        </tr>
                    {/foreach}
                {else}
                    <tr class="{cycle values="odd,even"}">
                        <td colspan="3">{'There is currently no one registered'|gettext}</td>
                    </tr>
                {/if}
            </tbody>
        </table>
    </div>
    {if $registrants|count > 0}
        {icon class=downloadfile controller=eventregistration action=export id=$event->id text='Export this Event Roster'|gettext}
    {/if}
</div>
