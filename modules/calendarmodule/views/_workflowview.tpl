{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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
<h3>{$item->title}</h3>
{if $item->is_allday}{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT}
{else}{$item->eventstart|format_date:"%b %e, $Y, %l:%M %P"} - {$item->eventend|format_date:"%l:%M %P"}
{/if}<br />
<hr size="1" />
{$item->body}