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

<h2>This is the config form for Image Gallery</h2>

{control type=text name="imagegallery_width" label="Image Width" value=$config.imagegallery_width|default:100 size="5"}
{control type=text name="imagegallery_height" label="Image Height" value=$config.imagegallery_height|default:100 size="5"}
{control type=checkbox name="imagegallery_constrain" label="Keep aspect ratio?" checked=$config.imagegallery_constrain value=1}
<h3>Square Thumbnails</h3>
<p>Setting a number will crop your images to fit within a square box at the specified pixel size. Leave it at 0 if you don't want square thumbnail images.</p>
{control type=text name="imagegallery_square" label="Squared thumbnail box size" value=$config.imagegallery_square|default:0 size="5"}


