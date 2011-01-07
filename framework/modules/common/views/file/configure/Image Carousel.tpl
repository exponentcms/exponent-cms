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

<h2>This is the config for the image carousel</h2>

<h3>Square Images</h3>
<p>Setting a number will crop your images to fit within a square box at the specified pixel size. Leave it at 0 if you don't want square images.</p>
{control type=text name="thumbnail_square" label="Squared thumbnail box size" value=$config.thumbnail_square|default:50 size="5"}

<h3>Carousel</h3>
<p>Please set the height and width you desire for your carousel.  Leaving these fields blank can cause quirks.</p>
{control type=text name="carousel_width" label="Carousel Width" value=$config.carousel_width|default:200 size="5"}
{control type=text name="carousel_height" label="Carousel Height" value=$config.carousel_height|default:200 size="5"}


<h3>Other Carousel Controls</h3>
<p>The following items are optional for configuring your carousel.</p>
{control type=text name="carousel_num_visible" label="Number of Items Visible" value=$config.carousel_num_visible|default:1 size="5"}
{control type=dropdown name="carousel_circular" items="true,false" values="true,false" label="Circular Carousel" value=$config.carousel_circular}
{control type=dropdown name="carousel_vertical" items="false,true" values="false,true" label="Vertical Carousel" value=$config.carousel_vertical}

{control type=dropdown name="carousel_animate" items="Default,Easy Slide,Hard Slide,Bounce,Overshoot,Elastic" values="easeBoth,easeOut,easeInStrong,bounceIn,backBoth,elasticBoth" label="Carousel Animation" value=$config.carousel_animate}
