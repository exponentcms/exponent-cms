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

<div id="config" class="module scaffold configure">
    <div class="form_header">
        <div class="info-header">
    		<div class="related-actions">
    		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("module configuration"|gettext) page="module-configuration"}
    		</div>
            <h1>{'Configure Settings for this'|gettext} {$title} {'Module'|gettext}</h1>
    	</div>
    	<p>{'Use this form to configure the behavior of the module.'|gettext}</p>
    </div>
	{form action=saveconfig}
		<div id="config-tabs" class="yui-navset exp-skin-tabview hide">
			<ul class="yui-nav">
			    {foreach from=$views item=tab name=tabs}
			        <li{if $smarty.foreach.tabs.first} class="selected"{/if}>
			            <a href="#tab{$smarty.foreach.tabs.iteration}"><em>{$tab.name}</em></a>
			        </li>
			    {/foreach}
			</ul>            
            <div class="yui-content">
                {foreach from=$views item=body name=body}
                    <div id="tab{$smarty.foreach.body.iteration}">
                        {include file=$body.file}
                    </div>
                {/foreach}
			</div>
		</div>
		<div class="loadingdiv">{"Loading Settings"|gettext}</div>
		{control type=buttongroup submit="Save Config"|gettext cancel="Cancel"|gettext}
	{/form}
</div>

{script unique="conf" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_PATH+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#config-tabs'});
        Y.one('#config-tabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
	});

{/literal}
{/script}