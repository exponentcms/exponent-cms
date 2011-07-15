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
    <h1>{"Up Charge Rate Settings"|gettext}</h1>
	
	{form action=update_upcharge}
		Select the configuration of the Up charge Rate for the Countries and States/Provinces:
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
					{assign var="key" value="country_`$country->id`"}
					{control type="text" name="upcharge[country_`$country->id`]" label="Rate:" size="5" value="`$upcharge.$key`"}
                </td>
            </tr>                        
            {foreach from=$regions item=region}
                {if $region->country_id == $country->id}
					<tr class="{cycle values='odd,even'}">
						<td>&nbsp;</td>
						<td><strong>{$region->name}</strong></td>
						<td>
							{assign var="key" value="region_`$region->id`"}
							{control type="text" name="upcharge[region_`$region->id`]" label="Rate:" size="5" value="`$upcharge.$key`"}
						</td>  
					</tr>                                
                {/if}
            {/foreach}
        {/foreach}
        </tbody>
        </table>
        {control type=buttongroup submit="Save Upcharge Rate" cancel="Cancel"}
    {/form}
</div>