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

{control type=hidden name=category name="cat-id" value=$smarty.get.id}
<h3>{$title}</h3>
{group label="Global Override"|gettext}
    {control type="checkbox" name="use_global" label="Use the Global Store Settings instead?"|gettext value=1 checked=$config.use_global}
{/group}
{group label='Product Display'|gettext}
    {control type="text" name="images_per_row" label="Products per Row (also determines product width if not set below)"|gettext value=$config.imagesperrow|default:1}
    {control type="text" name="productheight" label="Product Height (0 will not set a height)"|gettext value=$config.productheight|default:200}
{/group}
{group label="Product Sorting"|gettext}
    {control type="dropdown" name="orderby" label="Default sort order"|gettext items="Name, Price, Rank"|gettxtlist values="title,base_price,rank" value=$config.orderby}
    {control type="dropdown" name="orderby_dir" label="Sort direction"|gettext items="Ascending, Descending"|gettxtlist values="ASC, DESC" value=$config.orderby_dir}
{/group}
{group label="Pagination"|gettext}
    {control type="text" name="pagination_default" label="Default # of products to show per page"|gettext size=3 filter=integer value=$config.pagination_default}
{/group}