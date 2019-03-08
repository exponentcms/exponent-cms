{*
 * Copyright (c) 2004-2019 OIC Group, Inc.
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

{group label="Image Showcase Configuration"|gettext}
    {control type="checkbox" name="pio" label="Only show primary image on listing pages"|gettext value=1 checked=$config.pio}
    {control type="text" name="listingwidth" label="Width of primary image on listing page"|gettext value=$config.listingwidth|default:100 size=5}
    {*{control type="dropdown" name="lpfloat" label="Float File Display Box"|gettext items="No Float,Left,Right"|gettxtlist values="No Float,Left,Right" value=$config.lpfloat}*}
    {*{control type="text" name="lpfwidth" label="Width of File Display Box"|gettext value=$config.lpfwidth size=5}*}
    {control type="text" name="piwidth" label="Width of primary image on non-listing page"|gettext value=$config.piwidth|default:100 size=5}
    {control type="radiogroup" name="hoverorclick" columns=2 label="Replace main image on click or hover?"|gettext items="Click,Hover"|gettxtlist values="1,2" default=$config.hoverorclick|default:"1"}
    {control type="text" name="tclass" label="Stylesheet class to apply to images"|gettext value=$config.tclass}
    {group label="Thumbnails"|gettext}
        {control type="text" name="thumb" label="Thumbnail size"|gettext value=$config.thumb|default:48 size=5}
        {control type="text" name="spacing" label="Thumbnail spacing"|gettext value=$config.spacing|default:5 size=5}
        {control type=text name="quality" label="Thumbnail JPEG Quality"|gettext|cat:" (0 - 95)" value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}
    {/group}
{/group}