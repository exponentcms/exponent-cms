{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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
    {*<table>*}
        {foreach from=$causes item=cause}
            <div class="row">
                <div class="col-sm-3">{img class="img-fluid" file_id=$cause->expFile.mainimage[0]->id square=120}</div>
                <div class="col-sm-6">
                    <h3>{$cause->title}</h3>
                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit || ($permissions.create && $cause->poster == $user->id)}
                                {icon controller=store action=edit record=$cause title="Edit Donation"|gettext}
                            {/if}
                            {if $permissions.delete || ($permissions.create && $cause->poster == $user->id)}
                                {icon controller=store action=delete record=$cause title="Remove Donation"|gettext}
                            {/if}
                        </div>
                    {/permissions}
                    {$cause->body}
                </div>
                <div class="col-sm-3">
                    <a class="add-to-cart-btn {button_style color=blue size=large}" href={link controller=cart action=addItem product_type=$cause->product_type product_id=$cause->id quick=$quickadd}>{expCore::getCurrencySymbol()}&#160;{'Donate'|gettext} {if $config.quickadd}{$cause->base_price|currency}{/if}</a>
                </div>
             </div>
        {foreachelse}
            {permissions}
                {if $permissions.create}
                    {message class=notice text="No causes have been setup for donations."|gettext}
                {/if}
            {/permissions}
        {/foreach}
    {*</table>*}
</div>
