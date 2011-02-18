{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
 * Written and Designed by James Hunt
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
{css unique="managemods" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/admin-global.css"}
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

 

<div class="module administrationmodule modulemanager exp-skin-tabview hide">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help Managing Modules" module="addcontent"}
        </div>
        <h1>{"Module Manager"|gettext}</h1>
    </div>

    {form action="update"}
    <div id="mods" class="yui-navset">
        <ul class="yui-nav">
            <li class="selected"><a href="#tab1"><em>Exponent 2</em></a></li>
            <li><a href="#tab2"><em>Old School</em></a></li>
        </ul>            
        <div class="yui-content">
            <div id="tab1">
                <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
                    <thead>
                        <tr>
                            <th>
                                <a class="selectall" href="#" id="sa_conts" onclick="EXPONENT.selectAllCheckboxes('#tab1 input[type=checkbox]'); return false;">{"Select All"|gettext}</a> / <a class="selectnone" href="#" id="sa_conts" onclick="EXPONENT.unSelectAllCheckboxes('#tab1 input[type=checkbox]'); return false;">{"Select None"|gettext}</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$controllers item=module}
                        <tr class="{cycle values="odd,even"}{if $module->active == 1} active{/if}">
                            <td class="activate">
                            <label>
                                <input type="checkbox" name="mods[{$module->class}]"{if $module->active == 1} checked {/if}value=1>
                                <strong>
                                    {$module->name}
                                </strong>
                                <span class="{$module->codequality}">
                                    {$module->codequality}
                                </span>
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
            <div id="tab2">
                <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
                    <thead>
                        <tr>
                            <th>
                                <a class="selectall" href="#" id="sa_conts" onclick="EXPONENT.selectAllCheckboxes('#tab2 input[type=checkbox]'); return false;">{"Select All"|gettext}</a> / <a class="selectnone" href="#" id="sa_conts" onclick="EXPONENT.unSelectAllCheckboxes('#tab2 input[type=checkbox]'); return false;">{"Select None"|gettext}</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$old_school_mods item=module}
                        <tr class="{cycle values="odd,even"}">
                            <td class="activate">
                            <label>
                            <input type="checkbox" name="mods[{$module->class}]"{if $module->active == 1} checked {/if}value=1>
                            <strong>{$module->name}</strong>{br}
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
        </div>
    </div>
    {control type="buttongroup" submit="Update Active Modules"}
    {/form}
</div>
<div class="loadingdiv">{"Loading"|gettext}</div>

{script unique="filetabs" yui2mods="tabview,element" yui3mods="node"}
{literal}
YUI({base:EXPONENT.YUI3_PATH,loadOptional: true}).use('*', function(Y) {
    var tabView = new YAHOO.widget.TabView('mods');
    Y.one('.modulemanager.hide').removeClass('hide');
    Y.one('.loadingdiv').remove();
    
    EXPONENT.selectAllCheckboxes = function (selector) {
        Y.all(selector).each(function(n){
            n.set('checked',1);
        });
    };
    
    EXPONENT.unSelectAllCheckboxes = function (selector) {
        Y.all(selector).each(function(n){
            n.set('checked',0);
        });
    };
});    
{/literal}
{/script}
