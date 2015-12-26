{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
    {control type="checkbox" name="use_global" label="Use the Global Store Settings instead?"|gettext value=1 checked=$config.use_global focus=1}
{/group}
{group label='Product Listing Pages'|gettext}
    {control type="number" name="images_per_row" label="Products per Row"|gettext value=$config.images_per_row|default:3 min=0 max=6 description='0 will use default'|gettext}
    {control type="text" name="pagination_default" label="Default # of products to show per page"|gettext size=3 filter=integer value=$config.pagination_default}
    {control type="checkbox" name="show_products" label="Show all products with the category?"|gettext value=1 checked=$node->show_products description='Show all products under category when displaying categories'|gettext}
    {control type="text" name="productheight" label="Product Height"|gettext value=$config.productheight|default:200 description='0 will not set a height'|gettext}
{/group}
{group label="Product Sorting"|gettext}
    {control type="dropdown" name="orderby" label="Default sort order"|gettext items="Name, Price, Rank"|gettxtlist values="title,base_price,rank" value=$config.orderby}
    {control type="dropdown" name="orderby_dir" label="Sort direction"|gettext items="Ascending, Descending"|gettxtlist values="ASC, DESC" value=$config.orderby_dir}
{/group}
{if $smarty.const.SITE_FILE_MANAGER == 'elfinder'}
    {control type="text" name="upload_folder" label="Quick Add Upload Subfolder"|gettext value=$config.upload_folder}
{else}
    {control type=dropdown name="upload_folder" label="Select the Quick Add Upload Folder"|gettext items=$folders value=$config.upload_folder}
{/if}
