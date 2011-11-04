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

<div class="module text edit">
    {if $record->id != ""}
        <h1>{'Editing'|gettext}: {$record->title}</h1>
    {else}
        <h1>{'New Text Item'|gettext}</h1>
    {/if}

    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=rank value=$record->rank}
        {control type=text name=title label="Title"|gettext value=$record->title|escape:"html"}
        {control type=html name=body label="Body Content"|gettext value=$record->body}
        {if $config.filedisplay}
            {control type="files" name="files" label="Files"|gettext value=$record->expFile}
        {/if}
        {control type=buttongroup submit="Save Text"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>
