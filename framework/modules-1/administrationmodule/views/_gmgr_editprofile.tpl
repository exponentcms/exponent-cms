{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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
<div class="form_title">{if $is_edit}{'Edit Group Account'|gettext}{else}{'Create Group Account'|gettext}{/if}</div>
<div class="form_header">{'If you check the "Default?" checkbox, user accounts created after this group is saved will be added to it.  This will not retro-actively add existing users to this group.'|gettext}</div>
{$form_html}