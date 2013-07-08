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

<div id="editevent" class="events calendar edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} '{$record->title}'</h1>{else}<h1>{'New'|gettext} {$modelname|capitalize}</h1>{/if}
    <div class="form_header">
        <blockquote>{'Enter the information about the calendar event (the date and times) below.'|gettext}</blockquote>
        <blockquote>{'Note: multiple day events are not supported.'|gettext}</blockquote>
    </div>
    {form action=update}
	    {control type=hidden name=id value=$record->id}
        {control type=hidden name=date_id value=$record->eventdate[0]->id}
        <div id="editevent-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'Event'|gettext}</em></a></li>
                <li><a href="#tab2"><em>{'Date'|gettext}</em></a></li>
                {if $config.enable_feedback}
                    <li><a href="#tab3"><em>{'Feedback'|gettext}</em></a></li>
                {/if}
                {if $config.enable_images}
                    <li><a href="#tab4"><em>{'Images'|gettext}</em></a></li>
                {/if}
            </ul>
            <div class="yui-content yui3-skin-sam">
                <div id="tab1">
                    {control type=text name=title label="Title"|gettext value=$record->title}
                	{control type="editor" name="body" label="Event Details"|gettext value=$record->body}
                	{control type="checkbox" name="is_featured" label="Feature this Event?"|gettext value=1 checked=$record->is_featured}
                    {control type="checkbox" name="is_cancelled" label="Cancel this Event?"|gettext value=1 checked=$record->is_cancelled}
                    {if !$config.disabletags}
                        {control type="tags" value=$record}
                    {/if}
                    {if $config.usecategories}
                        {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='`$modelname`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
                    {/if}
                	{if $config.enable_ealerts}
                	    {control type="checkbox" name="send_ealerts" label="Send E-Alert?"|gettext value=1}
                	{/if}
                </div>
                <div id="tab2">
                    {control type="yuicalendarcontrol" name="eventdate" label="Event Date"|gettext value=$record->eventdate[0]->date}
                    {*{control type="calendar" name="eventdate" label="Event Date"|gettext default_date=$record->eventdate[0]->date}*}
                    {$jsHooks = ['onclick'=>'exponent_forms_disable_datetime(\'eventstart\',this.form,this.checked); exponent_forms_disable_datetime(\'eventend\',this.form,this.checked);']}
                  	{control type="checkbox" name="is_allday" label="All Day Event?"|gettext value=1 checked=$record->is_allday hooks=$jsHooks}
                    {control type="datetimecontrol" name="eventstart" label="Start Time"|gettext showdate=false value=$record->eventstart+$record->eventdate[0]->date disabled=$record->is_allday}
                    {control type="datetimecontrol" name="eventend" label="End Time"|gettext showdate=false value=$record->eventend+$record->eventdate[0]->date disabled=$record->is_allday}
                    {if (empty($record->id)) }
                        {include "_recurring.tpl"}
                    {elseif ($record->is_recurring == 1) }
                        {$dates=$record->eventdate}
                        {control type=hidden name=is_recurring value=$record->is_recurring}
                        {'This event is a recurring event, and occurs on the dates below.  Select which dates you wish to apply these edits to.'|gettext}
                        <table cellspacing="0" cellpadding="2" width="100%" class="exp-skin-table">
                            {include '_recur_dates.tpl'}
                        </table>
                    {/if}
                </div>
                {if $config.enable_feedback}
                    <div id="tab3">
                        {control type=dropdown name=feedback_form label="Feedback Form"|gettext items=$allforms items=$allforms value=$record->feedback_form}
                        {*{control type=text name=feedback_email label="Feedback Email"|gettext value=$record->feedback_email}*}
                        {control type=email name=feedback_email label="Feedback Email"|gettext value=$record->feedback_email}
                    </div>
                {/if}
                {if $config.enable_images}
                    <div id="tab4">
                        {control type=files name=images label="Attached Images"|gettext accept="image/*" value=$record->expFile}
                    </div>
                {/if}
            </div>
        </div>
	    <div class="loadingdiv">{"Loading Event"|gettext}</div>
        {control type=buttongroup submit="Save Event"|gettext cancel="Cancel"|gettext}
     {/form}
</div>

{script unique="editform" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#editevent-tabs'});
		Y.one('#editevent-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
