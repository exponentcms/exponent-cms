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

<h2>{"Blog"|gettext}</h2>

{control type="files" name="blog_image" subtype="blog_image" label="Blog Banner Image"|gettext accept="image/*" value=$config['expFile'] limit='1'}
{control type="editor" name="blog_body" label="Blog HTML"|gettext value=$config.blog_body}

<table class="exp-skin-table">
    <thead>
        <tr>
            {*<th>{""|gettext}</th>*}
            <th><input type='checkbox' name='checkall' title="{'Select All/None'|gettext}" style="margin-left: 1px;" onChange="blogSelectAll(this.checked)"></th>
            {$tabno = $smarty.foreach.body.iteration-1}
            {$tabanchor = '#tab='|cat:$tabno|cat:'" alt="'}
            {$page['blog']->header_columns|replace:'" alt="':$tabanchor}
            {*<th>{"Title"|gettext}</th>*}
            {*<th>{"Page"|gettext}</th>*}
        </tr>
    </thead>
    <tbody>
{*{foreach from=$pullable_modules item=mod key=src}*}
{foreach from=$page['blog']->records item=mod key=src name=mod}
        <tr class="{cycle values="even,odd"}">
            <td width="20">
                {control type="checkbox" name="blog_aggregate[]" value=$mod->src checked=$config.blog_aggregate}
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

{script unique="aggregation-blg"}
    function blogSelectAll(val) {
        var checks = document.getElementsByName("blog_aggregate[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/script}
