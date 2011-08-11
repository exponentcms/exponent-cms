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
<div class="form_title">{if $is_edit == 1}{'Edit Existing Internal Alias'|gettext}{else}{'New Internal Alias'|gettext}{/if}</div>
<div class="form_header">{'Select which internal page you want this section to link to.  If you link to another internal alias, the aliases will all be dereferenced, and the original destination used.  If you link to an external alias, then this section will point to the external aliases external web address.'|gettext}</div>
{$form_html}