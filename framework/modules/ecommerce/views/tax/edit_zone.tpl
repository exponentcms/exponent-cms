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

<h1>{"Edit Tax Zone"|gettext}</h1>
{form action=update_zone}
    {control type="hidden" name="id" value=$zone->id}
    {control type="text" name="name" label="Zone Name"|gettext value=$zone->name focus=1}
    {*{control type=state name=state label="State/Province"|gettext default=$zone->state}*}
    {*{control type=country name=country label="Country"|gettext default=$zone->country}*}
    {control type="countryregion" name=address label="Country/State"|gettext country_default=$zone->country|default:223 region_default=$zone->state includeblank="-- Choose a State --"|gettext}
    <div class="module-actions">
        {icon controller="address" action=manage text="Manage Locations"|gettext}
    </div>
    {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
{/form}
