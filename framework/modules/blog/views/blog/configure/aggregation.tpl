{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Aggregating Content"|gettext) module="aggregation"}
		</div>
        <h2>{"Aggregate content from similar modules"|gettext}</h2>
	</div>
</div>
{control type="checkbox" name="add_source" label='Segregate this blog\'s content'|gettext|cat:"?" checked=$config.add_source value=1 description='The default behavior is to aggregate all site blog posts into this module'|gettext}
<hr />
{control type="checkbox" name="noeditagg" label="Prevent editing aggregate items"|gettext value=1 checked=$config.noeditagg}
<hr />
<div id="aggregation-list">
<table class="exp-skin-table">
    <thead>
        <tr>
            {*<th>{""|gettext}</th>*}
            <th><input type='checkbox' name='checkall' title="{'Select All/None'|gettext}" style="margin-left: 1px;" onchange="selectAll(this.checked)"></th>
            {$tabno = $smarty.foreach.body.iteration-1}
            {$tabanchor = '#tab='|cat:$tabno|cat:'" alt="'}
            {$page->header_columns|replace:'" alt="':$tabanchor}
            {*<th>{"Title"|gettext}</th>*}
            {*<th>{"Page"|gettext}</th>*}
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
</div>

{script unique="aggregation"}
    function selectAll(val) {
        var checks = document.getElementsByName("aggregate[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
    YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
        var aggnodes = Y.one('#aggregation-list');
        EXPONENT.handleClick = function(e) {
            if (e.currentTarget.get('checked')) {
                aggnodes.setStyle('display','block');
            } else {
                aggnodes.setStyle('display','none');
            }
        };
        Y.one('#add_sourceControl').delegate('click', EXPONENT.handleClick, "#add_source");
        if (!Y.one('#add_source').get('checked')) {
            aggnodes.setStyle('display','none');
        }
    });
{/script}
