{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{css unique="myID" corecss="tables"}
{literal}
.control label.label {
    display:inline-block;
    *display:inline;
    zoom:1;
    vertical-align:top;
    margin-right:10px
}
{/literal}
{/css}
<div id="storeconfig" class="module upcharge manage">
    <h1>{"Location Up-Charge Rate Settings"|gettext}</h1>
    <blockquote>
        If you do NOT see the desired country/region, you will need to activate it <a href="{link controller=address action=manage}">here</a>.
    </blockquote>

	{form action=update_upcharge}
		{'Select the configuration of the Up charge Rate for the Countries and States/Provinces:'|gettext}
        {'Note: the state upcharge rate will be added to the country upcharge rate!'|gettext}
        <table class="exp-skin-table">
            <thead>
                <tr>
                    <th>
                        {"Country"|gettext}
                    </th>
                    <th>
                        {"State"|gettext}
                    </th>
                    <th>
                        {"Rate"|gettext}
                    </th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$countries item=country}
                    <tr class="{cycle values="odd,even"}">
                        <td colspan="2">{$country->name}
                        </td>
                        <td>
                            {$key="country_`$country->id`"}
                            {control type="text" name="upcharge[country_`$country->id`]" label="" size="5" value="`$upcharge.$key`"}
                        </td>
                    </tr>
                    {foreach from=$regions item=region}
                        {if $region->country_id == $country->id}
                            <tr class="{cycle values='odd,even'}">
                                <td>&#160;</td>
                                <td><strong>{$region->name}</strong></td>
                                <td>
                                    {$key="region_`$region->id`"}
                                    {control type="text" name="upcharge[region_`$region->id`]" label="" size="5" value="`$upcharge.$key`"}
                                </td>
                            </tr>
                        {/if}
                    {/foreach}
                {/foreach}
            </tbody>
        </table>
        {control type=buttongroup submit="Save Upcharge Rate"|gettext cancel="Cancel"|gettext}
    {/form}
</div>