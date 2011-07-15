<h2>Category Display</h2>
{control type=dropdown name=category label="Category to Display" frommodel=storeCategory display=title key=id includeblank="Display all categories" value=$config.category}
<h2>Product Display</h2>
{control type="text" name="imagesperrow" label="Products per Row (also determines product width if not set below)" value=$config.imagesperrow|default:1}
{control type="text" name="productheight" label="Product Height (0 will not set a height)" value=$config.productheight|default:200}

<h2>Image Display</h2>
<h3>Product Listing Pages</h3>
{control type="text" name="listingwidth" label="Maximum image width" value=$config.listingwidth|default:150}
{control type="text" name="listingheight" label="Maximum image height" value=$config.listingheight|default:0}

<h3>Product Detail Pages</h3>
{control type="text" name="displaywidth" label="Image Viewer Width" value=$config.displaywidth|default:250}
{control type="text" name="displayheight" label="Image Viewer Height (0 for auto height)" value=$config.displayheight|default:0}
<h4>Thumnails</h4>
{control type="checkbox" name="thumbsattop" label="Display thumbnails above main image?" checked=$config.thumbsattop|default:1 value=1}
{control type="text" name="addthmbw" label="Thumbnail width" value=$config.addthmbw|default:50}
{control type="text" name="addthmbh" label="Thumbnail height" value=$config.addthmbh|default:50}
<h4>Swatches</h4>
{control type="text" name="swatchsmw" label="Swatch Thumbnail width" value=$config.swatchsmw|default:50}
{control type="text" name="swatchsmh" label="Swatch Thumbnail Height" value=$config.swatchsmh|default:50}
{control type="text" name="swatchpopw" label="Swatch Thumbnail popup width" value=$config.swatchpopw|default:75}
{control type="text" name="swatchpoph" label="Swatch Thumbnail popup width" value=$config.swatchpoph|default:75}


