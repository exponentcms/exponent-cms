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

<div class="form_header">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Form Settings"|gettext) module="form-settings"}
        </div>
        <h2>{"Form Settings"|gettext}</h2>
    </div>
</div>
{*{control type=text name='forms_id' label='Select Form'|gettext value=$config.forms_id required=true}*}
{control type="dropdown" name="forms_id" label="Select the Form Assigned to this Module"|gettext items=$forms_list default=$config.forms_id required=true}
{control type=text name='title' label='Form Name'|gettext value=$config.name}
{control type="checkbox" name="restrict_enter" label="Restrict Form Entry by Using Permissions?"|gettext value=1 checked=$config.restrict_enter description='Enable this setting to only allow those users with permission to enter data.'|gettext}
{control type=html name='description' label='Form Description'|gettext value=$config.description}
{control type=html name='response' label='Response after submission'|gettext value=$config.response description='Message to display on site after submitting a form.'|gettext}
{group label='Form Display Settings'|gettext}
{control type=text name='submitbtn' label='Submit Button Text'|gettext value=$config.submitbtn|default:"Submit"|gettext}
{control type=text name='resetbtn' label='Reset Button Text'|gettext value=$config.resetbtn|default:"Reset"|gettext}
{control type=radiogroup name='style' label='Style'|gettext default=$config.style|default:0 items='Single Column, Two Column'|gettxtlist values='0,1'}
{/group}
