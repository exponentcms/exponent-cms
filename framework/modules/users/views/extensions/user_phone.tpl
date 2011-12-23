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

<div class="module users extension phone">
    {control type="text" name="home_phone" label="Home Phone"|gettext value=$edit_user->home_phone}
    {control type="text" name="bus_phone" label="Work Phone"|gettext value=$edit_user->bus_phone}
    {control type="text" name="other_phone" label="Other Phone"|gettext value=$edit_user->other_phone}
    {control type="text" name="pref_contact" label="Preferred Contact Method"|gettext value=$edit_user->pref_contact}
    {control type="text" name="contact_time" label="Preferred Contact Time"|gettext value=$edit_user->contact_time}
</div>


