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

<h2>{'Category Display'|gettext}</h2>
{control type=dropdown name=category label="Category to Display"|gettext frommodel=storeCategory display=title key=id includeblank="Display all categories"|gettext value=$config.category}
<h2>{'Product Display'|gettext}</h2>
{control type="text" name="imagesperrow" label="Products per Row (also determines product width if not set below)"|gettext value=$config.imagesperrow|default:1}
{control type="text" name="productheight" label="Product Height (0 will not set a height)"|gettext value=$config.productheight|default:200}

<h2>{'Image Display'|gettext}</h2>
<h3>{'Product Listing Pages'|gettext}</h3>
{control type="text" name="listingwidth" label="Maximum image width"|gettext value=$config.listingwidth|default:150}
{control type="text" name="listingheight" label="Maximum image height"|gettext value=$config.listingheight|default:0}

<h3>{'Product Detail Pages'|gettext}</h3>
{control type="text" name="displaywidth" label="Image Viewer Width"|gettext value=$config.displaywidth|default:250}
{control type="text" name="displayheight" label="Image Viewer Height (0 for auto height)"|gettext value=$config.displayheight|default:0}
<h4>{'Thumnails'|gettext}</h4>
{control type="checkbox" name="thumbsattop" label="Display thumbnails above main image?"|gettext checked=$config.thumbsattop|default:1 value=1}
{control type="text" name="addthmbw" label="Thumbnail width"|gettext value=$config.addthmbw|default:50}
{control type="text" name="addthmbh" label="Thumbnail height"|gettext value=$config.addthmbh|default:50}
<h4>{'Swatches'|gettext}</h4>
{control type="text" name="swatchsmw" label="Swatch Thumbnail width"|gettext value=$config.swatchsmw|default:50}
{control type="text" name="swatchsmh" label="Swatch Thumbnail Height"|gettext value=$config.swatchsmh|default:50}
{control type="text" name="swatchpopw" label="Swatch Thumbnail popup width"|gettext value=$config.swatchpopw|default:75}
{control type="text" name="swatchpoph" label="Swatch Thumbnail popup width"|gettext value=$config.swatchpoph|default:75}


