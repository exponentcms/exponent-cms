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

{css unique="showalleventregistrations" corecss="tables"}

{/css}

<div class="store events manage">
    <h1>{'Manage Event Registrations'|gettext}</h1>
    {permissions}
    <div class="module-actions">
        {if $permissions.create == true || $permissions.edit == true}
            {icon class="add" controller=store action=edit product_type=eventregistration text="Add an event"|gettext}
        {/if}
    </div>
    {/permissions}
    <div id="products">
        {pagelinks paginate=$page top=1}
        <table class="exp-skin-table">
                <thead>
                    <tr>
                        {$page->header_columns}
                        <th>{'Action'|gettext}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$page->records item=listing name=listings}
                        <tr class="{cycle values="odd,even"}">
                            <td><a href="{link controller=store action=edit id=$listing->id}">{$listing->title}</a></td>
                            <td>{$listing->eventdate|date_format:"%b %d,'%y"} {($listing->eventdate+$listing->event_starttime)|date_format:"%l:%M %p"}</td>
                            <td>{$listing->number_of_registrants} {'of'|gettext} {$listing->quantity}</td>
                            <td>
                            {icon img='groupperms.png' action=view_registrants record=$listing title="View Registrants"|gettext}
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {pagelinks paginate=$page bottom=1}
    </div>
</div>
