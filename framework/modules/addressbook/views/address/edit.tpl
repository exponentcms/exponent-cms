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

{css unique="address-edit" link="`$asset_path`css/address.css"}

{/css}

{script unique="hidePasswordFields" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
     // start coding
     var checkbox = Y.one('#remember_me'); //the checkbox
     if (checkbox){
         checkbox.on('click',function(e){
             var psswrd = Y.one("#passwordDiv .passwords");//div wrapping the password boxs
             psswrd.toggleClass('hide');
         });
     }
})
{/literal}
{/script}
<div class="module address edit address-form">
    {if $record->id != ""}
        <h1>{'Editing address for'|gettext} {$record->firstname} {$record->lastname}</h1>
    {else}
        <h1>{'New'|gettext} {$modelname}</h1>
    {/if}

    <p>
        <em>{'Fields marked with an * are required'|gettext}.</em>
    </p>
    
    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=is_default value=$record->is_default}
        {control type=hidden name=is_shipping value=$record->is_shipping}
        {control type=hidden name=is_billing value=$record->is_billing}
        {control type=text name=firstname label="<span class=\"required\">*</span>"|cat:"First Name"|gettext value=$record->firstname}
        {control type=text name=middlename label="Middle Name"|gettext value=$record->middlename}
        {control type=text name=lastname label="<span class=\"required\">*</span>"|cat:"Last Name"|gettext value=$record->lastname}
        {control type=text name=organization label="Company/Organization"|gettext value=$record->organization}
        {control type=text name=address1 label="<span class=\"required\">*</span>"|cat:"Street Address"|gettext value=$record->address1}
        {control type=text name=address2 label="Apt/Suite #"|gettext value=$record->address2}
        {control type=text name=city label="<span class=\"required\">*</span>"|cat:"City"|gettext value=$record->city}
        
        {if ($user->is_admin || $user->is_acting_admin) && $admin_config == true}
            {control type=state name=state label="<span class=\"required\">*</span>"|cat:"State/Province"|gettext includeblank="-- Choose a State --"|gettext value=$record->state add_other=true}
            {control type=text name=non_us_state label="&nbsp;"|cat:"Non U.S. State/Province"|gettext value=$record->non_us_state}
            {control type=country name=country label="&nbsp;"|cat:"Country"|gettext show_all=true value=$record->country|default:223}
        {else}
            {control type=state name=state label="<span class=\"required\">*</span>"|cat:"State/Province"|gettext includeblank="-- Choose a State --"|gettext value=$record->state}
            {control type=country name=country label="&nbsp;"|cat:"Country"|gettext value=$record->country}
        {/if}
        
        {control type=text name=zip label="<span class=\"required\">*</span>"|cat:"Zip/Postal Code"|gettext value=$record->zip}
        {control type="text" name="phone" label="<span class=\"required\">*</span>"|cat:("Phone Number"|gettext)|cat:" <span class=\"example\">ex: 480-555-4200</span>" value=$record->phone}
        {control type="dropdown" name="address_type" label="Address Type"|gettext items="Business,Military,Residential"|gettext values="Business,Military,Residential" default=$record->address_type|default:"Residential"}
        {control type="text" name="email" label="<span class=\"required\">*</span>"|cat:"Email Address"|gettext value=$record->email}
        {if !$user->isLoggedIn()}
 
            <div id="passwordDiv">
                {control type="checkbox" flip=1 id="remember_me" name="remember_me" label="Remember Me"|gettext|cat:"?" value=1 checked=true}
                <p>
                    {"If you would like us to remember you, simply supply a password here and you may login to this site anytime to track your orders and view your order history.
    Otherwise uncheck \'Remember Me?\' and continue anonymously."|gettext}
                </p>
                <div class="passwords">
                    {control type="password" name="password" label="<span class=\"required\">*</span>"|cat:"Password"|gettext}
                    {control type="password" name="password2" label="<span class=\"required\">*</span>"|cat:"Confirm Password"|gettext}
                </div>
            </div>
            
            <!--The following field is an anti-spam measure to prevent fradulent account creation. -->
            {* control type="antispam" *}
        {/if}
        {control type=buttongroup submit="Save Address and Continue"|gettext cancel="Cancel"|gettext}
        
    {/form}
</div>
