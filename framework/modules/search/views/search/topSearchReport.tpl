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

{css unique="topsearchreport" corecss="tables"}

{/css}

<div class="module topsearchquery report">
    <div class="info-header">
   
        <h1>{$moduletitle|default:"Top `$limit` Search Queries Report"|gettext}</h1>
    </div>

	<table class="exp-skin-table">
	    <thead>
			<tr>
				<th>{"Rank"|gettext}</th>
				<th>{"Term"|gettext}</th>
				<th>{"% of All Searches"|gettext}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$records item=query name=listings}
			<tr class="{cycle values='odd,even'}">
				<td>{counter}</td>
				<td>{$query->query}</td>
				<td>{(($query->cnt / $total)*100)|number_format:2} %</td>
			</tr>
			{foreachelse}
			    <td colspan="3">{"No Search Query Data"|gettext}</td>
			{/foreach}
		</tbody>
	</table>

</div>