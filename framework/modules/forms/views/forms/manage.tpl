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

{css unique="manageforms" corecss="admin-global,tables"}

{/css}

<div class="module forms manage">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Forms"|gettext) module="manage-site-forms"}
        </div>
        <h2>{"Site Forms Manager"|gettext}</h2>
    </div>
    <div class="module-actions">
        {icon class="add" action="edit_form" text="Create a New Form"|gettext}
        {icon class="import" action="import_csv" text="Import CSV File"|gettext}
    </div>
    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                {if $__loc->src}
                    <th>
                        {"Assigned"|gettext}
                    </th>
                {/if}
                <th>
                    {"Form Name"|gettext}
                </th>
                <th>
                    {"Database"|gettext}
                </th>
                <th width="37%">
                    {"Action"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$forms item=form}
                <tr class="{cycle values="odd,even"}">
                    {if $__loc->src}
                       <td>
                           {if $config.forms_id == $form->id}
                               <span class="active">{'Active'|gettext}</span>
                           {else}
                               <a class="inactive" href="{link action=activate id=$form->id}" title="Assign this Form to the Module"|gettext>{'Activate'|gettext}</a>
                           {/if}
                       </td>
                   {/if}
                    <td>
                        {$form->title}
                    </td>
                    <td>
                        {if $form->is_saved}
                            {icon class="view" action=showall id=$form->id text='View Data'|gettext|cat:" (`$form->count`)"}
                            {icon class="downloadfile" action=export_csv id=$form->id text="Export CSV"|gettext}
                            {icon class="downloadfile" action=export_eql id=$form->id text="Export EQL"|gettext}
                        {else}
                            {'Data Not Saved'|gettext}
                        {/if}
                    </td>
                    <td>
                        <div class="item-actions">
                            {icon img='edit.png' action=edit_form record=$form title="Edit this Form"|gettext}
                            {icon img='copy.png' action=edit_form copy=1 record=$form title="Copy this Form"|gettext}
                            {icon img='configure.png' action=design_form record=$form title="Design this Form"|gettext|cat:" (`$form->control_count` "|cat:'Controls'|gettext|cat:')'}
                            {icon img='delete.png' action=delete_form record=$form title="Delete this Form"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this form and ALL the saved data?"|gettext)|cat:"');"}
                        </div>
                    </td>
                </tr>
            {foreachelse}
                <tr><td colspan=3>{'No Forms were found in the system.'|gettext}</td></tr>
            {/foreach}
        </tbody>
    </table>
</div>
