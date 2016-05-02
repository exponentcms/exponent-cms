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

<div id="navmanager" class="module navigation manager">
	<div class="form_header">
		<div class="info-header">
			<div class="related-actions">
				{help text="Get Help with"|gettext|cat:" "|cat:("Managing Pages"|gettext) module="manage-all-pages"}
			</div>
			<h2>{'Manage Pages'|gettext}</h2>
		</div>
	</div>
	{permissions}
		{if $user->isAdmin()}
			<div class="module-actions">
				{icon class=manage action=manage_sitemap text='Manage by Sitemap'|gettext}
			</div>
		{/if}
	{/permissions}
	<div id="navmanager-tabs" class="yui-navset exp-skin-tabview hide">
	    <ul class="yui-nav">
        	<li class="selected"><a href="#tab1"><em>{'Menu Hierarchy'|gettext}</em></a></li>
	        {if $canManageStandalones}<li><a href="#tab2"><em>{'Standalone Pages'|gettext}</em></a></li>{/if}
	    </ul>
	    <div class="yui-content">
        	<div id="tab1">{exp_include file="manage_hierarchy.tpl"}</div>
	        {if $canManageStandalones}<div id="tab2">{exp_include file="manage_standalone.tpl"}</div>{/if}
	    </div>
	</div>
	{*<div class="loadingdiv">{'Loading Pages'|gettext}</div>*}
	{loading title='Loading Pages'|gettext}
</div>

{script unique="editform" yui3mods="exptabs"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.expTabs({srcNode: '#navmanager-tabs'});
	    Y.one('#navmanager-tabs').removeClass('hide');
	    Y.one('.loadingdiv').remove();
	});
{/literal}
{/script}
