{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="gallery-config"}
{literal}
    small {
        display:block;
    }
{/literal}
{/css}

<h3>{"Image Gallery Configuration"|gettext}</h3>
{control type="checkbox" label="Lightbox effect"|gettext name="lightbox" value=1 checked=$config.lightbox}
{control type="text" label="Width of Primary Image"|gettext|cat:"<small>"|cat:("Setting to 0 will default to <em>Thumbnail Box Size</em> settings"|gettext|cat:"</small>") name="piwidth" value=$config.piwidth|default:100 size=5}
{control type="checkbox" label="Only show primary image on listing pages"|gettext name="pio" value=1 checked=$config.pio}
{control type="text" name="thumb" label="Thumbnail Box Size"|gettext value=$config.thumb|default:100 size=5}
{control type="text" name="spacing" label="Thumbnail Spacing"|gettext value=$config.spacing|default:10 size=5}
{control type="dropdown" name="floatthumb" label="Float Thumbnails"|gettext items="No Float,Left,Right" values="No Float,Left,Right" value=$config.floatthumb}
{control type=text name="quality" label="Thumbnail JPEG Quality"|gettext|cat:" (0 - 95)" value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}
{control type="text" name="tclass" label="Additional Thumbnail Class"|gettext value=$config.tclass}