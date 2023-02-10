{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

<div class="module navigation edit_redirection">
    <h1>
        {if $record->id == ""}{'New Page Redirection'|gettext}{else}{'Editing Page Redirection'|gettext}{/if}
    </h1>
    {form action=update_redirection}
        {control type="hidden" name="id" value=$record->id}
        {control type="text" name="old_sef_name" label="Redirect From"|gettext value=$record->old_sef_name|default:$sef_name}
        {control type="text" name="new_sef_name" label="Redirect To"|gettext value=$record->new_sef_name}
        {control type="dropdown" name="type" label="HTTP Status Code"|gettext items="Moved Permanently (301),Moved Temporarly (302),Temporary Redirect (307),Permanent Redirect (308),Custom URL"|gettxtlist values="301,302,307,308,url" value=$record->type|default:301}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
