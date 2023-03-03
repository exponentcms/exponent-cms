{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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
    {if $record->id != ""}<h1>{'Editing'|gettext} '{$record->title}'</h1>{else}<h1>{'New'|gettext} {$model_name|capitalize}</h1>{/if}
    <div class="form_header">
        {'Enter the information about the calendar event (the date and times) below.'|gettext}{br}{br}
        {'Note: multiple day events are not supported.'|gettext}
    </div>
    {form action=update}
	    {control type=hidden name=id value=$record->id}
        {control type=hidden name=date_id value=$record->eventdate[0]->id}
        <div id="editevent-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation"><a href="#tab1" class="nav-link active" role="tab" data-bs-toggle="tab"><em>{'Event'|gettext}</em></a></li>
                <li class="nav-item" role="presentation"><a href="#tab2" class="nav-link" role="tab" data-bs-toggle="tab"><em>{'Date'|gettext}</em></a></li>
                {if $config.enable_feedback}
                    <li class="nav-item" role="presentation"><a href="#tab3" class="nav-link" role="tab" data-bs-toggle="tab"><em>{'Feedback'|gettext}</em></a></li>
                {/if}
                {if $config.enable_images}
                    <li class="nav-item" role="presentation"><a href="#tab4" class="nav-link" role="tab" data-bs-toggle="tab"><em>{'Images'|gettext}</em></a></li>
                {/if}
            </ul>
            <div class="tab-content yui3-skin-sam">
                <div id="tab1" role="tabpanel" class="tab-pane fade show active">
                    <h2>{'Event Entry'|gettext}</h2>
                    {control type=text name=title label="Title"|gettext value=$record->title focus=1}
                	{control type="editor" name="body" label="Event Details"|gettext value=$record->body}
                	{control type="checkbox" name="is_featured" label="Feature this Event?"|gettext value=1 checked=$record->is_featured}
                    {control type="checkbox" name="is_cancelled" label="Cancel this Event?"|gettext value=1 checked=$record->is_cancelled}
                    {if !$config.disabletags}
                        {control type="tags" value=$record}
                    {/if}
                    {if $config.usecategories}
                        {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='`$model_name`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
                    {/if}
                	{if $config.enable_ealerts}
                	    {control type="checkbox" name="send_ealerts" label="Send E-Alert?"|gettext value=1}
                	{/if}
                    {if $config.enable_auto_status}
                        {control type="checkbox" name="send_status" label="Post as Facebook Event?"|gettext value=1}
                    {/if}
                    {if $config.enable_auto_tweet}
                        {control type="checkbox" name="send_tweet" label="Post as a Tweet?"|gettext value=1}
                    {/if}
                </div>
                <div id="tab2" role="tabpanel" class="tab-pane fade">
                    <h2>{'Event Date'|gettext}</h2>
                    {control type="yuicalendarcontrol" name="eventdate" label="Event Date"|gettext value=$record->eventdate[$event_key]->date showtime=false}
                    {$jsHooks = ['onclick'=>'exponent_forms_disable_datetime(\'eventstart\',this.form,this.checked); exponent_forms_disable_datetime(\'eventend\',this.form,this.checked);']}
                  	{control type="checkbox" name="is_allday" label="All Day Event?"|gettext value=1 checked=$record->is_allday hooks=$jsHooks}
                    {control type="datetimecontrol" name="eventstart" label="Start Time"|gettext showdate=false value=$record->eventstart+$record->eventdate[0]->date disabled=$record->is_allday}
                    {control type="datetimecontrol" name="eventend" label="End Time"|gettext showdate=false value=$record->eventend+$record->eventdate[0]->date disabled=$record->is_allday}
                    {if $record->id}
                        {icon class="add" action=add_recurring id=$record->id text='Add more Dates'|gettext}
                    {/if}
                    {if (empty($record->id)) }
                        {exp_include file="_recurring.tpl"}
                    {elseif ($record->is_recurring == 1) }
                        {$dates=$record->eventdate}
                        {control type=hidden name=is_recurring value=$record->is_recurring}
                        {exp_include file='_recur_dates.tpl'}
                    {/if}
                </div>
                {if $config.enable_feedback}
                    <div id="tab3" role="tabpanel" class="tab-pane fade">
                        <h2>{'Event Feedback'|gettext}</h2>
                        {control type=dropdown name=feedback_form label="Feedback Form"|gettext items=$allforms items=$allforms value=$record->feedback_form}
                        {*{control type=text name=feedback_email label="Feedback Email"|gettext value=$record->feedback_email}*}
                        {control type=email name=feedback_email label="Feedback Email"|gettext value=$record->feedback_email}
                    </div>
                {/if}
                {if $config.enable_images}
                    <div id="tab4" role="tabpanel" class="tab-pane fade">
                        <h2>{'Attach Files'|gettext}</h2>
                        {control type=files name=images label="Attached Images"|gettext accept="image/*" value=$record->expFile folder=$config.upload_folder}
                    </div>
                {/if}
            </div>
        </div>
        {loading title="Loading Event"|gettext}
        {control type=buttongroup submit="Save Event"|gettext cancel="Cancel"|gettext}
     {/form}
</div>
