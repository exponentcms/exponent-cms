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

<div class="module module action">
    <h1>{'Create User Group'|gettext}</h1>
    {form action=update_group}
        {control type="hidden" name="id" value=$record->id}
        {control type="text" name="name" label="Name"|gettext value=$record->name}
        {control type="textarea" name="description" label="Description"|gettext value=$record->description}
        {control type="dropdown" name="redirect" label="Login Landing Page"|gettext includeblank="None"|gettext items=navigationController::levelDropdownControlArray(0,0,array(),false,'view',true) value=$record->redirect description='Redirect group members to a specific page when logging in'|gettext}
        {control type="checkbox" name="inclusive" label="Is this a Default Group?"|gettext:cat:'?' value=1 checked=$record->inclusive description='Should new accounts be automatically assigned to this group?'|gettext}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
