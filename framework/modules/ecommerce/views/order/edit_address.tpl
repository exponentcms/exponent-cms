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

<div class="module address edit address-form">
    <h1>Editing address</h1>
    <p>
        <em>Fields marked with an * are required.</em>
    </p>
    {form action=save_address}
        {control type=hidden name=orderid value=$orderid}
        {control type=hidden name=addyid value=$record->id}
        {control type=hidden name=type value=$type}
        {control type=hidden name=same value=$same}
        {if $same==true}
            This address is the same for both shipping and billing on this order. If you update the existing address, it will update both the 
            shipping and billing address for this order.  If you save this as a new address, it will leave the existing address as is and only update the {$type} address.{br}
        {else}
            If you update the existing address, it will change this saved address permanently.  If you save this as a new address, it will leave the existing address as-is to be used later by the customer and create a new {$type} address for this order.{br}
        {/if}
            {control type=radiogroup label='' items='Update existing address, Save as new address' values='0,1' name=save_option default='0'}        
            
        {control type=checkbox label='Default address for this customer?' flip=true name=address[is_default] value=1 checked=$record->is_default}
        {control type=hidden name=address[is_shipping] value=$record->is_shipping}
        {control type=hidden name=address[is_billing] value=$record->is_billing}
        {control type=text name=address[firstname] label="*First Name" value=$record->firstname}
        {control type=text name=address[middlename] label="Middle Name" value=$record->middlename}
        {control type=text name=address[lastname] label="*Last Name" value=$record->lastname}
        {control type=text name=address[organization] label="Company/Organization" value=$record->organization}
        {control type=text name=address[address1] label="*Street Address" value=$record->address1}
        {control type=text name=address[address2] label="Apt/Suite #" value=$record->address2}
        {control type=text name=address[city] label="*City" value=$record->city}
        
        {control type=state name=address[state] label="*State" includeblank="-- Choose a State -- " value=$record->state add_other=true all_us_territories=true exclude="6,8,10,17,30,46,50"}
        {control type=text name=address[non_us_state] label="&nbsp;State/Province if non-US" value=$record->non_us_state}           
        {control type=country name=address[country] label="&nbsp;Country" value=$record->country|default:223}    
        
        {control type=text name=address[zip] label="*Zip Code" value=$record->zip}
        {control type="text" name="address[phone]" label="*Phone Number (xxx-xxx-xxxx)" value=$record->phone}
        {control type="text" name="address[email]" label="*Email Address" value=$record->email}
       
        {control type=buttongroup submit="Save Address Change" cancel="Cancel"}            
        
    {/form}
</div>
