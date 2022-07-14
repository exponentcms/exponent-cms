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

{css unique="address-edit" link="`$asset_path`css/address.css"}

{/css}

<div class="module order address edit address-form">
    <h1>{'Editing'|gettext} {if $type == 'b'}{'Billing'|gettext}{else}{'Shipping'|gettext}{/if} {'address'|gettext}</h1>
    <blockquote>
        <em>{'Fields marked with an * are required.'|gettext}</em>
    </blockquote>
    {form action=save_address}
        {control type=hidden name=orderid value=$orderid}
        {control type=hidden name=addyid value=$record->id}
        {control type=hidden name=type value=$type}
        {control type=hidden name=same value=$same}
        <blockquote>
            {if $same==true}
                {'This address is the same for both shipping and billing on this order.'|gettext}&#160;&#160;
                {'If you update the existing address, it will update both the shipping and billing address for this order.'|gettext}&#160;&#160;
                {'If you save this as a new address, it will leave the existing address as is and only update the'|gettext} {$type} {'address'|gettext}.{br}
            {else}
                {'If you update the existing address, it will change this saved address permanently.'|gettext}
                {'If you save this as a new address, it will leave the existing address as-is to be used later by the customer and create a new'|gettext} {$type} {'address for this order.'|gettext}{br}
            {/if}
        </blockquote>
        {control type=radiogroup label='' items='Update existing address, Save as new address'|gettxtlist values='0,1' name=save_option default='0' focus=1}
        {control type=checkbox label='Default address for this customer?'|gettext flip=true name='address[is_default]' value=1 checked=$record->is_default}
        {br}
        {control type=hidden name='address[is_shipping]' value=$record->is_shipping}
        {control type=hidden name='address[is_billing]' value=$record->is_billing}
        {control type=text name='address[firstname]' label="First Name"|gettext value=$record->firstname required=true}
        {control type=text name='address[middlename]' label="Middle Name"|gettext value=$record->middlename}
        {control type=text name='address[lastname]' label="Last Name"|gettext value=$record->lastname required=true}
        {control type=text name='address[organization]' label="Company/Organization"|gettext value=$record->organization}
        {control type=text name='address[address1]' label="Street Address"|gettext value=$record->address1 required=true}
        {control type=text name='address[address2]' label="Apt/Suite #"|gettext value=$record->address2}
        {control type=text name='address[city]' label="City"|gettext value=$record->city required=true}

        {*{control type=state name='address[state]' label="*"|cat:"State" includeblank="-- Choose a State --"|gettext default=$record->state add_other=true all_us_territories=true exclude="6,8,10,17,30,46,50"}*}
        {*{control type=text name='address[non_us_state]' label="&#160;"|cat:("State/Province if non-US"|gettext) value=$record->non_us_state}*}
        {*{control type=country name='address[country]' label="&#160;"|cat:("Country"|gettext) default=$record->country|default:223}*}
        {control type=countryregion name='address[address]' label="Country/Region"|gettext country_default=$record->country|default:223 region_default=$record->state includeblank="-- Choose a State --"|gettext required=true}

        {control type=text name='address[zip]' label="Zip Code"|gettext value=$record->zip required=true}
        {control type=tel name="address[phone]" label="Phone Number"|gettext|cat:" (xxx-xxx-xxxx)" value=$record->phone required=true}
        {control type=email name="address[email]" label="Email Address"|gettext value=$record->email required=true}
        {br}
        {control type=buttongroup submit="Save Address Change"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
