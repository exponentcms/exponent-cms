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
 
<div class="module importexport import-product">
    <h1>{"Upload Your"|gettext} {$type->basemodel_name|capitalize} {"File to Import"|gettext}</h1>
    <blockquote>
        {'This CSV file can be created using the Exponent, Super-Admin Tools, Database, Import/Export Data menu item and then selecting Export Data, Store Category manager'|gettext}{br}
        {icon class=export controller=storeCategory action=export }{br}
    </blockquote>
    {form action=importCategory}
        {control type="hidden" name="import_type" value=$type->baseclassname}
        {control type=uploader name=import_file size="50"}
        {control type="buttongroup" submit="Import"|gettext|cat:"!" cancel="Cancel"|gettext}
    {/form}
    {br}
</div>
