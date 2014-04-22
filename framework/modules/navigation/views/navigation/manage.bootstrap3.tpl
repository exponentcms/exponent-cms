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

<div id="navmanager" class="module navigation manager">
	<div class="form_header">
		<div class="info-header">
			<div class="related-actions">
				{help text="Get Help with"|gettext|cat:" "|cat:("Managing Pages"|gettext) module="manage-all-pages"}
			</div>
			<h2>{'Manage Pages'|gettext}</h2>
		</div>
	</div>
	<div id="navmanager-tabs" class="">
	    <ul class="nav nav-tabs">
        	<li class="active"><a href="#tab1" data-toggle="tab"><em>{'Menu Hierarchy'|gettext}</em></a></li>
	        {if $canManageStandalones}<li><a href="#tab2" data-toggle="tab"><em>{'Standalone Pages'|gettext}</em></a></li>{/if}
	    </ul>
	    <div class="tab-content">
        	<div id="tab1" class="tab-pane fade in active">{exp_include file="`$smarty.const.BASE`framework/modules/navigation/views/navigation/manage_hierarchy.bootstrap3.tpl"}</div>
	        {if $canManageStandalones}<div id="tab2" class="tab-pane fade">{exp_include file="`$smarty.const.BASE`framework/modules/navigation/views/navigation/manage_standalone.bootstrap3.tpl"}</div>{/if}
	    </div>
	</div>
	<div class="loadingdiv">{'Loading Pages'|gettext}</div>
</div>

{*{script unique="editform" yui3mods=1}*}
{*{literal}*}
    {*EXPONENT.YUI3_CONFIG.modules.exptabs = {*}
        {*fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',*}
        {*requires: ['history','tabview','event-custom']*}
    {*};*}

	{*YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {*}
        {*Y.expTabs({srcNode: '#navmanager-tabs'});*}
	    {*Y.one('#navmanager-tabs').removeClass('hide');*}
	    {*Y.one('.loadingdiv').remove();*}
	{*});*}
{*{/literal}*}
{*{/script}*}

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}