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
		    {help text="Get Help with"|gettext|cat:" "|cat:("Banner Settings"|gettext) module="banner"}
		</div>
        <h2>{'Banner Settings'|gettext}</h2>
        <blockquote>
            {'This is where you can configure the settings used by this Banner module.'|gettext}&#160;&#160;
            {'These settings only apply to this particular banner module.'|gettext}
        </blockquote>
	</div>
</div>
<h2>{'Number of Banners to Display'|gettext}</h2>
{control type="text" name="limit" label="Number of banners"|gettext size=3 filter=integer value=$config.limit focus=1}
<h2>{'Banner Size'|gettext}</h2>
{control type="text" name="width" label="Width"|gettext size=4 filter=integer value=$config.width}
{control type="text" name="height" label="Height"|gettext size=4 filter=integer value=$config.height}
{if $smarty.const.SITE_FILE_MANAGER == 'elfinder'}
    {control type="text" name="upload_folder" label="Quick Add Upload Subfolder"|gettext value=$config.upload_folder}
{else}
    {control type=dropdown name="upload_folder" label="Select the Quick Add Upload Folder"|gettext items=$folders value=$config.upload_folder}
{/if}
