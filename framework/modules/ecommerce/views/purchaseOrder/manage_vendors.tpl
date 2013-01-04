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
 
{css unique="purchase-orders" corecss="tables" link="`$asset_path`css/purchaseorder.css"}

{/css}

<div id="managevendors" class="module purchaseorder managevendor">

    <h1>{"Viewing Vendors"|gettext}</h1>

    <div class="module-actions">
        {icon action=manage class=manage text="Manage Purchase Orders"|gettext}  |  
        {icon action=create class=add text="Create new Purchase Order"|gettext}  |  
        {icon action=edit_vendor class=add text="Add a new vendor"|gettext}
    </div>

    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>
                {"Vendor"|gettext}
                </th>
                <th>
                {"Action"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
			{foreach from=$vendors item=vendor key=key name=vendor}
                <tr class='{cycle values="odd,even"}'>
                    <td>
                        <a href="{link action=show_vendor id=$vendor->id}">{$vendor->title}</a>
                    </td>
                    <td>
                        {permissions}
                            {icon action=edit_vendor class="edit" id=$vendor->id}
                            {icon action=delete_vendor class="delete" id=$vendor->id}
                        {/permissions}
                    </td>
                </tr>
			{/foreach}
        </tbody>
    </table>
</div>

{script unique="purchase-orders" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {

    });
{/literal}
{/script}