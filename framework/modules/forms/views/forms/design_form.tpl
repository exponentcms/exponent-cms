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

{css unique="design-form" corecss="button"}

{/css}

{if $config.style}
    {css unique="formmod2" corecss="forms2col"}

    {/css}
{/if}

<div class="module forms design-form">
    <div class="form_title">
        <h1>{'Form Editor'|gettext}</h1>
        {if $edit_mode != 1}
            {'Use the drop down to add fields to this form.'|gettext}
            <div class="module-actions">
                {ddrerank module="forms_control" where="forms_id=`$form->id`" sortfield="caption" label="Form Controls"|gettext}
            </div>
        {/if}
    </div>
    {if $edit_mode != 1}
    <div style="border: 2px dashed lightgrey; padding: 1em;">
        {/if}
        {$form_html}
        {if $edit_mode != 1}
    </div>
    {/if}
    {if $edit_mode != 1}
        <table cellpadding="5" cellspacing="0" border="0">
            <tr>
                <td style="border:none;">
                    <form method="post" action="{$smarty.const.PATH_RELATIVE}index.php">
                        <input type="hidden" name="controller" value="forms"/>
                        <input type="hidden" name="action" value="edit_control"/>
                        <input type="hidden" name="forms_id" value="{$form->id}"/>
                        {'Add a'|gettext} <select name="control_type" onchange="this.form.submit()">
                            {foreach from=$types key=value item=caption}
                                <option value="{$value}">{$caption}</option>
                            {/foreach}
                        </select>
                    </form>
                </td>
            </tr>
        </table>
    {*<p><a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}"*}
    {*href="JavaScript: pickSource();">{'Append fields from existing form'|gettext}</a></p>*}

    {*{script unique="viewform"}*}
    {*function pickSource() {ldelim}*}
    {*window.open('{$pickerurl}','sourcePicker','title=no,toolbar=no,width=800,height=600,scrollbars=yes');*}
    {*{rdelim}*}
    {*{/script}*}
    {*{if !empty($forms_list)}{control type="dropdown" name="forms_id" label="Append fields from an existing form"|gettext items=$forms_list}{/if}*}
        <p>
            <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}"
               href="{$backlink}">{'Done'|gettext}</a>
        </p>
    {/if}
</div>