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

<div class="module help edit yui-skin-sam">
    {if $record->id != ""}
        <h1>{'Editing Version'|gettext} {$record->version}</h1>
    {else}
        <h1>{'New Help Version'|gettext}</h1>
        <p>
            {'Creating a new version will copy all the docs from the current version over to the new version and make them available for viewing and editing'|gettext}
        </p>
    {/if}

    {form action=update_version}
        {control type=hidden name=id value=$record->id}
        {control type="text" name="version" label="Version"|gettext|cat:" #" value=$record->version}
        {control type=text name=title label="Version Name"|gettext value=$record->title}
        {control type="checkbox" name="is_current" label="Make this the current version"|gettext value=1 checked=$record->is_current}
        {control type=buttongroup submit="Save Version"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>
