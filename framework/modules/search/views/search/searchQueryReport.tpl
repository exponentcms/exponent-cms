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

{css unique="searchqueryreport" corecss="tables"}

{/css}

<div class="module searchquery report">
    <div class="info-header">
        <h1>{$moduletitle|default:"Search Queries Report"|gettext}</h1>
    </div>
    {permissions}
    <div class="module-actions">
        {if $permissions.manage == 1}
            {icon class=delete action=delete_search_queries text="Delete Past Queries"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete all past search queries?"|gettext)|cat:"');"}
        {/if}
    </div>
    {/permissions}
	<div id="searchqueryreport" class="yui-navset exp-skin-tabview hide">
		<ul class="yui-nav">
			<li class="selected"><a href="#tab1"><em>{"All Search Queries"|gettext}</em></a></li>
			<li><a href="#tab2"><em>{"Bad Search Queries"|gettext}</em></a></li>
		</ul>
		<div class="yui-content">
			<div id="tab1">
				{pagelinks paginate=$page top=1}
				{control type="dropdown" name="user_id" label="Filter by User"|gettext items="{$users.name}" values="{$users.id}" value=$user_default id="userdropdown"}
				<table class="exp-skin-table">
					<thead>
						<tr>
							{$page->header_columns}
						</tr>
					</thead>
					<tbody>
						{foreach from=$page->records item=query name=listings}
						<tr class="{cycle values='odd,even'}">
							<td>{$query->id}</td>
							<td>{$query->query}</td>
							<td>{$query->timestamp|format_date}</td>
							<td>
								{if !empty($query->user)}
									{$query->user}
								{else}
									{"Anonymous"|gettext}
								{/if}
							</td>
						</tr>
						{foreachelse}
							<td colspan="{$page->columns|count}">{"No Search Query Data"|gettext}</td>
						{/foreach}
					</tbody>
				</table>
				{pagelinks paginate=$page bottom=1}
			</div>
			<div id="tab2">
                <table class="exp-skin-table">
                    <thead>
                        <tr>
                            <th>Term</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$badSearch item=item name=listings}
                        <tr class="{cycle values='odd,even'}">
                            <td>{$item.query}</td>
                            <td>{$item.count}</td>
                        </tr>
                        {foreachelse}
                            <td colspan="2">{"No Bad Search Query Data"|gettext}</td>
                        {/foreach}
                    </tbody>
                </table>
            </div>
		</div>
	</div>
    <div class="loadingdiv">{"Loading"|gettext}</div>
</div>

{script unique="searchQueryReport" yui3mods="1"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

    YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#searchqueryreport'});
        Y.one('#searchqueryreport').removeClass('hide');
        Y.one('.loadingdiv').remove();

        var userdropdown = Y.one('#userdropdown');
        userdropdown.on("change",function(e){
            if(e.target.get('value') == -1) {
                window.location = EXPONENT.PATH_RELATIVE+"search/searchQueryReport/";
            } else {
                window.location = EXPONENT.PATH_RELATIVE+"search/searchQueryReport/user_id/"+e.target.get('value');
            }
        });
	});
{/literal}
{/script}