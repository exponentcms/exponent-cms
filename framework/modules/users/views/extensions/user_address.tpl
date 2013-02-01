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

<div class="module users extension address">
    {control type="text" name="address1" label="Address"|gettext value=$edit_user->address1}
    {control type="text" name="address2" label="Address2"|gettext value=$edit_user->address2}
    {control type="text" name="city" label="City"|gettext value=$edit_user->city}
    {control type="text" name="state" label="State"|gettext value=$edit_user->state}
    {control type="text" name="zip" label="Zip Code"|gettext value=$edit_user->zip}
    {control type="text" name="country" label="Country"|gettext value=$edit_user->country}
</div>


