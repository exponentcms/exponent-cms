{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<h2>{"Aggregate content from similar modules"|gettext}</h2>
{control type="checkbox" name="add_source" label="Separate this blog's content"|gettext|cat:"?" checked=$config.add_source value=1}
<hr />
{control type="checkbox" name="noeditagg" label="Prevent editing aggregate items"|gettext value=1 checked=$config.noeditagg}
<hr />
<table class="exp-skin-table">
    <thead>
        <tr>
            <th>{""|gettext}</th>
            {*{$page->header_columns}*}
            <th>{"Title"|gettext}</th>
            <th>{"Page"|gettext}</th>
        </tr>
    </thead>
    <tbody>
{*{foreach from=$pullable_modules item=mod key=src}*}
{foreach from=$page->records item=mod key=src name=mod}
        <tr class="{cycle values="even,odd"}">
            <td width="20">
                {control type="checkbox" name="aggregate[]" value=$mod->src checked=$config.aggregate}
            </td>
            <td>
                {$mod->title}
            </td>
            <td>
                {$mod->section}
            </td>
        </tr>
{foreachelse}
        <tr><td colspan=3>{'There doesn\'t appear to be any other modules installed that you can aggregate data from'|gettext}</td></tr>
{/foreach}
    </tbody>
</table>
