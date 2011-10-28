{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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

{css unique="managegroupdiscounts" corecss="tables"}

{/css}

<div class="module storeadmin groupdiscounts">
	<div class="form_header">
        	<h1>Group Discounts</h1>
	        <p>You can configure certain user groups to get a discount applied to their carts when they checkout.</p>
	</div>
	
	{br}
	<a class="add" href="{link controller=user action=edit_group id=0}">Add a new group</a>
	<a class="add" href="{link controller=ecomconfig action=manage_discounts}">Manage a Discount Rules</a>
	{br}{br}
	<h2>Add a new group discount</h2>
	<table class="exp-skin-table">
	<thead>
	    <tr>
	        <th>Group</th>
		    <th>Discount</th>
		    <th>Don't allow{br}other{br}group discounts</th>
		    <th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	    <tr>
	        {form action=update_groupdiscounts}
                <td>{control type=dropdown name=group_id items=$groups label=" " key=id display=name includeblank="-- Select a group --"}</td>
                <td>{control type="dropdown" name="discounts_id" items=$discounts key=id display=title label=" " includeblank="-- Select a Discount --"}</td>
                <td>{control type="checkbox" name="dont_allow_other_discounts" label=" " value=1}</td>
                <td>{control type=buttongroup submit="Add"}</td>
	        {/form}
	    </tr>
	</tbody>
	</table>
	
	{if $group_discounts|@count > 0}
	<h2>Modify existing group discount</h2>
	<table class="exp-skin-table">
	    <thead>
	    <tr>
	        <th>Group</th>
		    <th>Discount</th>
		    <th>Don't allow{br}other{br}group discounts</th>
		    <th>Order</th>
		    <th>&nbsp;</th>
	    </tr>
	    </thead>
	    {foreach from=$group_discounts item=discount name=items}
			<tr class="{cycle values='even,odd'}"">
			    {form action=update_groupdiscounts}
	                {control type="hidden" name="id" value=$discount->id}
                    <td>{control type=dropdown name=group_id items=$groups key=id display=name label=" " includeblank="-- Select a group --" value=$discount->group_id}</td>
                    <td>{control type="dropdown" name="discounts_id" items=$discounts key=id display=title label=" " value=$discount->discounts_id}</td>
                    <td>{control type="checkbox" name="dont_allow_other_discounts" label=" " value=1 checked=$discount->dont_allow_other_discounts}</td>
                    <td>
                        {if $permissions.manage == true}
                            {if $smarty.foreach.items.first == 0}
                                {icon controller=ecomconfig action=rerank_groupdiscount img='up.png' record=$discount push=up}
                            {/if}
                            {if $smarty.foreach.items.last == 0}
                                {icon controller=ecomconfig action=rerank_groupdiscount img='down.png' record=$discount push=down}
                            {/if}
                        {/if}
                    </td>
                    <td>{control type=buttongroup submit="Update"}</td>
	            {/form}
			</tr>
		{/foreach}
	</tbody>
	</table>
	{/if}
</div>
