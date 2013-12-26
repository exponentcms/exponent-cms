{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="module order_status edit-message">
    <h1>
        {if $record->id == ""}{'New Status'|gettext}{else}{'Editing'|gettext} {$record->title}{/if}
    </h1>
    {form action=update_message}
        {control type="hidden" name="id" value=$record->id}
        {control type="html" name="body" label="Message"|gettext value=$record->body}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
