{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

{css unique="subscriptions" corecss="tables"}

{/css}

<div class="module ealerts showall">
    <h1>{$moduletitle|default:"Sign Up for E-Alerts"}</h1>
    <p>
        If you would like to stay up to date with email alerts, simply give us your email address
        and pick what you would like to be alerted about. 
    </p>
    {form action=subscription_update}
    {control type="hidden" name="id" value=$subscriber->id}
    {control type="hidden" name="key" value=$subscriber->hash}
    <strong>Step 1: Let us know your email address</strong>
    {control type="text" name="email" label="Email Address" value=$subscriber->email}
    
    <strong>Step 2: Pick your E-Alerts</strong>
    <table class="exp-skin-table">
    <thead>
    <tr>
        <th>Subscribe</th>
        <th>Name/Description</th>
    </tr>
    </thead>
    <tbody>    
    {foreach from=$ealerts item=ealert}
        <td>
            {control type="checkbox" name="ealerts[]" label=" " value=$ealert->id checked=$subscriptions}
        </td>
        <td>
            <h2>{$ealert->ealert_title}</h2>
            <p>{$ealert->ealert_desc}</p>
        </td>
    {/foreach}    
    </tbody>
    </table>
    {control type="antispam"}    
    {control type="buttongroup" submit="Sign up now" cancel="Cancel"}    
    {/form}
</div>
