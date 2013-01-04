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

<div id="navmanager" class="module navigation manager">
	<div class="form_header">
		<div class="info-header">
			<div class="related-actions">
				{help text="Get Help"|gettext|cat:" "|cat:("Managing Pages"|gettext) module="manage-all-pages"}
			</div>
			<h1>{'Manage Pages'|gettext}</h1>
		</div>
	</div>
	<div id="navmanager-tabs" class="yui-navset exp-skin-tabview hide">
	    <ul class="yui-nav">
        	<li class="selected"><a href="#tab1"><em>{'Menu Hierarchy'|gettext}</em></a></li>
	        {if $canManageStandalones}<li><a href="#tab2"><em>{'Standalone Pages'|gettext}</em></a></li>{/if}
	    </ul>
	    <div class="yui-content">
        	<div id="tab1">{include file="`$smarty.const.BASE`framework/modules/navigation/views/navigation/manage_hierarchy.tpl"}</div>
	        {if $canManageStandalones}<div id="tab2">{include file="`$smarty.const.BASE`framework/modules/navigation/views/navigation/manage_standalone.tpl"}</div>{/if}
	    </div>
	</div>
	<div class="loadingdiv">{'Loading Pages'|gettext}</div>
</div>

{script unique="editform" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#navmanager-tabs'});
	    Y.one('#navmanager-tabs').removeClass('hide');
	    Y.one('.loadingdiv').remove();
	});
{/literal}
{/script}
