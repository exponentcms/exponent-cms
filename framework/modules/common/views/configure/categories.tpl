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

<h2>{"Allow item grouping by category"|gettext}</h2>
{control type="checkbox" name="usecategories" label="Use Categories for this module?"|gettext value=1 checked=$config.usecategories}
{control type=text name=uncat label="Label for Un-Categoried items"|gettext value=$config.uncat|default:"Uncategorized"|gettext}
{*{chain module=expCat view=manage}*}
