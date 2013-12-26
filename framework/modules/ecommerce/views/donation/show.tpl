{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        {if $permissions.edit || $permissions.manage}
            <div id="prod-admin">
                {icon class="add" controller=store action=edit id=0 product_type=donation text="Add a new donation cause"|gettext}
            </div>
        {/if}
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {if $config.quickadd}
        {$quickadd = '1'}
    {/if}
    <table>
        <tr>
            <td style="padding: 5px;">{img file_id=$product->expFile.mainimage[0]->id square=120}</td>
            <td style="padding: 5px;">
                <h3>{$product->title}</h3>
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $prot->poster == $user->id)}
                            {icon controller=store action=edit record=$product title="Edit Donation"|gettext}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $product->poster == $user->id)}
                            {icon controller=store action=delete record=$product title="Remove Donation"|gettext}
                        {/if}
                    </div>
                {/permissions}
                {$product->body}
            </td>
            <td style="padding: 5px;">
                <a class="add-to-cart-btn {button_style size=large}" href={link controller=cart action=addItem product_type=$product->product_type product_id=$product->id quick=$quickadd}>{'Donate'|gettext} {if $config.quickadd}{$product->base_price|currency}{/if}</a>
            </td>
         </tr>
    </table>
</div>
