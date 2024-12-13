{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{control type=hidden name=is_recurring value=$record->is_recurring}
{'This event is a recurring event, and occurs on the dates below.  Select which dates you wish to apply these edits to.'|gettext}
<div class="events recur-dates">
    <table cellspacing="0" cellpadding="2" width="100%" class="exp-skin-table">
        <tr>
            <td style="border:none;" colspan="2">
                {script unique="recurdates"}
                {literal}
                    function recur_selectUnselectAll(setChecked) {
                        var elems = document.getElementsByTagName("input");
                        for (var key = 0; key < elems.length; key++) {
                            if (elems[key].type == "checkbox" && elems[key].name.substr(0,6) == "dates[") {
                                elems[key].checked = setChecked;
                            }
                        }
                    }
                {/literal}
                {/script}
                <a href="#" onclick="recur_selectUnselectAll(true); return false;">{'Select All'|gettext}</a>
                &#160;/&#160;
                <a href="#" onclick="recur_selectUnselectAll(false); return false;">{'Unselect All'|gettext}</a>
            </td>
        </tr>
        {foreach from=$dates item=d}
            <tr class="{cycle values='even,odd'}">
                <td width="10" style="border:none;">
                    <input type="checkbox" name="dates[{$d->id}]" {if $d->id == $checked_date}checked="checked" {/if}/>
                </td>
                <td style="border:none;">
                    {$d->date|format_date}
                </td>
            </tr>
        {/foreach}
    </table>
</div>
