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
 
{css unique="purchase-orders" corecss="tables" link="`$asset_path`css/purchaseorder.css"}

{/css}



<div id="managepurchaseorders" class="module purchaseorder manage">

    <h1>{"Viewing Purchase Order"|gettext}</h1>

    <div class="module-actions">
        {icon action=create class=add text="Create new Purchase Order"|gettext}  |  
        {icon action=manage_vendors class=manage text="Manage Vendors"|gettext}  |
        {icon action=edit_vendor class=add text="Add a new vendor"|gettext}
    </div>

    <div class="filters">
        {control type="text" name="dynamicfilter" id="dynamicfilter" label="Filter By Order ID"}
        {control type="text" name="perpage" label="Items per-page" size=5 value=$perpage|default:50}
        {control type="dropdown" name="status" label="Show" items="All Orders,Only Open Orders, Only Closed Orders"}
        {control type="dropdown" name="daterange" label="Within" items="The last Month,The Last 6 months,The Last Year,All Time"}
    </div>

    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>
                {"Order Number"|gettext}
                </th>
                <th>
                {"Vender"|gettext}
                </th>
                <th>
                {"Date"|gettext}
                </th>
                <th>
                {"Status"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="even">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="odd">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="even">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="odd">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="even">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="odd">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="even">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="odd">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="even">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="odd">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="even">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="odd">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="even">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
            <tr class="odd">
                <td>
                hsne-44-jh
                </td>
                <td>
                Name Tapes R Us
                </td>
                <td>
                09/01/2011
                </td>
                <td>
                ordered
                </td>
            </tr>
        </tbody>
    </table>
</div>


{script unique="purchase-orders" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
    
});
{/literal}
{/script}