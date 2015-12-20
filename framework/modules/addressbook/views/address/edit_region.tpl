{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="module address edit edit-region">
    {if $record->id != ""}
        <h1>{'Editing region'|gettext}</h1>
    {else}
        <h1>{'New region'|gettext}</h1>
    {/if}
    {form action=update_region}
        {control type="hidden" name="id" value=$record->id}
        {control type="hidden" name="rank" value=$record->rank}
        {control type="text" name="name" label="Region Name"|gettext value=$record->name focus=1}
        {control type="text" name="code" label="Code"|gettext value=$record->code}
        {control type=country name="country_id" label="Country"|gettext default=$record->country_id show_all=1}
        {control type="checkbox" name="active" label="Active"|gettext value=1 checked=$record->active}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
