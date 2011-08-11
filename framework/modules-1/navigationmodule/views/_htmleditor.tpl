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
DEPRECATED
{*
<div style="margin-bottom: 5px; border-bottom: 1px solid darkblue">
{'Pick a section to see the images that have been uploaded for use.'|gettext}
</div>

<table cellpadding="1" cellspacing="0" border="0" width="100%">
{foreach from=$sections item=section}
<tr><td style="padding-left: {math equation="x*10" x=$section->depth}px">
<a href="imgr_display.php?section={$section->id}" class="mngmntlink">{$section->name}</a>&nbsp;
</td></tr>
{/foreach}
</table>
*}