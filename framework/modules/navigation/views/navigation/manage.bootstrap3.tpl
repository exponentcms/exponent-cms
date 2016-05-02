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
	<div id="navmanager-tabs" class="">
	    <ul class="nav nav-tabs" role="tablist">
        	<li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'Menu Hierarchy'|gettext}</em></a></li>
	        {if $canManageStandalones}<li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Standalone Pages'|gettext}</em></a></li>{/if}
	    </ul>
	    <div class="tab-content">
        	<div id="tab1" role="tabpanel" class="tab-pane fade in active">{exp_include file="manage_hierarchy.tpl"}</div>
	        {if $canManageStandalones}<div id="tab2" role="tabpanel" class="tab-pane fade">{exp_include file="manage_standalone.tpl"}</div>{/if}
	    </div>
	</div>
	{*<div class="loadingdiv">{'Loading Pages'|gettext}</div>*}
	{loading title='Loading Pages'|gettext}
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}