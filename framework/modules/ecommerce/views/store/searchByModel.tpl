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

<div class="module store show">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create}
            {icon class="add" action=create text="Add a Product"|gettext}
        {/if}
        {if $permissions.manage}
            {icon action=manage text="Manage Products"|gettext}
            {icon controller=storeCategory action=manage text="Manage Store Categories"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}

    {form action=searchByModelForm}
        {control type="text" name="search_string" label=" "}
        {control type="buttongroup" submit="Search"|gettext}
    {/form}
</div>
