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

<h2>{"Pagination Settings"|gettext}</h2>
{control type=text name=limit label="Items per page"|gettext value=$config.limit}
{control type=dropdown name=pagelinks label="Show page links"|gettext items="Top and Bottom,Top Only,Bottom Only,Don't show page links" values="Top and Bottom,Top Only,Bottom Only,Don't show page links" value=$config.pagelinks}
{control type="checkbox" name="multipageonly" label="Disable page links until page limit is reached"|gettext value=1 checked=$config.multipageonly}
