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

<div class="form_header">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Form Report Settings"|gettext) module="form-report-settings"}
        </div>
        <h2>{"Form Report Settings"|gettext}</h2>
    </div>
</div>
{control type="checkbox" name="unrestrict_view" label="Enable Data Viewing by Users without Permissions?"|gettext value=1 checked=$config.unrestrict_view description='Enable this setting to allow everyone to view data'|gettext}
{control type=text name='report_name' label='Report Title'|gettext value=$config.report_name}
{control type=html name='report_desc' label='Report Description'|gettext value=$config.report_desc}
{control type=text name='report_filter' label='Report Filter'|gettext value=$config.report_filter description='SQL Statement to limit records returned. Empty returns all records.'|gettext}
{control type="dropdown" name="order" label="Sort records by"|gettext items=$fields value=$config.order|default:'id'}
{control type=radiogroup name='dir' label='Sorting Direction'|gettext default=$config.dir|default:'ASC' items='Ascending, Descending'|gettxtlist values='ASC,DESC'}
{group label='Multi-Record Tabular View Configuration'|gettext}
    {control type="listbuilder" name="column_names_list" label="Columns for Display and Export CSV" values=$column_names source=$fields description='Selecting NO columns is equal to selecting first five columns'|gettext}
    {control type="checkbox" name="export_all" label="Export ALL Columns in CSV?"|gettext value=1 checked=$config.export_all}
{/group}
{group label='Custom Single Record View Configuration'|gettext}
    {if !empty($fieldlist)}
        {$addconf = "fieldinsert_list : $fieldlist"}
    {else}
        {$addconf = "fieldinsert_list : ''"}
    {/if}
    {control type=editor name='report_def' label='Custom E-Mail and Single Item View Template'|gettext value=$config.report_def rows=10 cols=60
        plugin="fieldinsert" additionalConfig=$addconf
        description='Leave blank to display all fields.  Use \'Fields\' dropdown to insert fields'}
{/group}