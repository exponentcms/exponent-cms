{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

<h2>Configure this Banner Module</h2>
<p>
    This is where you can configure the settings to be used by this news module. 
    These settings will only apply to this particular banner module.
</p>
<h2>Number of Banners to Display</h2>
{control type="text" name="limit" label="Number of banners" size=3 filter=integer value=$config.limit}
<h2>Banner Size</h2>
{control type="text" name="width" label="Width" size=4 filter=integer value=$config.width}
{control type="text" name="height" label="Height" size=4 filter=integer value=$config.height}
