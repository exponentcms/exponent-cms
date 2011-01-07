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

<h2>This is the config form for Thumbnails with Popups</h2>

<h3>Thumbnails</h3>
<p>Setting a number will crop your images to fit within a square box at the specified pixel size. Leave it at 0 if you don't want square thumbnail images.</p>
{control type=text name="thumbnail_square" label="Squared thumbnail box size" value=$config.thumbnail_square|default:50 size="5"}

<h3>Popup</h3>
<p>By default popups are full sized images.  Clear these values for no adjustment in file size.</p>
{control type=text name="popup_height" label="Height of popup image" value=$config.popup_height|default:300 size="5"}
{control type=text name="popup_width" label="Width of popup image" value=$config.popup_width|default:300 size="5"}
{control type=dropdown name="popup_matte" items="None, White Border" values="none,matte" label="Popup Border" value=$config.popup_matte}

<h3>Sepia</h3>
<p>You may choose to sepia tint your images.  Values range from 1 to 100, with 0 being no change in appearance. Sepia tinting may result in slower performance.</p>
{control type=text name="popup_sepia_color" label="Sepia tint images, values 1-100 " value=$config.popup_sepia_color|default:50 size="5"}
{*<!-- view plugins/function.img.php for other custom variables you can add.  Sepia can take hex values for color tint as well.  Greyscale, Blur, and many other options are available as filters -->*}
