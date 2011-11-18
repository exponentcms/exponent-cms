{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<h2>{'Configure this Module'|gettext}</h2>
<p>
    {'This is where you can configure the settings to be used by this Link Manager module.
    These settings will only apply to this particular module.'|gettext}
</p>
{control type=dropdown name=order label="Sort By"|gettext items="Alphabetical, Reverse Alphabetical, Order Manually" values="title,title DESC,rank" value=$config.order|default:rank}

