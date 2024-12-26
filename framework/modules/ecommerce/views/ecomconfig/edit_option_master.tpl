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

{permissions}
{if $permissions.manage}
    <div class="module storeadmin edit_option_master">
        <h1>{$moduletitle|default:"Edit Product Options"|gettext}</h1>
        {if $record->timesImplemented > 0}
            <blockquote>
                {'This option is being used by'|gettext} {$record->timesImplemented} {'products on your site.  Changing the name will change it for all the products currently using it.'|gettext}
            </blockquote>
        {/if}
        {form action=update_option_master}
            {control type="hidden" name=id value=$record->id}
            {control type="hidden" name=rank value=$record->rank}
            {control type="hidden" name=optiongroup_master_id value=$record->optiongroup_master_id}
            {control type="text" name="title" label="Name"|gettext value=$record->title focus=1}
            {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
{/if}
{/permissions}
