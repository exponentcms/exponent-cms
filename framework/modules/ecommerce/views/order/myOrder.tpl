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

{if $checkout}
    {$breadcrumb = [
        0 => [
            "title" => "{'Summary'|gettext}",
            "link"  => ""
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
    {breadcrumb items=$breadcrumb active=4 style=flat}
{/if}
<div class="item-actions">
    {br}
    {printer_friendly_link class="{button_style}" text="Print this invoice"|gettext view="show_printable" show=1}
    {permissions}
        {if $permissions.manage}
            <a class="{button_style}" href="{link controller='order' action='createReferenceOrder' id=$order->id}">{'Spawn Reference Order'|gettext}</a>
        {/if}
    {/permissions}
</div>

{exp_include file="invoice.tpl"}
