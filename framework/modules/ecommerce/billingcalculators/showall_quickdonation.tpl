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
{css unique="donation" link="`$asset_path`css/donation.css"}
{/css}
<div class="module donation showall">
    {if $moduletitle != ''}<h1>{$moduletitle}</h1>{/if}
	{form name=donationamount`$key` action="quickdonation_process"}
	{control type="hidden" name="donate[product_id]" value=1}
	<input id="quick" type="hidden" name="donate[quick]" value="0" size="20" class="hidden "/>
	<input type="hidden" name="donate[product_type]" value="donation" size="20" class="hidden "/>
	<h3>Donation Information</h3>
    <table>
        <tr>
            <td>*Enter the amount you would like to donate: </td>
            <td>
				{control type="text" name="donate[dollar_amount]" value=`$record.donate.dollar_amount` size=7 filter=money required=1}
            </td>
        </tr>
		
		<tr>
			<td>Would you like to make this donation recurring every month? </td>
			 <td>
				{control type="radiogroup" name="donate[extra][recurring]" items="Yes,No" values="Recurring:Yes,Recurring:No" default="Recurring:Yes" value=`$record.donate.extra.recurring`}
            </td>
		</tr>
		
		<tr>
			<td>Choose which day for your donation to be processed each month: </td>
			 <td>
				{control type="radiogroup" name="donate[extra][recurring_day]" items="1st of the Month,15th of the Month" values="Recurring Day:1st of the Month,Recurring Day:15th of the Month" default="Recurring Day:1st of the Month" value=`$record.donate.extra.recurring_day`}
            </td>
		</tr>
		
		<tr>
			<td colspan="2" style="padding-top: 10px;">** If you select to make your donation recurring, your first recurrence will start the month following your initial donation today.</td>
		</tr>
	</table>
	
	<h3>Billing Address</h3>
	<table>
		<tr>
			<td>* First Name</td>
			<td>
				{control type="text" name="address[firstname]" value=`$record.address.firstname` required=1}
			</td>
		</tr>
		
		<tr>
			<td>&nbsp;&nbsp;Middle Name</td>
			<td>
				{control type="text" name="address[middlename]" value=`$record.address.middlename`}
			</td>
		</tr>
		
		<tr>
			<td>* Last Name</td>
			<td>
				{control type="text" name="address[lastname]" value=`$record.address.lastname` required=1}
			</td>
		</tr>
		<tr>
			<td>&nbsp;&nbsp;Company/Organization</td>
			<td>
				{control type="text" name="address[organization]" value=`$record.address.organization`}
			</td>
		</tr>
		<tr>
			<td>* Address</td>
			<td>
				{control type="text" name="address[address1]"  value=`$record.address.address1` required=1}
			</td>
		</tr>
		<tr>
			<td>&nbsp;&nbsp;Address 2</td>
			<td>
				{control type="text" name="address[address2]" value=`$record.address.address2`}
			</td>
		</tr>
	
		<tr>
			<td>* City</td>
			<td>
				{control type="text" name="address[city]" value=`$record.address.city` required=1}
			</td>
		</tr>
		
		<tr>
			<td>* State</td>
			<td class="state_field"> 
				{control type=state name=address[state] includeblank="-- Choose a State -- " value=`$record.address.state` label="" required=1}            
			</td>
		</tr>
		<tr>
			<td>* Country/State:</td>
			<td>       
				{control type=country name=address[country] value=`$record.address.country` value=$record->country}
			</td>
		</tr>
		
		<tr>
			<td>* Zip&nbsp;Code</td>
			<td>
				{control type="text" name="address[zip]" value=`$record.address.zip` required=1}
			</td>
		</tr>
		<tr>
			<td>* Phone</td>
			<td>
				{control type="text" name="address[phone]" value=`$record.address.phone` required=1}
			</td>
		</tr>
		
		<tr>
			<td>* Address Type</td>
			<td>
				{control type="dropdown" name="address[address_type]" items="Business,Military,Residential" default=$record->address_type|default:"Residential" value=`$record.address.address_type`}
			</td>
		</tr>
	
		<tr>
			<td>* Email Address</td>
			<td>
				{control type="text" name="address[email]" value=`$record.address.email` required=1}	
			</td>
		</tr>
	</table>
	<h3>Credit Card Information</h3>
	<table>
		<tr>
			<td>*&nbsp;Card&nbsp;Type</td>
			<td class="creditcard-form">
	
					{control type="dropdown" name="billing[cc_type]" values="MasterCard,VisaCard" items="MasterCard,Visa"}
			
			
			</td>
		</tr>

		<tr>
			<td>*&nbsp;Card&nbsp;Number</td>
			<td class="creditcard-form">
				<div required"="" class="text-control control " id="cc_numberControl">
					<input type="text" class="text" name="billing[cc_number]" id="cc_number" size="20" maxlength="20" onkeypress="return integer_filter.on_key_press(this, event);" onblur="integer_filter.onblur(this);" onfocus="integer_filter.onfocus(this);" onpaste="return integer_filter.onpaste(this, event);" credit_card_number caption="Credit Card Number"/>
				</div>
			</td>
		</tr>
		<tr>
			<td>*&nbsp;Expiration</td>
			<td class="creditcard-form dropdown">
				{control type="dropdown" name="billing[expiration_month]" items="01,02,03,04,05,06,07,08,09,10,11,12" default="01"}
				<div>/</div>
				{control type="dropdown" name="billing[expiration_year]" items="2012,2013,2014,2015,2016,2017,2018"}
			</td>
		</tr>

		<tr>
			<td>*&nbsp;CVV&nbsp;Number (<a style="font-size:10px;" href="http://en.wikipedia.org/wiki/Card_Verification_Value" target="_blank" class="cvv_ver">What is a CVV Number?</a>)</td>
			<td class="creditcard-form">
				<div required"="" class="text-control control " id="cvvControl">
					<input type="text" class="text" id="cvv" name="billing[cvv]" size="4" maxlength="4" onkeypress="return integer_filter.on_key_press(this, event);" onblur="integer_filter.onblur(this);" onfocus="integer_filter.onfocus(this);" onpaste="return integer_filter.onpaste(this, event);" credit_card_cvv caption="CVV Number"/>
				</div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="awesome medium yellow" value="Next Page"/>
			</td>
		</tr>
			
	</table>
       
	{/form}
</div>