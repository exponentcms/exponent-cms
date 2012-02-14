{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
 
<div class="module importexport manage">
    <h1>{'Upload Your CSV File of External Addresses (MilitaryClothing.com, NameTapes.com, or Amazon)'|gettext}</h1>
    {'This will clear any existing external addresses for this source and replace with the addresses you upload.'|gettext}
    {form action=process_external_addresses}
        <input type="file" name="address_csv" size="50">
        {control type="dropdown" name="type_of_address" label="Select the source of the CSV file:"|gettext size=4 multiple=false items=$sources default=-1}
        {control type="buttongroup" submit="Import Addresses"|gettext cancel="Cancel"|gettext}
    {/form}
    {br}
</div>
