{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{group label="Image Gallery Configuration"|gettext}
    {control type="checkbox" name="lightbox" label="Lightbox effect"|gettext value=1 checked=$config.lightbox}
    {control type="text" name="piwidth" label="Width of primary image"|gettext value=$config.piwidth|default:100 size=5 description="Setting to 0 will default to Thumbnail Box Size settings"|gettext}
    {control type="checkbox" name="pio" label="Only show primary image on listing pages"|gettext value=1 checked=$config.pio}
    {control type="text" name="tclass" label="Stylesheet class to apply to images"|gettext value=$config.tclass}
    {group label="Thumbnails (All but primary image)"|gettext}
        {control type="dropdown" name="floatthumb" label="Thumbnail Placement in Relation to Primary Image"|gettext items="No Float,Left,Right,Bottom"|gettxtlist values="No Float,Left,Right,Bottom" value=$config.floatthumb}
        {control type="text" name="thumb" label="Thumbnail size"|gettext value=$config.thumb|default:48 size=5}
        {control type="text" name="spacing" label="Thumbnail spacing"|gettext value=$config.spacing|default:5 size=5}
        {control type=text name="quality" label="Thumbnail JPEG Quality"|gettext|cat:" (0 - 95)" value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}
    {/group}
{/group}