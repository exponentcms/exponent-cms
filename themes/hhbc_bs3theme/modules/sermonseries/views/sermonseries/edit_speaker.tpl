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

<div class="module sermonseries edit">
    <h1>
        {if $record->id == ""}{'New Speaker'|gettext}{else}{'Editing Speaker'|gettext} '{$record->first_name} {$record->last_name}'{/if}
    </h1>
    {form action=update_speaker}
        {control type="hidden" name="id" value=$record->id}
        {control type="text" name="title" label="Name"|gettext value=$record->title}
        {control type="editor" name="body" label="Biography"|gettext value=$record->body}
        {control type="checkbox" postfalse=1 name=is_default label="Make this the Default Speaker?"|gettext checked=$record->is_default value=1}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
