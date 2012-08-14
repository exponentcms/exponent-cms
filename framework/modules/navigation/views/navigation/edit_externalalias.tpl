{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div class="navigation edit_externalalias">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Editing External Alias Pages"|gettext) module="edit-external-page"}
        </div>
	    <h1>{if $section->id}{'Edit Existing External Alias'|gettext}{else}{'New External Alias'|gettext}{/if}</h1>
	</div>
	<div class="form_header">
        {'Below, enter the web address you want this section to link to.'|gettext}
	</div>
    {form action=update}
        {control type=hidden name=id value=$section->id}
        {control type=hidden name=rank value=$section->rank}
        {control type=hidden name=alias_type value=1}
        {control type=hidden name=active value=1}
        <div id="configure-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'Page'|gettext}</em></a></li>
            </ul>
            <div class="yui-content">
                <div id="tab1">
                    {control type=text name=name label="Name"|gettext value=$section->name}
                    {control type=text name=sef_name label="SEF Name"|gettext value=$section->sef_name}
                    <div class="control"><div class="control-desc">{'If you don\'t put in an SEF Name one will be generated based on the title provided. SEF names can only contain alpha-numeric characters, hyphens and underscores.'|gettext}</div></div>
                    {if $section->id == 0}
                        {control type=hidden name=parent value=$section->parent}
                    {else}
                        {control type="dropdown" name="parent" label="Parent Page"|gettext items=navigationController::levelDropdownControlArray(0,0,array($section->id),true,'manage') value=$section->parent}
                    {/if}
                    {control type="checkbox" name="new_window" label="Open in New Window"|gettext|cat:"?" checked=$section->new_window value=1}
                    {control type=text name=external_link label="Page URL"|gettext value=$section->external_link}
                    {control type="checkbox" name="public" label="Public"|gettext|cat:"?" checked=$section->public|default:1 value=1}
                </div>
            </div>
        </div>
        <div class="loadingdiv">{'Loading Pages'|gettext}</div>
        {control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
    {/form}
{script unique="configure" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
        var tabview = new Y.TabView({srcNode:'#configure-tabs'});
        tabview.render();
        Y.one('#configure-tabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
</div>