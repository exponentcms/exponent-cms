{*
 * Copyright (c) 2004-2019 OIC Group, Inc.
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

{css unique="managemods" corecss="admin-global,tables"}
{literal}
.exp-skin-table td label {
    display:block;
    margin-left:20px;
    position:relative;
}
.exp-skin-table th {
    padding:0px;
}
.exp-skin-table td label input {
    margin-left:-20px;
}
.exp-skin-table tr.active  {
    background-color:#d9e4d5;
}
.exp-skin-table td label strong {
    margin-left:3px;
}
.exp-skin-table td label span {
    position:absolute;
    right:0px;
    top:0px;
    text-transform:uppercase;
    font-size:10px;
}
.exp-skin-table td label span.alpha {
    color:#000;
}

.exp-skin-table td label span.beta {
    color:#339;
}

.exp-skin-table td label span.stable {
    display:none;
}

{/literal}
{/css}

<div id="mod-manager" class="module administration modulemanager">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Modules"|gettext) module="manage-modules"}
        </div>
        <h2>{"Module Manager"|gettext}</h2>
    </div>
    {form action="update"}
    <div id="mod-manager-tabs" class="yui-navset exp-skin-tabview">
        {*<ul class="yui-nav">*}
            {*<li class="selected"><a href="#tab1"><em>Exponent 2</em></a></li>*}
            {*<li><a href="#tab2"><em>{"Old School"|gettext}</em></a></li>*}
        {*</ul>            *}
        <div class="yui-content">
            <div id="tab1">
                <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
                    <thead>
                        <tr>
                            <th>
                                <a class="selectall" href="#" id="sa_conts" onclick="selectAll(1); return false;">{"Select All"|gettext}</a> / <a class="selectnone" href="#" id="sn_conts" onclick="selectAll(0); return false;">{"Select None"|gettext}</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$controllers item=module}
                        <tr class="{cycle values="odd,even"}{if $module->active == 1} active{/if}">
                            <td class="activate">
                            <label>
                                <input type="checkbox" class="selectbox" name="mods[{$module->class}]"{if $module->active == 1} checked{/if} value=1>
                                <strong>
                                    {$module->name}
                                </strong>
                                <span class="{$module->codequality}">
                                    {$module->codequality}
                                </span>
                                {if !empty($module->workflow)}
                                    <span class="workflow" title="{'Workflow enabled module'|gettext}"></span>
                                {/if}
                                {br}
                                <em>
                                    {$module->description}
                                </em>
                            </label>
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {*<div id="tab2">*}
                {*<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">*}
                    {*<thead>*}
                        {*<tr>*}
                            {*<th>*}
                                {*<a class="selectall" href="#" id="sa_conts1" onclick="EXPONENT.selectAllCheckboxes('#tab2 input[type=checkbox]'); return false;">{"Select All"|gettext}</a> / <a class="selectnone" href="#" id="sn_conts1" onclick="EXPONENT.unSelectAllCheckboxes('#tab2 input[type=checkbox]'); return false;">{"Select None"|gettext}</a>*}
                            {*</th>*}
                        {*</tr>*}
                    {*</thead>*}
                    {*<tbody>*}
                        {*{foreach from=$old_school_mods item=module}*}
                        {*<tr class="{cycle values="odd,even"}">*}
                            {*<td class="activate">*}
                            {*<label>*}
                            {*<input type="checkbox" name="mods[{$module->class}]"{if $module->active == 1} checked {/if}value=1>*}
                            {*<strong>{$module->name}</strong>{br}*}
                            {*<em>*}
                            {*{$module->description}*}
                            {*</em>*}
                            {*</label>*}
                            {*</td>*}
                        {*</tr>*}
                        {*{/foreach}*}
                    {*</tbody>*}
                {*</table>*}
            {*</div>*}
        </div>
    </div>
    {*<div class="loadingdiv">{"Loading Modules"|gettext}</div>*}
    {control type="buttongroup" submit="Update Active Modules"|gettext}
    {/form}
</div>

{script unique="selectall"}
{literal}
//    EXPONENT.YUI3_CONFIG.modules.exptabs = {
//        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
//        requires: ['history','tabview','event-custom']
//    };

//	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
//        Y.expTabs({srcNode: '#mod-manager-tabs'});
//		Y.one('#mod-manager-tabs').removeClass('hide');
//		Y.one('.loadingdiv').remove();

//		EXPONENT.selectAllCheckboxes = function (selector) {
//			Y.all(selector).each(function(n){
//				n.set('checked',1);
//			});
//		};

//		EXPONENT.unSelectAllCheckboxes = function (selector) {
//			Y.all(selector).each(function(n){
//				n.set('checked',0);
//			});
//		};
//	});

    function selectAll(val) {
        var checks = document.getElementsByClassName('selectbox');
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/literal}
{/script}
