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

<h2>{'Miscellaneous Information'|gettext}</h2>
{control type="hidden" name="tab_loaded[misc]" value=1} 
{control type="text" name="misc[warehouse_location]" label="Warehouse Location"|gettext value=$record->warehouse_location}
{control type="text" name="misc[previous_id]" label="Previous Product ID"|gettext value=$record->previous_id}
