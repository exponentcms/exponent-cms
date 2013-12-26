{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="module motd edit">
    <{$config.heading_level|default:'h1'}>{if $record->id == ''}{'New Message of the Day'|gettext}{else}{'Edit Message of the Day'|gettext}{/if}</{$config.heading_level|default:'h1'}>
    <p>{$record->body}</p>
    
    {form action='update'}
        {control type="hidden" name="id" value=$record->id}
        {*{control type="text" name="body" label="Message"|gettext size=35 value=$record->body}*}
        {control type="html" name="body" label="Message"|gettext value=$record->body}
        {control type="dropdown" name="month" label="Month"|gettext items=$record->months value=$record->month}
        {control type="dropdown" name="day" label="Day"|gettext from=1 to=31 value=$record->day}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
