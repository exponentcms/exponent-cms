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

<h2>Configure this Module</h2>
<p>
    If no Message of the Day if found for the current day we can pull up a random previous
    Message of the Day.  If you would like to use this functionality check the box below.
</p>
{control type="checkbox" name="userand" label="Use Random MOTD" value=1 checked=$config.userand}
