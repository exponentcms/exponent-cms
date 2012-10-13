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
{css unique="donation" link="`$asset_path`css/eventregistration.css"}
{/css}
<div class="module eventregistration showall">
    {if $moduletitle != ''}<h1>{$moduletitle}</h1>{/if}
	{form name="eventregistration`$key`" action="eventregistration_process"}
	{control type="hidden" name="eventregistration[product_id]" value="{$product_id}"}
	{control type="hidden" name="eventregistration[base_price]" value="{$base_price}"}
	<input type="hidden" name="eventregistration[product_type]" value="eventregistration" size="20" class="hidden "/>

	<h3>{'Billing Address'|gettext}</h3>
	<table>
		<tr>
			<td>* {'First Name'|gettext}</td>
			<td>
				{control type="text" name="address[firstname]" value="`$record.address.firstname`" required=1}
			</td>
		</tr>
		
		<tr>
			<td>&#160;&#160;{'Middle Name'|gettext}</td>
			<td>
				{control type="text" name="address[middlename]" value="`$record.address.middlename`"}
			</td>
		</tr>
		
		<tr>
			<td>* {'Last Name'|gettext}</td>
			<td>
				{control type="text" name="address[lastname]" value="`$record.address.lastname`" required=1}
			</td>
		</tr>
		<tr>
			<td>&#160;&#160;{'Company/Organization'|gettext}</td>
			<td>
				{control type="text" name="address[organization]" value="`$record.address.organization`"}
			</td>
		</tr>
		<tr>
			<td>* {'Address'|gettext}</td>
			<td>
				{control type="text" name="address[address1]"  value="`$record.address.address1`" required=1}
			</td>
		</tr>
		<tr>
			<td>&#160;&#160;{'Address'|gettext} 2</td>
			<td>
				{control type="text" name="address[address2]" value="`$record.address.address2`"}
			</td>
		</tr>
	
		<tr>
			<td>* {'City'|gettext}</td>
			<td>
				{control type="text" name="address[city]" value="`$record.address.city`" required=1}
			</td>
		</tr>
		
		<tr>
			<td>* {'State'|gettext}</td>
			<td class="state_field"> 
				{control type="state" name="address[state]" includeblank="-- Choose a State -- " value="`$record.address.state`" label="" required=1}            
			</td>
		</tr>
		<tr>
			<td>* {'Country/State'|gettext}:</td>
			<td class="state_field">       
				{control type="country" name="address[country]" value="`$record.address.country`" value="`$record->country`"}
			</td>
		</tr>
		
		<tr>
			<td>* {'Zip Code'|gettext}</td>
			<td>
				{control type="text" name="address[zip]" value="`$record.address.zip`" required=1}
			</td>
		</tr>
		<tr>
			<td>* Phone</td>
			<td>
				{control type="text" name="address[phone]" value="`$record.address.phone`" required=1}
			</td>
		</tr>
		
		<tr>
			<td>* {'Address Type'|gettext}</td>
			<td class="state_field">
				{control type="dropdown" name="address[address_type]" items="Business,Military,Residential" default=$record->address_type|default:"Residential" value="`$record.address.address_type`"}
			</td>
		</tr>
	
		<tr>
			<td>* {'Email Address'|gettext}</td>
			<td>
				{control type="text" name="address[email]" value="`$record.address.email`" required=1}	
			</td>
		</tr>
	</table>
	<h3>{'Credit Card Information'|gettext}</h3>
	<table>
		<tr>
			<td style="width: 143px;">*&#160;Card&#160;Type</td>
			<td class="creditcard-form state_field">
                {control type="dropdown" name="billing[cc_type]" values="MasterCard,VisaCard,DiscoverCard,AmExCard" items="MasterCard,Visa,Discover,American Express"}
			</td>
		</tr>

		<tr>
			<td style="width: 143px;">*&#160;Card&#160;Number</td>
			<td class="creditcard-form">
				<div required"="" class="text-control control " id="cc_numberControl">
					<input type="text" class="text" name="billing[cc_number]" id="cc_number" size="20" maxlength="20" onkeypress="return integer_filter.on_key_press(this, event);" onblur="integer_filter.onblur(this);" onfocus="integer_filter.onfocus(this);" onpaste="return integer_filter.onpaste(this, event);" credit_card_number caption="Credit Card Number"/>
				</div>
			</td>
		</tr>
		<tr>
			<td style="width: 143px;">*&#160;Expiration</td>
			<td class="creditcard-form dropdown">
				{control type="dropdown" name="billing[expiration_month]" items="01,02,03,04,05,06,07,08,09,10,11,12" default="01"}
				<div style="font-size: 14px; margin-top: 5px;">/</div>
				{control type="dropdown" name="billing[expiration_year]" items="2012,2013,2014,2015,2016,2017,2018"}
			</td>
		</tr>

		<tr>
			<td style="width: 143px;">*&#160;CVV&#160;Number <br />(<a style="font-size:10px;" href="http://en.wikipedia.org/wiki/Card_Verification_Value" target="_blank" class="cvv_ver">What is a CVV Number?</a>)</td>
			<td class="creditcard-form">
				<div required"="" class="text-control control " id="cvvControl">
					<input type="text" class="text" id="cvv" name="billing[cvv]" size="4" maxlength="4" onkeypress="return integer_filter.on_key_press(this, event);" onblur="integer_filter.onblur(this);" onfocus="integer_filter.onfocus(this);" onpaste="return integer_filter.onpaste(this, event);" credit_card_cvv caption="CVV Number"/>
				</div>
			</td>
		</tr>
		<tr>
			<td style="width: 143px;"></td>
			<td>
				<input type="submit" class="awesome medium yellow" value="Next Page"/>
			</td>
		</tr>
			
	</table>
       
	{/form}
</div>