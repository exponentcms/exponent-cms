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

<h2>Configure this Module</h2>
<p>
    This is where you can configure the settings to be used by this File Download module. 
    These settings will only apply to this particular module.
</p>
<blockquote>
    This will limit the number of files shown at one time on this page, putting a link to view more 
    files on the page once you have more files for download than the limit allows
</blockquote>
{control type=text name=limit label="Number of files to show" value=$config.limit}
{control type=dropdown name=order label="Sort By" items="Date Added, Order Manually" values="created_at, rank" value=$config.order}
{control type=dropdown name=dir label="Sort Order" items="Newest First, Oldest First" values="DESC, ASC" value=$config.dir}

