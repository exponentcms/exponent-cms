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
{script unique="hidePasswordFields" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
     // start coding
     var checkbox = Y.one('#remember_me'); //the checkbox
     checkbox.on('click',function(e){
         var psswrd = Y.one("#passwordDiv");//div wrapping the password box
         psswrd.toggleClass('hide-me');
     });
})
{/literal}
{/script}
<div class="module address edit address-form">
    {if $record->id != ""}
        <h1>Editing address for {$record->firstname} {$record->lastname}</h1>
    {else}
        <h1>New {$modelname}</h1>
    {/if}

    <p>
        <em>Fields marked with an * are required.</em>
    </p>
    {*eDebug var=$record*}
    
    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=is_default value=$record->is_default}
        {control type=hidden name=is_shipping value=$record->is_shipping}
        {control type=hidden name=is_billing value=$record->is_billing}
        {control type=text name=firstname label="*First Name" value=$record->firstname}
        {control type=text name=middlename label="Middle Name" value=$record->middlename}
        {control type=text name=lastname label="*Last Name" value=$record->lastname}
        {control type=text name=organization label="Company/Organization" value=$record->organization}
        {control type=text name=address1 label="*Street Address" value=$record->address1}
        {control type=text name=address2 label="Apt/Suite #" value=$record->address2}
        {control type=text name=city label="*City" value=$record->city}
        
        {if $user->is_admin || $user->is_acting_admin}
            {control type=state name=state label="*State" includeblank="-- Choose a State -- " all_us_territories=true exclude="6,8,10,17,30,46,50" value=$record->state add_other=true}
            {control type=text name=non_us_state label="&nbsp;State/Province if non-US" value=$record->non_us_state}           
            {control type=country name=country label="&nbsp;Country" value=$record->country|default:223}            
        {else}
            {control type=state name=state label="*State" includeblank="-- Choose a State -- " value=$record->state all_us_territories=true exclude="6,8,10,17,30,46,50"}
            {control type=hidden name=country value=223}
        {/if}
        
        {control type=text name=zip label="*Zip Code" value=$record->zip}
        {control type="text" name="phone" label="*Phone Number (xxx-xxx-xxxx)" value=$record->phone}
        {control type="text" name="email" label="*Email Address" value=$record->email}
        {if !$user->isLoggedIn()}
            
            
            {control type="checkbox" id="remember_me" name="remember_me" label="Remember Me?" value=1 checked=true}
            
 
            <div id="passwordDiv">
                            If you would like us to remember you, simply supply a password here and you may login to this site anytime to track your orders and view your order history. {br}
            Otherwise uncheck "Remember Me?" and continue anonymously. 
                {control type="password" name="password" label="*Password"}
                {control type="password" name="password2" label="*Confirm Password"}                
            </div>
            
            <!--The following field is an anti-spam measure to prevent fradulent account creation. -->
            {* control type="antispam" *}
        {/if}
        {control type=buttongroup submit="Save Address and Continue" cancel="Cancel"}            
        
    {/form}
</div>
