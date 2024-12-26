{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{uniqueid prepend=$form->sef_url assign="name"}
{if !$error}
    {if $config.style && !bs3() && !bs4() && !bs5()}
        {css unique="formmod2" corecss="forms2col"}

        {/css}
    {else}
        {css unique="formmod2"}
        {literal}
            .stepy-step label {
            	margin   : 0;
                display  : inline;
            }
        {/literal}
        {/css}
    {/if}
    {if bs3() || bs4() || bs5()}
        {css unique="formmod"}
        {literal}
            @media (max-width: 544px) {
                .stepy-header li span {
                    display: none;
                }
            }
        {/literal}
        {/css}
    {/if}

    <div class="module forms edit enter-data">
        {messagequeue name='notice'}
        {if $edit_mode}
            <{$config.heading_level|default:'h1'}>{'Edit Form Record'|gettext}</{$config.heading_level|default:'h1'}>
        {else}
            {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}
                <{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
            {if $config.moduledescription != ""}
                {$config.moduledescription}
            {/if}
        {/if}
        {permissions}
            <div class="module-actions">
                {if $permissions.viewdata && $form->is_saved}
                    {icon class="view" action=showall id=$form->id text='View Records'|gettext|cat:" (`$count`)" title='View all records'|gettext}
                    {if !bs()}
                        &#160;&#160;|&#160;&#160;
                    {/if}
                    {icon class="downloadfile" action=export_csv id=$form->id text="Export as CSV"|gettext}
                    {if $permissions.manage && !bs()}
                        &#160;&#160;|&#160;&#160;
                    {/if}
                {/if}
                {if $permissions.manage}
                    {if !empty($form->id)}
                        {icon class=configure action=design_form id=$form->id text="Design Form"|gettext}
                        {if !bs()}
                            &#160;&#160;|&#160;&#160;
                        {/if}
                    {/if}
                    {icon action=manage select=true text="Manage Forms"|gettext}
                {/if}
            </div>
        {/permissions}
        <div class="bodycopy">
            {if empty($form) && $permissions.configure}
                {permissions}
                    <div class="module-actions">
                        <div class="msg-queue notice" style="text-align:center">
                            <p>{'You MUST assign a form to use this module!'|gettext} {icon action="manage" select=true}</p>
                        </div>
                    </div>
                {/permissions}
            {else}
                {if $description != ""}
                    {$description}
                {/if}
                {$form_html}
            {/if}
        </div>
    </div>
    {clear}
{/if}
