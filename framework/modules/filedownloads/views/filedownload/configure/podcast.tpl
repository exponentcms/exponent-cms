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

<h2>{'Podcast/RSS Feed Settings'|gettext}</h2>
{control type="checkbox" name="enable_rss" label="Enable Podcasting"|gettext value=1 checked=$config.enable_rss}
{control type="text" name="feed_title" label="Feed Title"|gettext value=$config.feed_title}
{control type="textarea" name="feed_desc" label="Feed Description"|gettext value=$config.feed_desc}
{*control type="textarea" name="itunes_cats" label="Itunes Categories" value=$config.itunes_cats*}
