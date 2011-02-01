{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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
    <h1>Create User Group</h1>
    <p>
        If you check the "Default?" checkbox, user accounts created after this group is saved 
        will be added to it. This will not retro-actively add existing users to this group.
    </p>
    
    {form action=update_group}
        {control type="hidden" name="id" value=$record->id}
        {control type="text" name="name" label="Name" value=$record->name}
        {control type="textarea" name="description" label="Description" value=$record->description}
        {control type="text" name="redirect" label="Login Landing Page" value=$record->redirect}
        {control type="checkbox" name="inclusive" label="Default Group?" value=1 checked=$record->inclusive}
        {control type="buttongroup" submit="Submit" cancel="Cancel"}
    {/form}
</div>
