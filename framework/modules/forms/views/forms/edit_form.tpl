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

<div id="editform" class="module forms edit edit-form">
    <h1>
        {if $form->id != ""}
            {'Editing'|gettext} '{$form->title}'
        {else}
            {'New'|gettext}
        {/if}
        {'Form'|gettext}
    </h1>
    {form action=update_form}
    {control type=hidden name=id value=$form->id}
    {control type=hidden name=old_id value=$form->old_id}
    <div id="editform-tabs" class="yui-navset exp-skin-tabview hide">
        <ul class="yui-nav">
            <li class="selected"><a href="#tab1"><em>{'Form'|gettext}</em></a></li>
            <li><a href="#tab2"><em>{'Default Report'|gettext}</em></a></li>
        </ul>
        <div class="yui-content yui3-skin-sam">
            <div id="tab1">
                <div class="form_header">
                    <div class="info-header">
                        <div class="related-actions">
                            {help text="Get Help with"|gettext|cat:" "|cat:("Form Settings"|gettext) module="form-settings"}
                        </div>
                        <h2>{"Form Settings"|gettext}</h2>
                    </div>
                </div>
                {control type=text name='title' label='Form Name'|gettext value=$form->title required=true}
                {control type="text" name="sef_url" label="SEF URL"|gettext value=$form->sef_url description='If you don\'t put in an SEF URL one will be generated based on the title provided. SEF URLs can only contain alpha-numeric characters, hyphens, forward slashes, and underscores.'|gettext}
                {control type=html name='description' label='Form Description'|gettext value=$form->description description='Placed below module description and above the form'|gettext}
                {control type=html name='response' label='Submission Response Display'|gettext value=$form->response description='Message to display on the site after submitting a form'|gettext}
                {group label='Form Database Settings'|gettext}
                    {if $form->is_saved}
                        {control type="checkbox" name="is_saved" label="Save Form Submissions to the Database?"|gettext value=1 checked=$form->is_saved disabled=true}
                        {control type=hidden name=is_saved value=$form->is_saved}
                    {else}
                        {control type="checkbox" name="is_saved" label="Save Form Submissions to the Database?"|gettext value=1 checked=$form->is_saved description='Forms not saved to the database, are required to send an email on submission'|gettext}
                    {/if}
                    {if !empty($form->table_name)}
                        {control type=text name='table_name' label='Table Name'|gettext value=$form->table_name disabled=true}
                        {control type=hidden name='table_name' value=$form->table_name}
                    {/if}
                    <blockquote>
                        {'To help prevent data loss, you cannot remove a form\'s database table once it has been added.'|gettext}
                    </blockquote>
                {/group}
            </div>
            <div id="tab2">
                <div class="form_header">
                    <div class="info-header">
                        <div class="related-actions">
                            {help text="Get Help with"|gettext|cat:" "|cat:("Form Report Settings"|gettext) module="form-report-settings"}
                        </div>
                        <h2>{"Default Report Settings"|gettext}</h2>
                        <blockquote>
                            {'Report settings to use in the absence of a configured report view.'|gettext}
                        </blockquote>
                    </div>
                    {control type=text name='report_name' label='Report Title'|gettext value=$form->report_name}
                    {control type=html name='report_desc' label='Report Description'|gettext value=$form->report_desc}
                    {group label='Multi-Record Tabular View Configuration'|gettext}
                        {control type="listbuilder" name="column_names_list" label="Columns for Export CSV" values=$column_names source=$fields description='Selecting NO columns is equal to selecting first five columns'|gettext}
                    {/group}
                    {group label='Custom View Configuration'|gettext}
                    {control type=editor name='report_def' label='Custom E-Mail, Single and Portfolio View Template'|gettext value=$config.report_def rows=10 cols=60
                        plugin="fieldinsert" additionalConfig="fieldinsert_list : `$fieldlist`,"
                        description='Leave blank to display all fields.  Use \'Fields\' dropdown to insert fields'}
                    {/group}
                </div>
            </div>
        </div>
        <div class="loadingdiv">{'Loading Form'|gettext}</div>
        {control type=buttongroup submit="Save Form"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>

{script unique="editform" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

    YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#editform-tabs'});
        Y.one('#editform-tabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
