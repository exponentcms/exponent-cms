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

<div class="module importexport manage">
    <blockquote>
        {"Additional Form Import/Export methods available in"|gettext}&#160;<a href="{link controller=forms action=manage}">{'Site Forms Manager'|gettext}</a>
    </blockquote>
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Importing Data"|gettext) module="import-export-data"}
        </div>
        <h2>{"Import Data"|gettext}</h2>
    </div>
    {form action=import}
        {control type="dropdown" name="import_type" label="Select Module Data to Import"|gettext items=$importDD}
        {control type="buttongroup" submit="Begin Import"|gettext cancel="Cancel"|gettext}
    {/form}
    {br}
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Exporting Data"|gettext) module="import-export-data"}
        </div>
        <h2>{"Export Data"|gettext}</h2>
    </div>
    {form action=export}
        {control type="dropdown" name="export_type" label="Select Module Data to Export"|gettext items=$exportDD}
        {control type="buttongroup" submit="Begin Export"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
