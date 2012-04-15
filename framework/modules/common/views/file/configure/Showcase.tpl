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

<h3>{"Image ShowcaseConfiguration"|gettext}</h3>
<h4>{"Portolio listing page"|gettext}</h4>

{control type="checkbox" label="Only show primary image on listing pages"|gettext name="pio" value=1 checked=$config.pio}
{control type="text" label="Listing page image width"|gettext name="listingwidth" value=$config.listingwidth|default:100 size=5}

<h4>{"Portolio landing page"|gettext}</h4>
{control type="dropdown" name="lpfloat" label="File Display Box Float"|gettext items="No Float,Left,Right"|gettextlist values="No Float,Left,Right" value=$config.lpfloat}
{control type="text" label="Width of Landing Page File Display Box"|gettext name="lpfwidth" value=$config.lpfwidth size=5}
{control type="text" label="Width of main image"|gettext name="piwidth" value=$config.piwidth|default:100 size=5}
{control type="text" name="thumb" label="Thumbnail Box Size"|gettext value=$config.thumb|default:100 size=5}
{control type="radiogroup" columns=2 name="hoverorclick" label="Replace main image on click or hover?"|gettext items="Click,Hover"|gettextlist values="1,2"  default=$config.hoverorclick|default:"1"}
{control type="text" name="spacing" label="Thumbnail Spacing"|gettext value=$config.spacing|default:10 size=5}
{control type=text name="quality" label="Thumbnail JPEG Quality"|gettext|cat:" <small>0 - 99. 100 "|cat:("will use actual image without thumbnailing"|gettext|cat:"</small>") value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}
{control type="text" name="tclass" label="Stylesheet class to apply to images"|gettext value=$config.tclass}