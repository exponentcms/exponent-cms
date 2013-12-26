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

<div id="config" class="module scaffold configure">
    <div class="form_header">
        <div class="info-header">
    		<div class="related-actions">
    		    {help text="Get Help with"|gettext|cat:" "|cat:("module configuration"|gettext) page="module-configuration"}
    		</div>
            <h1>{'Configure Settings for this'|gettext} {$title} {'Module'|gettext}</h1>
    	</div>
    	<blockquote>{'Use this form to configure the behavior of the module.'|gettext}</blockquote>
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
		{control type=buttongroup submit="Save Configuration"|gettext cancel="Cancel"|gettext}
	{/form}
</div>

{script unique="conf" yui3mods=1}
{literal}
	/**
	 * add exp-tabs module and file to the YUI configuration object.
	 * Including the dependencies (requires) here saves 
	 * YUI and extra http call after loading exp-tabs, 
	 * which also contains the dependencies
	 */

    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

    /**
     * Now, we just have to specify exptabs as the module.
     * Looking in exp-tabs.js, you can see that on line 1, that's the module name.
     */
    
	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
		// Y.expTabs is the function defined in the exptabs script
		// we're passing it a static js object, with nothing but a
		// selector we want the tabs to work with

        Y.expTabs({srcNode: '#config-tabs'});

        // I didn't add this stuff to the tab script, as it's not essential to 
        // the tab functionality itself.
        Y.one('#config-tabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
	});

{/literal}
{/script}