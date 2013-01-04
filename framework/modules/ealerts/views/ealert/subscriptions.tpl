{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
    <h1>{$moduletitle|default:"Sign Up for E-Alerts"|gettext}</h1>
    <p>
        {'If you would like to stay up to date with email alerts, simply provide your email address and select what you would like to be alerted about.'|gettext}
    </p>
    {form action=subscription_update}
        {control type="hidden" name="id" value=$subscriber->id}
        {control type="hidden" name="key" value=$subscriber->hash}
        <strong>{'Step 1: Let us know your email address'|gettext}</strong>
        {control type="text" name="email" label="Email Address"|gettext value=$subscriber->email}

        <strong>{'Step 2: Select your E-Alerts'|gettext}</strong>
        <table class="exp-skin-table">
            <thead>
                <tr>
                    <th>{'Subscribe'|gettext}</th>
                    <th>{'Name/Description'|gettext}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$ealerts item=ealert}
                    <tr>
                        <td>
                            {control type="checkbox" name="ealerts[]" label=" " value=$ealert->id checked=$subscriptions}
                        </td>
                        <td>
                            <h2>{$ealert->ealert_title}</h2>
                            <p>{$ealert->ealert_desc}</p>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        {control type="antispam"}
        {control type="buttongroup" submit="Sign up now"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
