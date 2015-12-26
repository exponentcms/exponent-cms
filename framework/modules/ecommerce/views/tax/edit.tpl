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

<h1>{"Edit Tax Rate"|gettext}</h1>
{form action=update}
    {control type="hidden" name="id" value=$record->id}
    {*{control type="text" name="name" label="Class Name"|gettext value=$record->classname focus=1}*}
    {control type="dropdown" name="class" label="Class"|gettext items=$classes value=$record->class_id}
    <div class="module-actions">
        {icon action=edit_class class="add" text="Add a Tax Class"|gettext}
    </div>
    {control type="dropdown" name="zone" label="Zone"|gettext items=$zones value=$record->zone}
    <div class="module-actions">
        {icon action=edit_zone class="add" text="Add a Tax Zone"|gettext}
    </div>
    {*{control type=state name=state label="State/Province"|gettext default=$record->state}*}
    {*{control type=country name=country label="Country"|gettext default=$record->country}*}
    {*{control type="countryregion" name=address label="Country/State"|gettext country_default=$record->country|default:223 region_default=$record->state includeblank="-- Choose a State --"|gettext}*}
    {control type="text" name="rate" label="Percentage Rate"|gettext value=$record->rate}
    {control type="checkbox" name="shipping_taxed" label="Shipping cost is taxable"|gettext value=1 checked=$record->shipping_taxed}
    {control type="checkbox" name="origin_tax" label="Tax is based on origin (store address)"|gettext value=1 checked=$record->origin_tax}
    {control type="checkbox" name="inactive" label="Disable this tax rate"|gettext value=1 checked=$record->inactive}
    {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
{/form}
