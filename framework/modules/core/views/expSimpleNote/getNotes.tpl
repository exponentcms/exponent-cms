{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

<div class="module simplenote get-notes">
    <table>
        <thead>
            <tr>
                <th>
                    {if $title}{$title}{/if}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="note {cycle values="odd,even"}">
                <td>
                    <div class="module-actions">
                        {if $permissions.create}
                            {icon action=edit class="add" content_id=$content_id content_type=$content_type tab=$tab text="Add a Note"|gettext}
                        {/if}
                    </div>
                    {if $unapproved > 0}
                    <div class="unapproved">
                        || {'There are'|gettext} {$unapproved} {'notes awaiting approval'|gettext}.
                        {icon action=manage content_id=$content_id content_type=$content_type tab=$tab text="Approve Notes"|gettext} ||
                    </div>
                    {/if}
                    {$simplenotes->links}
                </td>
            </tr>
            {if !$hidenotes && $simplenotes->records|@count > 0}
                {foreach from=$simplenotes->records item=note name=simplenote}
                    <tr class="note {cycle values="odd,even"}">
                        <td>
                            <span class="attribution">{$note->name}</span> - <span class="date">{$note->edited_at|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}</span>
                            {permissions}
                                <div class="item-actions">
                                    {if $permissions.edit}
                                        {icon action=edit record=$note tab=$tab content_id=$content_id content_type=$content_type title="Edit this note"|gettext}
                                    {/if}
                                    {if $permissions.delete}
                                        {icon action=delete record=$note tab=$tab content_id=$content_id content_type=$content_type title="Delete this note"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this note?"|gettext)|cat:"');"}
                                    {/if}
                                </div>
                            {/permissions}
                            <div class="bodycopy">
                                <p>
                                    {$note->body}
                                </p>
                            </div>
                        </td>
                    </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>
</div>
