{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="module migration manage-pages">
    <h1>Migrate Files</h1>
    <p> 
        The following is a list of files found in the database ({$config.database}). 
        Note: this will only copy over the records from the old database properly into the Exponent 2.0 database. Make sure you manually copy the "files" 
        directory over to this installation.
        <span class="warning">
            WARNING: This process will wipe out all current file records in the database.
        </span>
    </p>
    
    {form action="migrate_files"}
        <table class="exp-skin-table">
        <thead>
            <tr>
                <th width="30%">File Name</th>
                <th width="70%">Directory</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$files item=file name=files}
        <tr class="{cycle values="even,odd"}">            
            <td width="30%">
                {$file->filename}
            </td>
            <td width="70%">
                {$file->directory}
            </td>
        </tr>
        {foreachelse}
                <tr><td colspan=2>No files found in the database {$config.database}</td></tr>
        {/foreach}
        </tbody>
        </table>
        {control type="buttongroup" submit="Migrate Files" cancel="Cancel"}
    {/form}
</div>