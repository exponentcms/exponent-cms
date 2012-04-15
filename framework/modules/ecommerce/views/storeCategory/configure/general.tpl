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

{control type=hidden name=category name="cat-id" value=$smarty.get.id}
<h2>{'Global Override'|gettext}</h2>
{control type="checkbox" name="use_global" label="Use the Global Store Settings instead?"|gettext value=1 checked=$config.use_global}

<h2>{'Display'|gettext}</h2>
{control type="checkbox" name="show_cats" label="Show Categories on listing pages"|gettext value=1 checked=$config.show_cats|default:1}
{control type="checkbox" name="show_prods" label="Show Products on listing pages"|gettext value=1 checked=$config.show_prods|default:1}

<h2>{'Product Sorting'|gettext}</h2>
{control type="dropdown" name="orderby" label="Default sort order"|gettext items="Name, Price, Rank"|gettextlist values="title,base_price,rank" value=$config.orderby}
{control type="dropdown" name="orderby_dir" label="Sort direction"|gettext items="Ascending, Descending"|gettextlist values="ASC, DESC" value=$config.orderby_dir}

<h2>{'Pagination'|gettext}</h2>
{control type="text" name="pagination_default" label="Default # of products to show per page"|gettext size=3 filter=integer value=$config.pagination_default}

<h2>{'Product Listing Pages'|gettext}</h2>
{control type="text" name="imagesperrow" label="Products per Row (also determines product width if not set below)"|gettext value=$config.imagesperrow|default:1}
{control type="text" name="productheight" label="Product Height (0 will not set a height)"|gettext value=$config.productheight|default:200}
{control type="text" name="listingwidth" label="Maximum image width"|gettext value=$config.listingwidth|default:150}
{control type="text" name="listingheight" label="Maximum image height"|gettext value=$config.listingheight|default:0}

<h2>{'Product Detail Pages'|gettext}</h2>
{control type="text" name="displaywidth" label="Image Viewer Width"|gettext value=$config.displaywidth|default:250}
{control type="text" name="displayheight" label="Image Viewer Height (0 for auto height)"|gettext value=$config.displayheight|default:0}
<h3>{'Thumbnails'|gettext}</h3>
{control type="checkbox" name="thumbsattop" label="Display thumbnails above main image?"|gettext checked=$config.thumbsattop|default:1 value=1}
{control type="text" name="addthmbw" label="Thumbnail width"|gettext value=$config.addthmbw|default:50}
{control type="text" name="addthmbh" label="Thumbnail height"|gettext value=$config.addthmbh|default:50}
<h3>{'Swatches'|gettext}</h3>
{control type="text" name="swatchsmw" label="Swatch Thumbnail width"|gettext value=$config.swatchsmw|default:50}
{control type="text" name="swatchsmh" label="Swatch Thumbnail Height"|gettext value=$config.swatchsmh|default:50}
{control type="text" name="swatchpopw" label="Swatch Thumbnail popup width"|gettext value=$config.swatchpopw|default:75}
{control type="text" name="swatchpoph" label="Swatch Thumbnail popup width"|gettext value=$config.swatchpoph|default:75}
