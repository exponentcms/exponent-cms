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

<div class="module address edit edit-country">
    {if $record->id != ""}
        <h1>{'Editing country'|gettext}</h1>
    {else}
        <h1>{'New country'|gettext}</h1>
    {/if}
    {form action=update_country}
        {control type="hidden" name="id" value=$record->id}
        {control type="hidden" name="is_default" value=$record->is_default}
        {control type="text" name="name" label="Country Name"|gettext value=$record->name focus=1}
        {control type="text" name="iso_code_2letter" label="ISO Code - 2 Letter"|gettext value=$record->iso_code_2letter size=2}
        {control type="text" name="iso_code_3letter" label="ISO Code - 3 Letter"|gettext value=$record->iso_code_3letter size=3}
        {control type="text" name="iso_code_number" label="ISO Code - Number"|gettext value=$record->iso_code_number size=8 filter=integer}
        {control type="checkbox" name="active" label="Active"|gettext value=1 checked=$record->active}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
