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

<h2>{"Configure this Module"|gettext}</h2>
{control type=dropdown name=order label="Sort By"|gettext items="Alphabetical, Reverse Alphabetical, Order Manually" values="title,title DESC,rank" value=$config.order|default:rank}
{control type="checkbox" name="only_featured" label="Only show featured Portfolio Pieces"|gettext value=1 checked=$config.only_featured}
{control type="radiogroup" name="usebody" label="Body Text"|gettext value=$config.usebody|default:0 items="Full,Summary,None" values="0,1,2"}
