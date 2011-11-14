{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
    <h1>Import Data</h1>    
    {form action=import}        
        {control type="dropdown" name="import_type" label="Select Data to Import" items=$importDD default="storeController"}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
    {br}
    {*
    <h1>Export Data</h1>    
    {form action=export}
        {control type="dropdown" name="export_type" label="Select Data to Export" items=$exportDD}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
    *}
</div>
