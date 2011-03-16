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

<div class="module donation showall">
    {if $moduletitle != ''}<h1>{$moduletitle}</h1>{/if}
    
    <table>
    {foreach from=$causes item=cause}
        <tr>
            <td>{img file_id=$cause->expFile.images[0]->id square=120}</td>
            <td>
                <h3>{$cause->title}</h3>
                {$cause->body}
            </td>
            <td>
                <a href="{link controller=cart action=addItem quick=1 product_type=$cause->product_type product_id=$cause->id}">Donate Now</a>                
            </td>
            <td>
                {permissions}
                    {if $permissions.edit == 1}
                        {icon img=edit.png controller=store action=edit id=$cause->id title="Edit Donation"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon img=delete.png controller=store action=delete id=$cause->id title="Remove Donation"}
                    {/if}
                {/permissions}
            </td>
         </tr>
    {foreachelse}
        <h2>No causes have been setup to donate to.</h2>
    {/foreach}
    </table>
    {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
        {if $permissions.edit == 1 or $permissions.administrate == 1}
        <div id="prod-admin">
            <a href="{link controller=store action=edit id=0 product_type=donation}">Add a new donation cause</a>
        </div>
    {/if}
    {/permissions}
</div>
