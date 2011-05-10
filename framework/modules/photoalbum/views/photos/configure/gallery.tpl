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

<h3>{"Gallery Settings"|gettext}</h3>
<hr />
<h4>Gallery Page</h4>
{control type=text name="pa_showall_thumbbox" label="Box size for image thumbnails" value=$config.pa_showall_thumbbox|default:100 size="5"}
{control type="checkbox" name="lightbox" label="Use lightbox effect" value=1 checked=$config.lightbox}
<hr />
<h4>Detail Page or Lightbox</h4>
{control type=text name="pa_showall_enlarged" label="Box size for enlarged images" value=$config.pa_showall_enlarged|default:300 size="5"}
<h4>Detail Page</h4>
{control type="dropdown" name="pa_float_enlarged" label="Float enlarged image" items="No Float,Left,Right" value=$config.pa_float_enlarged}
