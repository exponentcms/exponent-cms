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

{css unique="option-styles" link="`$asset_path`css/options-edit.css" corecss="tables"}

{/css}

<div class="product options-partial">
	{foreach from=$optiongroups item=group}
        <div class="panel">
            <div class="hd">
                <h2 title="{'Click to expand'|gettext}">{'Product Options'|gettext} - <strong>{$group->title}</strong></h2><a href="#" class="yexpand">{'Expand'|gettext}</a>
            </div>
            <div class="bd collapsed">
                <!-- cke lazy -->
                <table class="options exp-skin-table" summary="{$group->title} {'Product Options'|gettext}">
                    <thead>
                        <tr>
                            <th>
                                {*<h2>{$group->title}</h2>*}
                                {control type="hidden" name="optiongroups[`$group->title`][id]" value=$group->id}
                                {control type="hidden" name="optiongroups[`$group->title`][title]" value=$group->title}
                                {control type="hidden" name="optiongroups[`$group->title`][optiongroup_master_id]" value=$group->optiongroup_master_id}
                                {control type="checkbox" nowrap=true name="optiongroups[`$group->title`][required]" label="Required"|gettext value=1 checked=$group->required}
                            </th>
                            {*<th colspan="4">*}
                                {*{control type="hidden" name="optiongroups[`$group->title`][rank]" value=$group->rank}*}
                                {*{control type="text" name="optiongroups[`$group->title`][rank]" label="Rank"|gettext size="3" value=$group->rank}*}
                            {*</th>*}
                        {*</tr>*}
                        {*<tr>*}
                            <th colspan="2">
                                {control type="radio" nowrap=true name="optiongroups[`$group->title`][allow_multiple]" label="Select Single"|gettext value=0 checked=$group->allow_multiple description='Displayed as a dropdown'|gettext}
                            </th>
                            <th colspan="3">
                                {control type="radio" nowrap=true name="optiongroups[`$group->title`][allow_multiple]" label="Select Multiple"|gettext value=1 checked=$group->allow_multiple description='Displayed as checkboxes'|gettext}
                            </th>
                        </tr>
                        <tr class="column-label">
                            <th>{'Option Available'|gettext}</th>
                            <th>{'User Input'|gettext}</th>
                            <th>{'Adjust'|gettext}</th>
                            <th>{'Modifier'|gettext}</th>
                            <th>{'Amount'|gettext}</th>
                            <th id="option-{$group->id}" title="{'Click to clear default'|gettext}">{'Default'|gettext}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach key=key from=$group->options item=option}
                            <tr class="{cycle values='odd,even' advance=false}">
                                <td>
                                    {control type="hidden" name="optiongroups[`$group->title`][options][`$option->title`][id]" value=$option->id}
                                    {control type="hidden" name="optiongroups[`$group->title`][options][`$option->title`][title]" value=$option->title}
                                    {control type="hidden" name="optiongroups[`$group->title`][options][`$option->title`][option_master_id]" value=$option->option_master_id}
                                    {control type="checkbox" name="optiongroups[`$group->title`][options][`$option->title`][enable]" label=$option->title value=1 checked=$option->enable}
                                    <a rel="mo-{$key}-{$group->title|strip:'_'}" class="togglelink" href="#">+{'More'|gettext}...</a>
                                </td>
                                <td>{control type="checkbox" name="optiongroups[`$group->title`][options][`$option->title`][show_input]" label='Needs Input'|gettext value=1 checked=$option->show_input}</td>
                                <td>{control type="dropdown" name="optiongroups[`$group->title`][options][`$option->title`][updown]" items="+,-" values="+,-" value=$option->updown}</td>
                                <td>{control type="dropdown" name="optiongroups[`$group->title`][options][`$option->title`][modtype]" items="$,%" values="$,%" value=$option->modtype}</td>
                                <td>{control type="text" name="optiongroups[`$group->title`][options][`$option->title`][amount]" size=6 value=$option->amount}</td>
                                <td width="15%">{control type="radio" name="defaults[`$group->title`]" label="Default" value=$option->title checked=$option->is_default}</td>
                            </tr>
                            <tr class="{cycle values='odd,even'}" id="mo-{$key}-{$group->title|strip:'_'}" style="display:none">
                                <td colspan=5>
                                    {control type="text" name="optiongroups[`$group->title`][options][`$option->title`][optionweight]" label="Option Weight"|gettext size=6 value=$option->optionweight}
                                </td>
                            </tr>
                        {foreachelse}
                            <p>
                                {message class=notice text="This option group does not have any options yet."|gettext}
                            </p>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
        {script unique="default-clear-"|cat:$group->id jquery=1}
        {literal}
            $('#option-{/literal}{$group->id}{literal}').on('click', function(){
                $('input[name="defaults[{/literal}{$group->title}{literal}]"').removeAttr('checked');
            });
        {/literal}
        {/script}

    {foreachelse}
        {message class=notice text="There are no product options setup yet."|gettext}
    {/foreach}
</div>

{script unique="expand-panels" yui3mods="node"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        var action = function(e){
            e.halt();
            var pBody = e.target.ancestor('.panel').one('.bd');
            var pWidgetE = e.target.ancestor('.panel').one('a.yexpand');
            var pWidgetC = e.target.ancestor('.panel').one('a.ycollapse');

            if (e.target.getAttribute("class")=="ycollapse") {
                pBody.replaceClass('expanded','collapsed');
                e.target.replaceClass('ycollapse','yexpand');
                if (pWidgetC != null) pWidgetC.replaceClass('ycollapse','yexpand');
            } else {
                pBody.replaceClass('collapsed','expanded');
                e.target.replaceClass('yexpand','ycollapse');
                if (pWidgetE != null) pWidgetE.replaceClass('yexpand','ycollapse');
            }
        }
        Y.one('.options-partial').delegate('click', action, 'div.hd');

        var showit = function(e){
            e.halt();
            var targrel = e.target.get("rel");
            if (Y.one('#'+targrel).getStyle('display')=="none") {
                if (Y.UA.ie > 0) {
    //                Y.one('#'+targrel).setStyle('display', 'block');
                } else {
                    Y.one('#'+targrel).setStyle('display', 'table-row');
                }
            } else {
                Y.one('#'+targrel).setStyle('display',"none");
            }
        }
        Y.one('.options-partial').delegate('click', showit, 'a.togglelink');
    });
{/literal}
{/script}
