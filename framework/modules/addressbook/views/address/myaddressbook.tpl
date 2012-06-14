{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="myaddressbook" corecss="tables,button"}

{/css}

<div class="module address myaddressbook">
    <h1>{$moduletitle|default:"My address book"|gettext}</h1>
    <div>{'Click the'|gettext} <strong>{'Add New Address'|gettext}</strong> {'link below if you\'d like to add a new address to use for either your billing or shipping address'|gettext}.{br}
    {'To change your billing or shipping address for this order, simply select the button next to the address you\'d like to set in either the billing or shipping address column'|gettext}.{br}
    {'A green button indicates your selection'|gettext}.{br}{br}
    {'When you are done, simply click the <strong>Return to Checkout'|gettext}</strong> {'button below to go back to the checkout process'|gettext}.
    {br}{br}
    </div>
    <p>
        {icon class=add action=edit text="Add New Address"|gettext}
    </p>
    {br}    
    <table class="exp-skin-table">
    <thead>
    <th>{'Use as Billing'|gettext}</th>
    <th>{'Use as Shipping'|gettext}</th>
    <th>{'Address'|gettext}</th>
    <th>&nbsp;</th>
    </thead>
    <tbody>
    
    {foreach from=$addresses item=address}
        <tr class="{cycle values="odd,even"}">
            <td align="center">
                {if $address->is_billing}
                    <span style="text-align: center;"><img src="{$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}" /></span>
                {else}
                    <span style="text-align: center;"><a href="{link action=activate_address is_what="is_billing" id=$address->id enabled=1}"><img src="{$smarty.const.ICON_RELATIVE|cat:'toggle_off.png'}" /></a></span>
                {/if}   
            </td>
            <td align="center">
                {if $address->is_shipping}
                    <span style="text-align: center;"><img src="{$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}" /></span>
                {else}
                    <a href="{link action=activate_address is_what="is_shipping" id=$address->id enabled=1}"><img src="{$smarty.const.ICON_RELATIVE|cat:'toggle_off.png'}" /></a>
                {/if}   
            </td>
            <td>
                <address class="address show">
                    <strong>{$address->firstname} {$address->middlename} {$address->lastname}</strong>{br}
                    {$address->address1}{br}
                    {if $address->address2 != ""}{$address->address2}{br}{/if}
                    {$address->city}, {if $address->state == -2}{$address->non_us_state}{else}{$address->state|statename}{/if} {$address->zip}{br}
                    {if $address->state == -2}{$address->country|countryname}{br}{/if}
                    {$address->address_type}{br}
    				{$address->phone}{br}
    				{$address->email}{br}
				</address>
			</td>
			<td>
				{permissions}
					<div class="item-actions">
						{if $user->id == $address->user_id}
							{icon action=edit record=$address}
							{if $addresses|@count > 1}{icon action=delete record=$address}{/if}
						{/if}
					</div>
				{/permissions}
			</td>
				</tr>    
			{foreachelse}
				<tr><td colspan="4"></tr><p>{'You don\'t have any addresses in your address book yet'|gettext}</p></td>
			{/foreach}
		</tbody>
    </table>
    <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{backlink}">{"Done"|gettext}</a>
</div>

