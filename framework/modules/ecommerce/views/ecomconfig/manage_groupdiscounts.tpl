{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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
        <h2>{'Group Discounts'|gettext}</h2>
        <blockquote>{'You can configure certain user groups to get a discount applied to their carts when they checkout.'|gettext}</blockquote>
	</div>
    {icon class=add controller=user action=edit_group id=0 text='Add a new group'|gettext}
    {icon class=manage controller=ecomconfig action=manage_discounts text='Manage Discount Rules'|gettext}
	<h3>{'Add a new group discount'|gettext}</h3>
	<table class="exp-skin-table">
		<thead>
			<tr>
				<th>{'Group'|gettext}</th>
				<th>{'Discount'|gettext}</th>
				<th>{'Don\'t allow'|gettext}{br}{'other'|gettext}{br}{'group discounts'|gettext}</th>
				<th>{'Action'|gettext}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				{form action=update_groupdiscounts}
					<td>{control type=dropdown name=group_id items=$groups label=" " key=id display=name includeblank="-- Select a group --"|gettext}</td>
					<td>{control type="dropdown" name="discounts_id" items=$discounts key=id display=title label=" " includeblank="-- Select a Discount --"|gettext}</td>
					<td>{control type="checkbox" name="dont_allow_other_discounts" label=" " value=1}</td>
					<td>{control type=buttongroup submit="Add"|gettext}</td>
				{/form}
			</tr>
		</tbody>
	</table>

	{if $group_discounts|@count > 0}
	<h2>{'Modify existing group discount'|gettext}</h2>
	{permissions}
		<div class="module-actions">
			{if $permissions.manage}
				{ddrerank items=$group_discounts model="groupdiscounts" label="Group Discounts"|gettext}
			{/if}
		</div>
	{/permissions}
	<table class="exp-skin-table">
	    <thead>
			<tr>
				<th>{'Group'|gettext}</th>
				<th>{'Discount'|gettext}</th>
				<th>{'Don\'t allow other group discounts'|gettext}</th>
				<th>{'Order'|gettext}</th>
				<th>{'Action'|gettext}</th>
			</tr>
	    </thead>
		<tbody>
			{foreach from=$group_discounts item=discount name=items}
				<tr class="{cycle values='even,odd'}"">
					{form action=update_groupdiscounts}
						{control type="hidden" name="id" value=$discount->id}
						<td>{control type=dropdown name=group_id items=$groups key=id display=name label=" " includeblank="-- Select a group --"|gettext value=$discount->group_id}</td>
						<td>{control type="dropdown" name="discounts_id" items=$discounts key=id display=title label=" " value=$discount->discounts_id}</td>
						<td>{control type="checkbox" name="dont_allow_other_discounts" label=" " value=1 checked=$discount->dont_allow_other_discounts}</td>
						<td>
							{if $permissions.manage}
								<div class="item-actions">
									{if $smarty.foreach.items.first == 0}
										{icon controller=ecomconfig action=rerank_groupdiscount img='up.png' record=$discount push=up}
									{/if}
									{if $smarty.foreach.items.last == 0}
										{icon controller=ecomconfig action=rerank_groupdiscount img='down.png' record=$discount push=down}
									{/if}
								{/if}
							</div>
						</td>
						<td>{control type=buttongroup submit="Update"|gettext}</td>
					{/form}
				</tr>
			{/foreach}
		</tbody>
	</table>
	{/if}
</div>
