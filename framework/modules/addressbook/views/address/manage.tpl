{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div id="storeconfig" class="module address manage">
    <h1>{"Manage Locations"|gettext}</h1>
    <div class="module-actions">
        {icon action=edit_country class="add" text="Add a Country"|gettext}
        {icon action=edit_region class="add" text="Add a Region"|gettext}
    </div>
    <blockquote>
        {'Select the Countries and States/Provinces below that you would like to show for users creating billing and shipping addresses'|gettext}{br}
        {'These are also the list displayed for tax class management, geo upcharges, etc...'|gettext}:
    </blockquote>
	{form action=manage_update}
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
                        {"Default / Rank"|gettext}
                    </th>
                    <th>
                        {"Actions"|gettext}
                    </th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$countries item=country}
                    <tr class="{cycle values="odd,even"}">
                        <td colspan="2">
                            {control type="checkbox" name="country[`$country->id`]" label=$country->name value=1 checked=$country->active}
                        </td>
                        <td>
                            {control type="radio" name="country_default" label="Default Country"|gettext|cat:"?" value=$country->id checked=$country->is_default}
                        </td>
                        <td>
                            {icon action=edit_country record=$country img="edit.png" title='Edit this country'|gettext}
                            {icon action=delete_country record=$country img="delete.png" title='Delete this country and its regions'|gettext}
                        </td>
                    </tr>
                    {foreach from=$regions item=region}
                        {if $region->country_id == $country->id}
                            <tr class="{cycle values="odd,even"}">
                                <td>
                                    &#160;
                                </td>
                                <td>
                                    {control type="checkbox" name="region[`$region->id`]" label=$region->name value=1 checked=$region->active}
                                </td>
                                <td>
                                    {control type="text" name="region_rank[`$region->id`]" label="Rank"|gettext|cat:":" size="5" value=$region->rank}
                                </td>
                                <td>
                                    {icon action=edit_region record=$region img="edit.png" title='Edit this region'|gettext}
                                    {icon action=delete_region record=$region img="delete.png" title='Delete this region'|gettext}
                                </td>
                            </tr>
                            <!-- div>
                                <div style="margin-left: 25px; width:49%;">{control type="checkbox" name="address_state[`$region->id`]" label=$region->name value=1 checked=$config.address_allow_admins_all}</div>
                                <div style="float: right; width:49%;">{control type="text" name="address_state_rank[x]" label=" " size="10" value=$config.invoice_subject}</div>
                                <div style="clear:both;"></div>
                            </div -->
                        {/if}
                    {foreachelse}
                        {'No defined regions in this country'|gettext}.
                    {/foreach}
                {/foreach}
            </tbody>
        </table>
        {control type=buttongroup submit="Save Location Configuration"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
