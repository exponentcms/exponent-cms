{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<h2>{'Category Display'|gettext}</h2>
{control type=dropdown name=category label="Category to Display"|gettext frommodel=storeCategory display=title key=id includeblank="Display all categories"|gettext value=$config.category}
{group label='Product Display'|gettext}
    {control type="number" name="images_per_row" label="Products per Row"|gettext value=$config.images_per_row|default:3 min=1 max=6 focus=1}
    {control type="text" name="productheight" label="Product Height (0 will not set a height)"|gettext value=$config.productheight|default:200}
{/group}