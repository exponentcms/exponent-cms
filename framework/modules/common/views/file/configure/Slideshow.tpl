{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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
 
<h2>Slideshow File Displayer Settings</h2>
{control type=text name="slideshow_framelength" label="Slide length in Seconds" value=$config.slideshow_framelength|default:5 size="5"}

{control type=dropdown name="slideshow_anim" 
items="Fade,Slide Right,Slide Left,Slide Up,Reveal Left,Reveal Right,Reveal up,Reveal Down" 
values="fadeOut,slideRight,slideLeft,slideUp,squeezeLeft,squeezeRight,squeezeUp,squeezeDown" label="Animation Type" value=$config.slideshow_anim}

{control type=text name="slideshow_width" label="Slideshow Width" value=$config.slideshow_width|default:100 size="5"}
{control type=text name="slideshow_height" label="Slideshow Height" value=$config.slideshow_height|default:100 size="5"}


