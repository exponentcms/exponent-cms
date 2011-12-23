{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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
 
<h2>{'Slideshow Configuration'}</h2>
{control type=text name="width" label="Slideshow Width"|gettext value=$config.width|default:350 size="5"}
{control type=text name="height" label="Slideshow Height"|gettext value=$config.height|default:200 size="5"}
{control type=text name="speed" label="Seconds per slide"|gettext value=$config.speed|default:5 size="5"}
{control type=text name="quality" label="Slide Thumbnail JPEG Quality"|gettext|cat:" (0 - 95, 100)<br><small>"|cat:("If quality is set to 100, the raw image will be used instead of thumbnailing"|gettext|cat:"</small>") value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}


