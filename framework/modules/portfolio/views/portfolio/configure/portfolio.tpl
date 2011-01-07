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

<h2>{"Configure Portfolio"|gettext}</h2>
{control type="text" name="limit" label="Number of pieces to show before paginating"|gettext size=3 filter=integer value=$config.limit|default:10}
{control type="text" name="thumbsize" label="Thumbnail Box Size on listing page"|gettext size=3 filter=integer value=$config.thumbsize|default:200}
{control type="text" name="detailthumbsize" label="Thumbnail Box Size detail page"|gettext size=3 filter=integer value=$config.detailthumbsize|default:100}
{control type="text" name="enlargedsize" label="Enlarged image size"|gettext size=3 filter=integer value=$config.enlargedsize|default:500}
{control type="checkbox" name="truncate" label="Summarize by showing only first paragraph on listing pages?"|gettext checked=$config.truncate value=1}
