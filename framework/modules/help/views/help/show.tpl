{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

<div id="showhelp" class="module help show">
    <h1>{$doc->title}</h1>
    {permissions}
    <div class="item-actions">
        {if $permissions.edit == 1}
            {icon action=edit record=$doc title="Edit this `$modelname`"}
        {/if}
    </div>
    {/permissions}
	<div id="showhelp-tabs" class="yui-navset yui3-skin-sam hide">
		<ul class="yui-nav">
			<li class="selected"><a href="#tab1"><em>General Overview</em></a></li>
			{if $doc->actions_views}
				<li><a href="#tab2"><em>Actions and Views</em></a></li>
			{/if}
			{if $doc->configuration}
				<li><a href="#tab3"><em>Configuration</em></a></li>
			{/if}
			{if $doc->youtube_vid_code}
				<li><a href="#tab4"><em>Videos</em></a></li>
			{/if}
			{if $doc->additional}
				<li><a href="#tab5"><em>Additional Info</em></a></li>
			{/if}
		</ul>
		<div class="yui-content bodycopy">
			<div id="tab1">
				{$doc->body|replace:"!!!version!!!":$hv}
			</div>
			{if $doc->actions_views}
				<div id="tab2">
					{$doc->actions_views|replace:"!!!version!!!":$hv}
				</div>
			{/if}
			{if $doc->configuration}
				<div id="tab3">
					{$doc->configuration|replace:"!!!version!!!":$hv}
				</div>
			{/if}
			{if $doc->youtube_vid_code}
				<div id="tab4">
					{$doc->youtube_vid_code}
				</div>
			{/if}
			{if $doc->additional}
				<div id="tab5">
					{$doc->additional|replace:"!!!version!!!":$hv}
				</div>
			{/if}
		</div>
	</div>
	<div class="loadingdiv">{"Loading Help"|gettext}</div>
</div>

{script unique="editform" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
	    var tabview = new Y.TabView({srcNode:'#showhelp-tabs'});
	    tabview.render();
		Y.one('#showhelp-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
