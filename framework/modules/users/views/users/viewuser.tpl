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

{css unique="view_user" corecss="tables"}

{/css}
<div id='viewuser' class="module users view">
	<div id="general_account_info">
		<h1>{'General Account Information'|gettext}</h1>
		<table class="exp-skin-table">
			<tr>
				<th>{'Username'|gettext}:</th>
				<td>{$u->username}</td>
			</tr>
			<tr>
				<th>{'Name'|gettext}:</th>
				<td>{$u->firstname} {$u->lastname}</td>
			</tr>
			<tr>
				<th>{'Email'|gettext}:</th>
				<td>{$u->email}</td>
			</tr>
			<tr>
				<th>{'Is Admin'|gettext}:</th>
				<td>
				{if $u->is_acting_admin == 1}	
					{'Yes'|gettext}
				{else}
					{'No'|gettext}
				{/if}
				</td>
			</tr>
			<tr>
				<th>{'Last Login'|gettext}:</th>
				<td>{$u->last_login|format_date}</td>
			</tr>
            <tr><td colspan=2>
                <a class="manage" href="{link module=users action=edituser id=$u->id}">{'Update Profile'|gettext}</a>
            </td></tr>
		</table>
	</div>
	
	<div id="addresses_info">
		<h1>{'Addresses Information'|gettext}</h1>
		<table class="exp-skin-table">
			<thead>
				<tr>
					<th><h3>{'Billing Address'|gettext}</h3></th>
					<th><h3>{'Shipping Address'|gettext}</h3></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						{if $billings[0]->id == ''}
							{'You have not selected an address yet'|gettext}.
                        {else}
							{foreach from=$billings item=billing}
								{$billing|address}
								{br}
							{/foreach}
						{/if}
					</td>
					<td>
						{if $shippings[0]->id == ''}
							{'No address yet'|gettext}
						{else}
							{foreach from=$shippings item=shipping}
								{$shipping|address}
								{br}
							{/foreach}	
						{/if}
					</td>
				</tr>
                {*{if $billings[0]->id == '' || $shippings[0]->id == ''}*}
                    <tr><td colspan=2>
                        <a class="manage" href="{link module=address action=myaddressbook user_id=$u->id}">{'Manage My Addresses'|gettext}</a>
                    </td></tr>
                {*{/if}*}
			</tbody>
		</table>
	</div>
	
	<div id="orders">
		<h1>{'Order Information'|gettext}</h1>
		{pagelinks paginate=$orders top=1}
		<table id="prods" class="exp-skin-table">
			<thead>
				<tr>
					<!--th><span>Purchased By</span></th-->
					{$orders->header_columns}
				</tr>
			</thead>
			<tbody>
				{foreach from=$orders->records item=listing name=listings}
                    <tr class="{cycle values='odd,even'}">
                        <td><a href="{link action=show id=$listing->id}">{$listing->invoice_id}</a></td>
                        <td style="text-align:right;">{$listing->grand_total|currency}</td>
                        <td>{$listing->purchased|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}</td>
                        <td>{$listing->order_type}</td>
                        <td>{$listing->status}</td>
                        <td>{if $listing->orig_referrer !=''}<a href="{$listing->orig_referrer}" target="_blank" title="{$listing->orig_referrer}">{icon img="clean.png"}</a>{/if}</td>
                    </tr>
				{foreachelse}
				    <tr class="{cycle values="odd,even"}">
				        <td colspan="4">{'No orders have been placed yet'|gettext}</td>
				    </tr>
				{/foreach}
		    </tbody>
		</table>
		{pagelinks paginate=$orders bottom=1}
	</div>
</div>