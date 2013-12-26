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

<h2>{'Default Banner'|gettext}</h2>
<blockquote>
    {'This is the banner that will be used if there are no banners available for this banner module.'|gettext}
</blockquote>
{control type="files" name="default" label="Default Banner Image"|gettext accept="image/*" value=$config.defaultbanner}
