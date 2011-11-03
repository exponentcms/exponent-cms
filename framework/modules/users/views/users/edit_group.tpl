{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="module module action">
    <h1>{'Create User Group'|gettext}</h1>
    <p>
        {'If you check the \'Default?\' checkbox, user accounts created after this group is saved
        will be added to it. This will not retro-actively add existing users to this group.'|gettext}
    </p>
    
    {form action=update_group}
        {control type="hidden" name="id" value=$record->id}
        {control type="text" name="name" label="Name"|gettext value=$record->name}
        {control type="textarea" name="description" label="Description"|gettext value=$record->description}
        {control type="text" name="redirect" label="Login Landing Page"|gettext value=$record->redirect}
        {control type="checkbox" name="inclusive" label="Default Group"|gettext:cat:'?' value=1 checked=$record->inclusive}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
