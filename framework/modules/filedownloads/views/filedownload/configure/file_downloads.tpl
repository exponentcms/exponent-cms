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
    {'This is where you can configure the settings to be used by this File Download module.'|gettext}&nbsp;&nbsp;
    {'These settings will only apply to this particular module.'|gettext}
</p>
{control type=dropdown name=order label="Sort By"|gettext items="Date Added, Date Added Descending, Date Updated, Date Updated Descending, Number Downloads, Number Downloads Descending, Alphabetical, Reverse Alphabetical, Order Manually" values="created_at,created_at DESC,edited_at,edited_at DESC,downloads,downloads DESC,title,title DESC,rank" value=$config.order|default:'created_at DESC'}
{*{control type=dropdown name=dir label="Sort Order"|gettext items="Newest First, Oldest First" values="DESC, ASC" value=$config.dir}*}
{control type="radiogroup" name="usebody" label="Body Text"|gettext value=$config.usebody|default:0 items="Full,Summary,None" values="0,1,2"}
{control type="checkbox" name="quick_download" label="Quick Download"|gettext|cat:"?" value=1 checked=$config.quick_download}
{control type="checkbox" name="show_info" label="Show File Info"|gettext|cat:"?" value=1 checked=$config.show_info}
{control type="checkbox" name="show_icon" label="Show File Icon or Preview"|gettext|cat:"?" value=1 checked=$config.show_icon}
{control type="checkbox" name="show_player" label="Show Media Player"|gettext|cat:"?" value=1 checked=$config.show_player}
