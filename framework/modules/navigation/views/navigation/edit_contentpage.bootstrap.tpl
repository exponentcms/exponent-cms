{*
 * Copyright (c) 2004-2015 OIC Group, Inc.
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

{css unique="newpage"}

{/css}

<div class="module navigation edit_contentpage">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with"|gettext|cat:" "|cat:("Editing Content Pages"|gettext) module="edit-content-page"}
        </div>
		<h2>{if $section->id}{'Edit Existing'|gettext}{else}{'Create New'|gettext} {if $section->parent == -1}{'Standalone'|gettext}{elseif $section->parent == 0}{'Top Level'|gettext}{/if} {'Content Page'|gettext}{/if}</h2>
        <blockquote>{if $section->id}{'Use the form below to change the details of this content page.'|gettext}{else}{'Use the form below to enter the information about your new content page.'|gettext}{/if}</blockquote>
	</div>
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
                    {control type=text name=name label="Name"|gettext value=$section->name focus=1}
                    {if $section->id == 0 || $section->parent == -1}
                        {control type=hidden name=parent value=$section->parent}
                    {else}
                        {control type=dropdown name=parent label="Parent Page"|gettext items=section::levelDropdownControlArray(0,0,array($section->id),($user->isAdmin() || $section->parent == 0),'manage') value=$section->parent}
                    {/if}
                    {control type="checkbox" name="new_window" label="Open in New Window"|gettext|cat:"?" checked=$section->new_window value=1 description='Should menu links for this page open in a new window/tab?'|gettext}
                    {control type="checkbox" name="active" label="Active"|gettext|cat:"?" checked=$section->active|default:1 value=1 description='Should this page menu link be active and actually link to this page?'|gettext}
                    {control type="checkbox" name="public" label="Public"|gettext|cat:"?" checked=$section->public|default:1 value=1 description='Should this page and menu item be visible to all users regardless of permissions?'|gettext}
                    {control type="dropdown" name="subtheme" label="Theme Variation"|gettext items=expTheme::getSubThemes() value=$section->subtheme description='Select an alternate page format'|gettext}
                    {if $smarty.const.ENABLE_SSL}
                        {*TODO we don't secure individual pages at this time*}
                        {*{control type="checkbox" name="secured" label="Secured"|gettext|cat:"?" checked=$section->secured value=1}*}
                    {/if}
                    {control type="files" name="files" label="Icon"|gettext accept="image/*" value=$section->expFile limit=1 description='Select an icon to use for this menu item'|gettext}
                </div>
                <div id="tab2">
                    <h2>{'SEO Information'|gettext}</h2>
                    {control type=text name=sef_name label="SEF URL"|gettext value=$section->sef_name description='If you don\'t put in an SEF URL one will be generated based on the title provided. SEF URLs can only contain alpha-numeric characters, hyphens, forward slashes, and underscores.'|gettext}
                    {control type=text name=canonical label="Canonical URL"|gettext value=$section->canonical|default description='Helps get rid of duplicate search engine entries'|gettext}
                    {control type=text name=page_title label="Page Title"|gettext value=$section->page_title description='Override the page/menu name for search engine entries'|gettext}
                    {control type=textarea name=keywords label="Keywords"|gettext value=$section->keywords description='Comma separated phrases - overrides site keywords'|gettext}
                    {control type=textarea name=description label="Page Description"|gettext value=$section->description description='Page description for search engine entries'|gettext}
                    {control type="checkbox" name="noindex" label="Do Not Index"|gettext|cat:"?" checked=$section->noindex value=1 description='Should this page be indexed by search engines?'|gettext}
                    {control type="checkbox" name="nofollow" label="Do Not Follow Links"|gettext|cat:"?" checked=$section->nofollow value=1 description='Should links on this page be indexed and followed by search engines?'|gettext}
                </div>
            </div>
        </div>
        <div class="loadingdiv">{'Loading Pages'|gettext}</div>
        {control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
    {/form}
{script unique="configure" yui3mods="exptabs"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.expTabs({srcNode: '#configure-tabs'});
        Y.one('#configure-tabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
</div>