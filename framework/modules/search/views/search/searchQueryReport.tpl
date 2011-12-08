{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
 * Written and Designed by OIC Group
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

	
    {pagelinks paginate=$page top=1}
	{control type="dropdown" name="user_id" label="Filter by User"|gettext items="{$user_name}" values="{$user_id}" value=$user_default class="userdropdown"}
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
				<td>{$query->timestamp|format_date:$smarty.const.DISPLAY_DATE_FORMAT}</td>
				<td>
					{if !empty($query->user)}
						{$query->user}
					{else}
						Anonymous
					{/if}
				</td>
			</tr>
			{foreachelse}
			    <td colspan="{$page->columns|count}">No Search Query Data</td>
			{/foreach}
		</tbody>
	</table>
    {pagelinks paginate=$page bottom=1}

</div>

{script unique="searchQueryReport"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node',function(Y) {
    
  
        var userdropdown = Y.one('.userdropdown');
    
        userdropdown.on("change",function(e){
			if(e.target.get('value') == -1) {
				window.location = EXPONENT.URL_FULL+"search/searchQueryReport/";
			} else {
				window.location = EXPONENT.URL_FULL+"search/searchQueryReport/user_id/"+e.target.get('value');
			}
        });
            
 
});
{/literal}
{/script}
