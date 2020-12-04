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

{script unique="hidePasswordFields" yui3mods="node"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    // start coding
    var checkbox = Y.one('#remember_me'); //the checkbox
    if (checkbox){
        checkbox.on('click',function(e){
            var psswrd = Y.one("#passwordDiv .passwords");
            psswrd.toggleClass('hide');
            if (Y.one("#passwordDiv .hide") == null) {
                psswrd.all('input').setAttribute('required','required');
            } else {
                psswrd.all('input').removeAttribute('required');
            }
        });
    }
})
{/literal}
{/script}
<div class="module address edit address-form">
    {if $checkout}
        {$breadcrumb = [
            0 => [
                "title" => "{'Summary'|gettext}",
                "link"  => makeLink(['controller'=>'cart','action'=>'show'])
            ],
            1 => [
                "title" => "{'Sign In'|gettext}",
                "link"  => ""
            ],
            2 => [
                "title" => "{'Shipping/Billing'|gettext}",
                "link"  => ""
            ],
            3 => [
                "title" => "{'Confirmation'|gettext}",
                "link"  => ""
            ],
            4 => [
                "title" => "{'Complete'|gettext}",
                "link"  => ""
            ]
        ]}
        {breadcrumb items=$breadcrumb active=1 style=flat}
    {/if}
    {if $record->id != ""}
        <h1>{'Editing address for'|gettext} {$record->firstname} {$record->lastname}</h1>
    {else}
        <h1>{'New'|gettext} {$model_name}</h1>
    {/if}
    <blockquote>
        <em>{'Fields marked with an * are required'|gettext}.</em>
    </blockquote>
    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=is_default value=$record->is_default}
        {control type=hidden name=is_shipping value=$record->is_shipping}
        {control type=hidden name=is_billing value=$record->is_billing}
        {control type=text name=firstname label="First Name"|gettext required=true value=$record->firstname focus=1}
        {control type=text name=middlename label="Middle Name"|gettext value=$record->middlename}
        {control type=text name=lastname label="Last Name"|gettext required=true value=$record->lastname}
        {control type=text name=organization label="Company/Organization"|gettext value=$record->organization}
        {control type=text name=address1 label="Street Address"|gettext required=true value=$record->address1}
        {control type=text name=address2 label="Apt/Suite #"|gettext value=$record->address2}
        {control type=text name=city label="City"|gettext required=true value=$record->city}
        {*{if ($user->is_admin || $user->is_acting_admin) && $admin_config == true}*}
            {*{control type=state name=state label="State/Province"|gettext required=true includeblank="-- Choose a State --"|gettext default=$record->state add_other=true}*}
            {*{control type=text name=non_us_state label="&#160;"|cat:"Non U.S. State/Province"|gettext value=$record->non_us_state}*}
            {*{control type=country name=country label="Country"|gettext show_all=true default=$record->country|default:223}*}
        {*{else}*}
            {*{control type=state name=state label="State/Province"|gettext required=true includeblank="-- Choose a State --"|gettext default=$record->state}*}
            {*{control type=country name=country label="Country"|gettext default=$record->country}*}
        {*{/if}*}
        {control type=countryregion name=address label="Country/State"|gettext country_default=$record->country|default:223 region_default=$record->state includeblank="-- Choose a State --"|gettext required=true}
        {control type=text name=zip label="Zip/Postal Code"|gettext required=true value=$record->zip}
        {*{control type="text" name="phone" label="Phone Number"|gettext|cat:" <span class=\"example\">ex: 480-555-4200</span>" required=true value=$record->phone}*}
        {control type=tel name="phone" label="Phone Number"|gettext required=true value=$record->phone placeholder="ex: 480-555-4200"}
        {control type="dropdown" name="address_type" label="Address Type"|gettext items="Business,Military,Residential"|gettxtlist values="Business,Military,Residential"|gettxtlist default=$record->address_type|default:"Residential"}
        {*{control type="text" name="email" label="Email Address"|gettext required=true value=$record->email}*}
        {control type=email name="email" label="Email Address"|gettext required=true value=$record->email}
        {if !$user->isLoggedIn()}
            <div id="passwordDiv">
                {control type="checkbox" flip=1 id="remember_me" name="remember_me" label="Remember Me"|gettext|cat:"?" value=1 checked=true}
                <div class="passwords">
                    <blockquote>
                        {"If you would like us to remember you, simply supply a password here and you may login to this site anytime to track your orders and view your order history."|gettext}&#160;&#160;
                        {'Otherwise uncheck \'Remember Me?\' and continue anonymously.'|gettext}
                    </blockquote>
                    {control type="password" name="password" class="col-sm-4" meter=1 label="Password"|gettext required=true}
                    {control type="password" name="password2" label="Confirm Password"|gettext required=true}
                </div>
            </div>

            <!--The following field is an anti-spam measure to prevent fraudulent account creation. -->
            {* control type="antispam" *}
        {/if}
        {control type=buttongroup size=large submit="Save Address and Continue"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
