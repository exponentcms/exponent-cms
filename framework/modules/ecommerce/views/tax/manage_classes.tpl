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

{css unique="tax" corecss="tables"}

{/css}

<h1>{"Tax Class Manager"|gettext}</h1>

<div class="module-actions">
    {icon action=edit_class class="add" text="Add a Tax Class"|gettext}
</div>
{br}
<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
    <thead>
        <tr>
            <th>
                {'Name'|gettext}
            </th>
            <th>
                {'Action'|gettext}
            </th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$classes item=class key=key name=classes}
            <tr class="{cycle values="odd,even"}">
                <td>
                    {$class->name}
                </td>
                <td class="module-actions">
                    {icon action=edit_class record=$class img="edit.png" title='Edit tax class'|gettext}
                    {icon action=delete_class record=$class img="delete.png" title='Delete tax class along with rates'|gettext}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
{br}
<div class="module-actions">
    {icon action=manage class="reply" text="Return to Tax Manager"|gettext}
</div>
