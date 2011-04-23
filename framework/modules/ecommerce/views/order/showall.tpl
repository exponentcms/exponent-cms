{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

<div class="modules order showall">
	<h1>{$moduletitle|default:"Store Order Administration"}</h1>
	{if $closed_count > -1}
    	{br}{$closed_count} orders have been closed. <a href="{link action=showall showclosed=1}">View Now</a>{br}
    {else}
        {br}<a href="{link action=showall showclosed=0}">Hide closed orders</a>{br}
    {/if}
    {*eDebug var=$page->records[0]*}
	<div id="orders">
		{pagelinks paginate=$page top=1}
		<table id="prods" class="exp-skin-table">
			<thead>
				<tr>
					<!--th><span>Purchased By</span></th-->
					{$page->header_columns}
				</tr>
			</thead>
			<tbody>
				{foreach from=$page->records item=listing name=listings}
				<tr class="{cycle values="odd,even"}">
					<td>
						<a href="{link action=show id=$listing->id}">
							{$listing->lastname}, {$listing->firstname} 
						</a>
					</td> 
					<td><a href="{link action=show id=$listing->id}">{$listing->invoice_id}</a></td>
					<td>${$listing->grand_total|number_format:2}</td>
					<td>{$listing->purchased|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}</td>
					<td>{$listing->status}</td>
				</tr>
				{foreachelse}
				    <tr class="{cycle values="odd,even"}">
				        <td colspan="4">No orders have been placed yet</td>
				    </tr>
				{/foreach}
		</tbody>
		</table>
		{pagelinks paginate=$page bottom=1}
	</div>
</div>
