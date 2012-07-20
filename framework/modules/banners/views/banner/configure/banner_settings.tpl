{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<h2>{'Configure this Module'|gettext}</h2>
<p>
    {'This is where you can configure the settings to be used by this banner module.'|gettext}&#160;&#160;
    {'These settings will only apply to this particular banner module.'|gettext}
</p>
<h2>{'Number of Banners to Display'|gettext}</h2>
{control type="text" name="limit" label="Number of banners"|gettext size=3 filter=integer value=$config.limit}
<h2>Banner Size</h2>
{control type="text" name="width" label="Width"|gettext size=4 filter=integer value=$config.width}
{control type="text" name="height" label="Height"|gettext size=4 filter=integer value=$config.height}
