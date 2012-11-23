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

<div id="addyblock" class="cart createaddress">
	{form name=setaddy action="setAddress" ajax=true}
        {foreach from=$addresses item=addy name=addresses}
            <div class="address">
                <a href="javascript:void(0)" onclick="YAHOO.util.Connect.asyncRequest('GET', 'http://adam/core/cart/setAddress/1');">{'Make this my shipping address'|gettext}</a>
                {control type=radio flip=true name=shipping label="Ship to this address"|gettext value=$address->id}
                <strong>{$addy->firstname} {$addy->middlename} {$addy->lastname}</strong>{br}
                {$addy->address1}{br}
                {if $addy->address2 != ""}{$addy->address2}{br}{/if}
                {$addy->city}. {$addy->state|statename} {$addy->zip}{br}
                {if $addy->address2 == ""}{br}{/if}
                {control type=radio flip=true name=billing label="This is my billing address"|gettext value=$address->id}
            </div>
        {foreachelse}
            <a href="javascript:void(0);" onclick="divtoggle('saddy')">{'You don\'t have any address configured yet.  Click here to setup a new address'|gettext}</a>
        {/foreach}
	{/form}
    {clear}
	{*{br}<a href="javascript:void(0);" onclick="divtoggle('saddy')">{'Add a new address'|gettext}</a>*}
    {toggle id=saddy link='Add a new address'|gettext}
	{*<div id="saddy" style="display:none">*}
        {form name="newaddy" action=createaddress ajax=true update=addyblock}
            {control type=text name=firstname label="First Name"|gettext}
            {control type=text name=middlename label="Middle Name"|gettext}
            {control type=text name=lastname label="Last Name"|gettext}
            {control type=text name=address1 label="Address"|gettext}
            {control type=text name=address2 label=" "}
            {control type=text name=city label="City"|gettext}
            {control type=state name=state label="State"|gettext}
            {control type=text name=zip label="Zip Code"|gettext}
            {control type=buttongroup submit="Save Address"|gettext}
        {/form}
    {*</div>*}
    {/toggle}
</div>
