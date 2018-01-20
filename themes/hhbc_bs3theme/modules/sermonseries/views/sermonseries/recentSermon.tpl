{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

{if $error}
    {permissions}
        <div class="module-actions">
            {message center=1 text='There appear to be no sermons. Please add a sermon series and a sermon before using the Recent Sermons view!'|gettext}
            {if $permissions.create}
                {icon class="add" action="edit" text='Add a Sermon Series'|gettext}
            {/if}
            {icon class=view action=showall text="Show All Sermon Series"|gettext}
        </div>
    {/permissions}
{/if}
{$hide_attachments = true}
{exp_include file="show.tpl"}
