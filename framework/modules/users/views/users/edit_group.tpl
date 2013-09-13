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
        {control type="checkbox" name="inclusive" label="Is this a Default Group?"|gettext value=1 checked=$record->inclusive description='Should new accounts be automatically assigned to this group?'|gettext}
        {group label='Group Global Permissions/Restrictions'|gettext}
            {control type="checkbox" name="prevent_uploads" label="Prevent File Uploads?"|gettext value=1 checked=$record->prevent_uploads description='This group will not be allowed upload files'|gettext}
            {control type="checkbox" name="prevent_profile_change" label="Prevent User Profile Changes?"|gettext value=1 checked=$record->prevent_profile_change description='This group will not be allowed to change their user profiles'|gettext}
            {control type="checkbox" name="hide_exp_menu" label="Hide Exponent Menu?"|gettext value=1 checked=$record->hide_exp_menu description='This group will not be shown the Exponent menu'|gettext}
            {control type="checkbox" name="hide_files_menu" label="Hide Files Menu?"|gettext value=1 checked=$record->hide_files_menu description='This group will not be shown the Files menu'|gettext}
            {control type="checkbox" name="hide_pages_menu" label="Hide Pages Menu?"|gettext value=1 checked=$record->hide_pages_menu description='This group will not be shown the Pages menu'|gettext}
            {control type="checkbox" name="hide_slingbar" label="Hide Entire Exponent Menu Bar?"|gettext value=1 checked=$record->hide_slingbar description='This group will not be shown the Exponent menu bar'|gettext}
            {*{control type="checkbox" name="display_recyclebin" label="Allow restoring modules from recycle bin?"|gettext value=1 checked=$record->display_recyclebin description='This group will be allowed to access the recycle bin when creating new modules'|gettext}*}
        {/group}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
