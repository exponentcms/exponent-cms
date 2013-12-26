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

 <div class="form_header">
    {if $record->id != ""}
        <h1>{'Editing Poll Question'|gettext}</h1>
    {else}
        <h1>{'New Poll Question'|gettext}</h1>
    {/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=is_active value=$record->is_active|default:1}
        {control type=html name=question label="Question"|gettext value=$record->question}
        {control type="checkbox" name="open_results" label="Results are Publicly Viewable"|gettext|cat:"?" checked=$record->open_results|default:1 value=1}
        {control type="checkbox" name="open_voting" label="Open Voting"|gettext|cat:"?" checked=$record->open_voting|default:1 value=1}
        {control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
