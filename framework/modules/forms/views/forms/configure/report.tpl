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
            {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Form Report Settings"|gettext) module="form-report-settings"}
        </div>
        <h2>{"Form Report Settings"|gettext}</h2>
    </div>
</div>
{control type="checkbox" name="unrestrict_view" label="Enable Data Viewing by Users without Permissions?"|gettext value=1 checked=$config.unrestrict_view description='Enable this setting to allow everyone to view data.'|gettext}
{control type=text name='report_name' label='Report Title'|gettext value=$config.report_name}
{control type=html name='report_desc' label='Report Description'|gettext value=$config.report_desc}
{group label='Multi-Record View Configuration'|gettext}
    {control type="listbuilder" name="column_names_list" label="Columns for View Data/Export CSV" values=$column_names source=$fields description='Selecting NO columns is equal to selecting first five columns'|gettext}
{/group}
{group label='Single-Record View Configuration'|gettext}
    {control type=html name='report_def' label='Custom E-Mail Report and View Record Definition'|gettext value=$config.report_def description='Leave this custom definition blank to use the default \'all fields\' e-mail report and record view.'|gettext}
{/group}