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

<h2>{'Configure this Module'|gettext}</h2>
{*control type=dropdown name=order label="Sort Order"|gettext items="$sortopts" value=$config.order*}
{control type=dropdown name=order label="Sort By"|gettext items="Date Added, Date Added Descending, Date Updated, Date Updated Descending, Date Published, Date Published Descending"|gettxtlist values="created_at,created_at DESC,edited_at,edited_at DESC,publish,publish DESC" value=$config.order|default:'publish DESC'}
{control type="checkbox" name="only_featured" label="Only show Featured News Items"|gettext value=1 checked=$config.only_featured}
{control type="radiogroup" name="usebody" label="Body Text"|gettext value=$config.usebody|default:0 items="Full,Summary,None"|gettxtlist values="0,1,2"}
{control type="checkbox" name="printlink" label="Display Printer-Friendly Link"|gettext value=1 checked=$config.printlink}
