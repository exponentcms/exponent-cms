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

<div class="module eaas showall">
    {permissions}
        {if $permissions.configure}
            {if $moduletitle && !$config.hidemoduletitle}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
            <div class="module-actions">
                {icon class=configure action=configure text="Configure Service"|gettext}
            </div>
            <{$config.item_level|default:'h2'}>{'API Key'|gettext}</{$config.item_level|default:'h2'}>
            <textarea class="form-control" style="width:100%; height:100px;">{$info.apikey}</textarea>
            {*edebug var=$info*}
        {/if}
    {/permissions}
</div>
