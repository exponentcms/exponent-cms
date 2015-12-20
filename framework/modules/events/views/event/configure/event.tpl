{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
		    {help text="Get Help with"|gettext|cat:" "|cat:("Calendar Settings"|gettext) module="events"}
		</div>
        <h2>{"Calendar Settings"|gettext}</h2>
        <blockquote>
            {"This is where you can configure the settings used by this Events module."|gettext}&#160;&#160;
            {"These settings only apply to this particular module."|gettext}
        </blockquote>
	</div>
</div>
{control type="text" name="feed_title" label="Calendar Title"|gettext description="Used for iCal publishing and email reminders"|gettext value=$config.feed_title focus=1}
{control type="text" name="feed_sef_url" label="Calendar SEF URL"|gettext description="Auto-generated from title if left blank"|gettext value=$config.feed_sef_url}
{*control type=dropdown name=order label="Sort Order"|gettext items="$sortopts" value=$config.order*}
{*{control type=dropdown name=order label="Sort By"|gettext items="Date Added, Date Added Descending, Date Updated, Date Updated Descending, Date Published, Date Published Descending, Rank"|gettxtlist values="created_at,created_at DESC,edited_at,edited_at DESC,publish,publish DESC,rank" value=$config.order|default:'publish DESC'}*}
{control type="checkbox" name="only_featured" label="Only show Featured Events"|gettext value=1 checked=$config.only_featured}
{control type="checkbox" name="disable_links" label="Hide Links to other Views"|gettext description='Hide display of \'month\', \'list\' etc.. views'|gettext value=1 checked=$config.disable_links}
{control type="checkbox" name="printlink" label="Display Printer-Friendly and Export-to-PDF Links"|gettext description="Export-to-PDF feature requires optional pdf engine add-on"|gettext value=1 checked=$config.printlink}
{control type="checkbox" name="enable_feedback" label="Enable Event Feedback Option"|gettext value=1 checked=$config.enable_feedback}
{control type="checkbox" name="enable_images" label="Enable Attached Images"|gettext value=1 checked=$config.enable_images}
{if $smarty.const.SITE_FILE_MANAGER == 'elfinder'}
    {control type="text" name="upload_folder" label="Quick Add Upload Subfolder"|gettext value=$config.upload_folder}
{else}
    {control type=dropdown name="upload_folder" label="Select the Quick Add Upload Folder"|gettext items=$folders value=$config.upload_folder}
{/if}
{*{control type="checkbox" name="ajax_paging" label="Use ajax paging if available"|gettext value=1 checked=$config.ajax_paging description='Can decrease paging loading time, but may cause SEO issues'|gettext}*}
{if $smarty.const.ECOM}
{control type="checkbox" name="aggregate_registrations" label="Aggregate Event Registrations"|gettext value=1 checked=$config.aggregate_registrations}
{*{control type=color name=registrations_color label="Color for Event Registrations"|gettext value=$config.registrations_color hide=1 flip=1}*}
{control type=color name=registrations_color label="Color for Event Registrations"|gettext value=$config.registrations_color}
{/if}
