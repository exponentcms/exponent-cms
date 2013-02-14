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

<div id="uploadextension" class="module administration install-extension">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Installing New Extensions"|gettext) module="install-extension"}
        </div>
        <h1>{'Install new Extension'|gettext}</h1>
    </div>
	<div id="extension-tabs" class="yui-navset exp-skin-tabview hide">
		<ul class="yui-nav">
		<li class="selected"><a href="#tab1"><em>{"Themes"|gettext}</em></a></li>
		<li><a href="#tab2"><em>{"Fixes"|gettext}</em></a></li>
		<li><a href="#tab3"><em>{"Mods"|gettext}</em></a></li>
		<li><a href="#tab4"><em>{"Upload Extension"|gettext}</em></a></li>
		</ul>
		<div class="yui-content">
			<div id="tab1">
				<h2>{"Themes"|gettext}</h2>
                {form action=install_extension_confirm}
                    {foreach from=$themes item=theme name=themes}
                        <div class="item" style="margin-top: 5px; padding-bottom: 5px; border-bottom: 1px; border-bottom-color: black; border-bottom-style: dashed;">
                            <div style="float: left;">{control type="checkbox" name="files['`$theme->title`']" label=" " value="`$theme->enclosure`"}</div>
                            <a href="{$theme->rss_link}" title="More Information"|gettext target="_blank"><h4>{$theme->title}</h4></a>
                            <em class="date">
                                {$theme->publish_date|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}
                            </em>
                            <div class="bodycopy">
                                {$theme->body|summarize:"html":"paralinks"}
                                <a href="{$theme->rss_link}" title="More Information"|gettext target="_blank">{'More Information'|gettext}</a>
                            </div>
                        </div>
                    {foreachelse}
                        <h4>{'There Are No Themes Available'|gettext}</h4>
                    {/foreach}
                    {if_elements array=$themes}{control type="buttongroup" submit="Install Selected Themes"|gettext}{/if_elements}
                {/form}
			</div>
			<div id="tab2">
				<h2>{"Patches and Fixes"|gettext}</h2>
                {form action=install_extension_confirm}
                    {control type=hidden name=patch value=1}
                    {foreach from=$fixes item=fix name=fixes}
                        <div class="item" style="margin-top: 5px; padding-bottom: 5px; border-bottom: 1px; border-bottom-color: black; border-bottom-style: dashed;">
                            <div style="float: left;">{control type="checkbox" name="files['`$fix->title`']" label=" " value="`$fix->enclosure`"}</div>
                            <a href="{$fix->rss_link}" title="More Information"|gettext target="_blank"><h4>{$fix->title}</h4></a>
                            <em class="date">
                                {$fix->publish_date|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}
                            </em>
                            <div class="bodycopy">
                                {$fix->body|summarize:"html":"paralinks"}
                                <a href="{$fix->rss_link}" title="More Information"|gettext target="_blank">{'More Information'|gettext}</a>
                            </div>
                        </div>
                    {foreachelse}
                        <h4>{'There Are No Fixes or Patches Available'|gettext}</h4>
                    {/foreach}
                    {if_elements array=$fixes}{control type="buttongroup" submit="Install Selected Patches"|gettext}{/if_elements}
                {/form}
			</div>
			<div id="tab3">
				<h2>{"Modifications"|gettext}</h2>
                {form action=install_extension_confirm}
                    {foreach from=$mods item=mod name=mods}
                        <div class="item" style="margin-top: 5px; padding-bottom: 5px; border-bottom: 1px; border-bottom-color: black; border-bottom-style: dashed;">
                            <div style="float: left;">{control type="checkbox" name="files['`$mod->title`']" label=" " value="`$mod->enclosure`"}</div>
                            <a href="{$mod->rss_link}" title="More Information"|gettext target="_blank"><h4>{$mod->title}</h4></a>
                            <em class="date">
                                {$mod->publish_date|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}
                            </em>
                            <div class="bodycopy">
                                {$mod->body|summarize:"html":"paralinks"}
                                <a href="{$mod->rss_link}" title="More Information"|gettext target="_blank">{'More Information'|gettext}</a>
                            </div>
                        </div>
                        {foreachelse}
                        <h4>{'There Are No Modifications Available'|gettext}</h4>
                    {/foreach}
                    {if_elements array=$mods}{control type="buttongroup" submit="Install Selected Modifications"|gettext}{/if_elements}
                {/form}
			</div>
			<div id="tab4">
                <h2>{"Extension File Upload"|gettext}</h2>
                <div class="form_header">{'This form allows you to upload modules, themes, plugins, and views to the website.  After you upload an archive containing an extension (or more) you will be shown a pre-installation summary page, outlining exactly what files will be installed where, and what each file contains (for security reasons)'|gettext}</div>
                <p><h4>{'It is NOT intended to be used to perform a version upgrade!'|gettext}</h4></p>
				{$form_html}
			</div>
		</div>
	</div>
</div>
<div class="loadingdiv">{'Loading'|gettext}</div>

{script unique="uploadextension" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#extension-tabs'});
       Y.one('#extension-tabs').removeClass('hide');
       Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}