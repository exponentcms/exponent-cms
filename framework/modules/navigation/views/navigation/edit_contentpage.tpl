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

{css unique="newpage" corecss="forms"}

{/css}

<div class="module navigation edit_contentpage">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Editing Content Pages"|gettext) module="edit-content-page"}
        </div>
		<h1>{if $section->id}{'Edit Existing'|gettext}{else}{'Create New'|gettext} {if $section->parent == -1}{'Standalone'|gettext}{elseif $section->parent == 0}{'Top Level'|gettext}{/if} {'Content Page'|gettext}{/if}</h1>
	</div>
    <p>{if $section->id}{'Use the form below to change the details of this content page.'|gettext}{else}{'Use the form below to enter the information about your new content page.'|gettext}{/if}</p>
    {form action=update}
        {control type=hidden name=id value=$section->id}
        {control type=hidden name=rank value=$section->rank}
        {control type=hidden name=alias_type value=0}
        {control type=hidden name=_validate value=1}
        <div id="configure-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'Page'|gettext}</em></a></li>
                <li><a href="#tab2"><em>{'SEO'|gettext}</em></a></li>
            </ul>
            <div class="yui-content">
                <div id="tab1">
                    {control type=text name=name label="Name"|gettext value=$section->name}
                    {control type=text name=sef_name label="SEF Name"|gettext value=$section->sef_name}
                    <div class="control"><div class="control-desc">{'If you don\'t put in an SEF Name one will be generated based on the title provided. SEF names can only contain alpha-numeric characters, hyphens and underscores.'|gettext}</div></div>
                    {if $section->id == 0 || $section->parent == -1}
                        {control type=hidden name=parent value=$section->parent}
                    {else}
                        {control type=dropdown name=parent label="Parent Page"|gettext items=navigationController::levelDropdownControlArray(0,0,array($section->id),$user->isAdmin(),'manage') value=$section->parent}
                    {/if}
                    {control type="checkbox" name="new_window" label="Open in New Window"|gettext|cat:"?" checked=$section->new_window value=1}
                    {control type="checkbox" name="active" label="Active"|gettext|cat:"?" checked=$section->active|default:1 value=1}
                    {control type="checkbox" name="public" label="Public"|gettext|cat:"?" checked=$section->public|default:1 value=1}
                    {control type="dropdown" name="subtheme" label="Theme Variation"|gettext items=expTheme::getSubThemes() value=$section->subtheme}
                    {if $smarty.const.ENABLE_SSL}
                        {*TODO we don't secure individual pages at this time*}
                        {*{control type="checkbox" name="secured" label="Secured"|gettext|cat:"?" checked=$section->secured value=1}*}
                    {/if}
                    {control type="files" name="files" label="Icon"|gettext value=$section->expFile limit=1}
                </div>
                <div id="tab2">
                    <h2>{'SEO Information'|gettext}</h2>
                    {control type=text name=page_title label="Page Title"|gettext value=$section->page_title}
                    {control type=textarea name=keywords label="Keywords"|gettext value=$section->keywords}
                    {control type=textarea name=description label="Page Description"|gettext value=$section->description}
                </div>
            </div>
        </div>
        <div class="loadingdiv">{'Loading Pages'|gettext}</div>
        {control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
    {/form}
{script unique="configure" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

    YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#configure-tabs'});
        Y.one('#configure-tabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
</div>