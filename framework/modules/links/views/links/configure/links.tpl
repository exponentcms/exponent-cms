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
		    {help text="Get Help with"|gettext|cat:" "|cat:("Links Settings"|gettext) module="links"}
		</div>
        <h2>{"Links Settings"|gettext}</h2>
        <blockquote>
            {'This is where you can configure the settings used by this Links module.'|gettext}&#160;&#160;
            {'These settings only apply to this particular module.'|gettext}
        </blockquote>
	</div>
</div>
{control type=dropdown name=order label="Sort By"|gettext items="Alphabetical, Reverse Alphabetical, Order Manually, Random"|gettxtlist values="title,title DESC,rank,RAND()" value=$config.order|default:rank focus=1}
{control type="checkbox" name="opennewwindow" label="Default to Open Link in New Window?"|gettext value=1 checked=$config.opennewwindow}
{control type="text" name="websnapr_key" label="Show link snapshots using websnapr"|gettext value=$config.websnapr_key description='Enter the key received from'|gettext|cat:' <a href="http://www.websnapr.com" target="_blank">websnapr</a>'}
{if $smarty.const.SITE_FILE_MANAGER == 'elfinder'}
    {control type="text" name="upload_folder" label="Quick Add Upload Subfolder"|gettext value=$config.upload_folder}
{else}
    {control type=dropdown name="upload_folder" label="Select the Quick Add Upload Folder"|gettext items=$folders value=$config.upload_folder}
{/if}
