{*
 * Copyright (c) 2004-2019 OIC Group, Inc.
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

<div class="module order_type edit">
    <h1>
        {if $record->id == ""}{'New Order Type'|gettext}{else}{'Editing'|gettext} {$record->title}{/if}
    </h1>

    {form action=update}
        {control type="hidden" name="id" value=$record->id}
        {control type="text" name="title" label="Order Type"|gettext value=$record->title focus=1}
        {control type="checkbox" name="is_default" label="Default?"|gettext value=1 checked=$record->is_default}
        {control type="checkbox" name="creates_new_user" label="Creates New User?"|gettext value=1 checked=$record->creates_new_user description='Only applies to Passthru payment option'|gettext}
        {control type="checkbox" name="emails_customer" label="Emails Customer?"|gettext value=1 checked=$record->emails_customer}
        {control type="checkbox" name="affects_inventory" label="Affects Inventory?"|gettext value=1 checked=$record->affects_inventory}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
