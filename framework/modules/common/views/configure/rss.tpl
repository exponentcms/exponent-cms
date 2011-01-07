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

<h2>RSS Feed Settings</h2>
<blockquote>
    These setting allow you make syndicate your content via RSS.  To start syndicating all
    you have to do is enable RSS and give this module's content a title and description!
</blockquote>
{control type="checkbox" name="enable_rss" label="Enable RSS" value=1 checked=$config.enable_rss}
{control type="text" name="feed_title" label="Feed Title" value=$config.feed_title}
{control type="textarea" name="feed_desc" label="Feed Description" value=$config.feed_desc}
