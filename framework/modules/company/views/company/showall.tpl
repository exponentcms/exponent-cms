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

{css unique="showallcompany" corecss="tables"}

{/css}

<div class="company showall">
	<{$config.heading_level|default:'h1'}>{$moduletitle|default:'Product Manufacturer Listings'|gettext}</{$config.heading_level|default:'h1'}>
    {if $permissions.create}
        {icon class=add controller=$controller action=edit text="Add a new"|gettext|cat:" `$model_name`"}
    {/if}
    <table class="exp-skin-table">
        <thead>
            {$page->header_columns}
            <th></th>
            {permissions}
            <th>{'Actions'|gettext}</th>
            {/permissions}
        </thead>
        <tbody>
            {foreach from=$page->records item=company name=companies}
            <tr class="{cycle values="odd,even"}">
                <td><a href="{link controller=company action=show id=$company->id}" title="{'View all products offered from this manufacturer'|gettext}">{$company->title}</a></td>
                <td><a href="{$company->website}" title="{'Visit the manufacturer\'s web site'|gettext}" target="_blank">{$company->website}</a></td>
                <td width=64px>
                    {if $company->expFile.logo[0]->id}
                        {img file_id=$company->expFile.logo[0]->id w=64 alt="Image of `$company->title`" title="`$company->title`" id="enlarged-image"}
                    {/if}
                </td>
                {permissions}
                    <td>
                        <div class="item-actions">
                            {if $permissions.edit || ($permissions.create && $company->poster == $user->id)}
                                {icon action=edit record=$company title="Edit"|gettext|cat:" `$company->title`"}
                            {/if}
                            {if $permissions.delete || ($permissions.create && $company->poster == $user->id)}
                                {icon action=delete record=$company title="Delete"|gettext|cat:" `$company->title`"}
                            {/if}
                        </div>
                    </td>
                {/permissions}
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
