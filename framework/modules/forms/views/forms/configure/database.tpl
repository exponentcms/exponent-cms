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

<div class="form_header">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Form Database Settings"|gettext) module="form-database-settings"}
        </div>
        <h2>{"Form Database Settings"|gettext}</h2>
    </div>
</div>
{if $config.is_saved}
    {control type="checkbox" name="is_saved" label="Save Submissions to the Database?"|gettext value=1 checked=$config.is_saved disabled=true}
    {control type=hidden name=is_saved value=$config.is_saved}
{else}
    {control type="checkbox" name="is_saved" label="Save Submissions to the Database?"|gettext value=1 checked=$config.is_saved}
{/if}
<blockquote>
    {'To help prevent data loss, you cannot remove a form\'s database table once it has been added.'|gettext}
</blockquote>
