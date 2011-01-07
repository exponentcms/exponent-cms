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

<h2>Configure this News Module</h2>
{control type=text name=limit label="Number of posts to paginate News Items at" value=$config.limit}
{*control type=dropdown name=order label="Sort Order" items="$sortopts" value=$config.order*}
{control type="checkbox" name="only_featured" label="Only show Featured News Items" value=1 checked=$config.only_featured}
{control type="checkbox" name="truncate" label="Summarize by showing only first paragraph on listing pages?"|gettext checked=$config.truncate value=1}

