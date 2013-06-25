{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
    {if $config.style}
        {css unique="formmod2" corecss="forms2col"}

        {/css}
    {/if}
    <div class="module forms edit enter-data">
        {messagequeue name='notice'}
        {permissions}
            <div class="module-actions">
                {if $permissions.viewdata && $form->is_saved}
                    {icon class="view" action=showall id=$form->id text='View Data'|gettext|cat:" (`$count`)"}
                    &#160;&#160;|&#160;&#160;
                    {icon class="downloadfile" action=export_csv id=$form->id text="Export CSV"|gettext}
                    {if $permissions.manage}
                        &#160;&#160;|&#160;&#160;
                    {/if}
                {/if}
                {if $permissions.manage}
                    {if !empty($form->id)}
                        {icon class=configure action=design_form id=$form->id text="Design Form"|gettext}
                        &#160;&#160;|&#160;&#160;
                    {/if}
                    {icon action=manage text="Manage Forms"|gettext}
                {/if}
            </div>
        {/permissions}
        {if $edit_mode}
            <h1>{'Edit Form Record'|gettext}</h1>
        {else}
            {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}
                <h1>{$moduletitle}</h1>{/if}
            {if $config.moduledescription != ""}
                {$config.moduledescription}
            {/if}
        {/if}
        <div class="bodycopy">
            {if empty($form) && $permissions.configure}
                {permissions}
                    <div class="module-actions">
                        <div class="msg-queue notice" style="text-align:center">
                            <p>{'You MUST assign a form to use this module!'|gettext} {icon action="manage"}</p></div>
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
{/if}

{*{script unique=jWizard jquery='jqueryui,jquery.jWizard'}*}
    {*$("#test").jWizard();*}
{*{/script}*}

{*{script unique=quickWizard jquery='jqueryui,jquery.validate,jquery.quickWizard'}*}
    {*$(document).ready(function() {*}
        {*$('#test').quickWizard();*}
    {*});*}
{*{/script}*}

{*{if $paged}*}
{*{script unique=$name jquery='jquery.validate,jquery.stepy'}*}
{*{literal}*}
    {*$("#{/literal}{$form->sef_url}{literal}").stepy({*}
        {*validate: true,*}
        {*block: true,*}
        {*errorImage: true,*}
{*//        finishButton: false,  // otherwise crashes because we can't find submit button*}
        {*btnClass: 'awesome {/literal}{$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}{literal}',*}
{*//        description: false,*}
{*//        legend: false,*}
        {*titleClick: true,*}
    {*});*}
{*{/literal}*}
{*{/script}*}
{*{/if}*}
