{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

<div class="module help showall">
    {help text="Get Help"}
    
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}

    <h2>Help Documentation for Version {$current_version->version}</h2>
    <ol>
    {foreach from=$page->records item=doc name=docs}
        <li>
            <a href={link action=show version=$doc->help_version->version title=$doc->title}>{$doc->title}</a>
        
            {permissions level=$smarty.const.UILEVEL_NORMAL}
                {if $permissions.edit == 1}
                    {icon action=edit img=edit.png class="editlink" id=$doc->id title="Edit this `$modelname`"}
                {/if}
                {if $permissions.delete == 1}
                    {icon action=delete img=delete.png id=$doc->id title="Delete this `$modelname`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
                {/if}
            {/permissions}
        </li>
        {clear}
    {/foreach}
    </ol>
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.create == 1}
            {icon class=add action=edit title="Add help doc" text="Add help doc to version `$current_version->version`"}{br}
        {/if}
        {if $permissions.manage == 1}
            {icon class=add action=manage title="Manage Help" text="Manage Help"}{br}
            {icon class=add action=manage_versions title="Manage Versions" text="Manage Versions"}{br}
        {/if}
    {/permissions}
</div>
