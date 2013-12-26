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
		    {help text="Get Help with"|gettext|cat:" "|cat:("Banner Settings"|gettext) module="banner"}
		</div>
        <h2>{'Banner Settings'|gettext}</h2>
	</div>
</div>
<blockquote>
    {'This is where you can configure the settings used by this Banner module.'|gettext}&#160;&#160;
    {'These settings only apply to this particular banner module.'|gettext}
</blockquote>
<h2>{'Number of Banners to Display'|gettext}</h2>
{control type="text" name="limit" label="Number of banners"|gettext size=3 filter=integer value=$config.limit}
<h2>{'Banner Size'|gettext}</h2>
{control type="text" name="width" label="Width"|gettext size=4 filter=integer value=$config.width}
{control type="text" name="height" label="Height"|gettext size=4 filter=integer value=$config.height}
