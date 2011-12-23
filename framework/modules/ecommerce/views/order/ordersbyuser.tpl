{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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

{css unique="ordersbyusers" corecss="tables"}

{/css}

<div class="module order ordersbyuser">
    <h1>{$moduletitle|default:"My Orders"|gettext}</h1>
    <div id="orders">
		{pagelinks paginate=$page top=1}
        <table id="prods" class="exp-skin-table">
            <thead>
                <tr>
                    {$page->header_columns}
                    <th><span>{'Total'|gettext}</span></th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$page->records item=listing name=listings}
                <tr class="{cycle values="odd,even"}">
                    <td>{$listing->purchased|format_date:$smarty.const.DISPLAY_DATE_FORMAT}</td>
                    <td><a href="{link action=myOrder id=$listing->id}">{$listing->invoice_id}</a></td>
                    <td>${$listing->billingmethod[0]->billing_cost|number_format:2}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
		{pagelinks paginate=$page bottom=1}
    </div>
</div>
