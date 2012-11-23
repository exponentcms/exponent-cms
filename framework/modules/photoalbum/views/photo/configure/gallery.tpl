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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Photo Album Settings"|gettext) module="photo"}
		</div>
        <h2>{"Photo Album Settings"|gettext}</h2>
	</div>
</div>
<h4>{'Gallery Page'|gettext}</h4>
{control type=dropdown name=order label="Sort By"|gettext items="Order Manually, Random"|gettxtlist values="rank,RAND()" value=$config.order|default:rank}
{control type=text name="pa_showall_thumbbox" label="Box size for image thumbnails"|gettext value=$config.pa_showall_thumbbox|default:100 size="5"}
{control type=text name="quality" label="Thumbnail JPEG Quality"|gettext|cat:" (0 - 95)" value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}
{control type="checkbox" name="lightbox" label="Use lightbox effect"|gettext value=1 checked=$config.lightbox}
{control type="dropdown" name="landing" label="Gallery pages displayed as:"|gettext items="Gallery,Slideshow"|gettxtlist values="showall,slideshow" value=$config.landing}
<hr />
<h4>{'Detail Page or Lightbox'|gettext}</h4>
{control type=text name="pa_showall_enlarged" label="Box size for enlarged images"|gettext value=$config.pa_showall_enlarged|default:300 size="5"}
<h4>{'Detail Page'|gettext}</h4>
{control type="dropdown" name="pa_float_enlarged" label="Float enlarged image"|gettext items="No Float,Left,Right"|gettxtlist values="No Float,Left,Right" value=$config.pa_float_enlarged}
