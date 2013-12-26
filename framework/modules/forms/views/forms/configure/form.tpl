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

<div class="form_header">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Form Settings"|gettext) module="form-settings"}
        </div>
        <h2>{"Form Settings"|gettext}</h2>
    </div>
</div>
{control type=text name='title' label='Assigned Form'|gettext value=$form_title disabled=true description='Forms are assigned using \'Manage Forms\''|gettext}
{control type=hidden name="forms_id" value=$config.forms_id}
{if $config.is_saved && !empty($config.table_name)}
    {control type=text name='table_name' label='Saved to Database'|gettext value=$config.table_name disabled=true}
{/if}
{control type="checkbox" name="restrict_enter" label="Restrict Form Entry by Using Permissions?"|gettext value=1 checked=$config.restrict_enter description='Enable this setting to only allow those users with permission to enter data'|gettext}
{control type=html name='description' label='Form Description'|gettext value=$config.description description='Placed below module description and above the form'|gettext}
{group label='Form Display Settings'|gettext}
    {control type=text name='submitbtn' label='Submit Button Text'|gettext value=$config.submitbtn|default:"Submit"|gettext}
    {control type=text name='resetbtn' label='Reset Button Text'|gettext value=$config.resetbtn|default:"Reset"|gettext}
    {control type=radiogroup name='style' label='Style'|gettext default=$config.style|default:0 items='Single Column, Two Column'|gettxtlist values='0,1'}
{/group}
