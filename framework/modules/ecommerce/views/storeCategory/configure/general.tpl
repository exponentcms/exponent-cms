{control type=hidden name=category name="cat-id" value=$smarty.get.id}
<h2>Global Override</h2>
{control type="checkbox" name="use_global" label="Use the Global Store Settings instead?" value=1 checked=$config.use_global}

<h2>Display</h2>
{control type="checkbox" name="show_cats" label="Show Categories on listing pages" value=1 checked=$config.show_cats|default:1}
{control type="checkbox" name="show_prods" label="Show Products on listing pages" value=1 checked=$config.show_prods|default:1}

<h2>Product Sorting</h2>
{control type="dropdown" name="orderby" label="Default sort order" items="Name, Price, Rank" values="title,base_price,rank" value=$config.orderby}
{control type="dropdown" name="orderby_dir" label="Sort direction" items="Ascending, Descending" values="ASC, DESC" value=$config.orderby_dir}				    

<h2>Pagination</h2>
{control type="text" name="pagination_default" label="Default # of products to show per page" size=3 filter=integer value=$config.pagination_default}

<h2>Product Listing Pages</h2>
{control type="text" name="imagesperrow" label="Products per Row (also determines product width if not set below)" value=$config.imagesperrow|default:1}
{control type="text" name="productheight" label="Product Height (0 will not set a height)" value=$config.productheight|default:200}
{control type="text" name="listingwidth" label="Maximum image width" value=$config.listingwidth|default:150}
{control type="text" name="listingheight" label="Maximum image height" value=$config.listingheight|default:0}

<h2>Product Detail Pages</h2>
{control type="text" name="displaywidth" label="Image Viewer Width" value=$config.displaywidth|default:250}
{control type="text" name="displayheight" label="Image Viewer Height (0 for auto height)" value=$config.displayheight|default:0}
<h3>Thumnails</h3>
{control type="checkbox" name="thumbsattop" label="Display thumbnails above main image?" checked=$config.thumbsattop|default:1 value=1}
{control type="text" name="addthmbw" label="Thumbnail width" value=$config.addthmbw|default:50}
{control type="text" name="addthmbh" label="Thumbnail height" value=$config.addthmbh|default:50}
<h3>Swatches</h3>
{control type="text" name="swatchsmw" label="Swatch Thumbnail width" value=$config.swatchsmw|default:50}
{control type="text" name="swatchsmh" label="Swatch Thumbnail Height" value=$config.swatchsmh|default:50}
{control type="text" name="swatchpopw" label="Swatch Thumbnail popup width" value=$config.swatchpopw|default:75}
{control type="text" name="swatchpoph" label="Swatch Thumbnail popup width" value=$config.swatchpoph|default:75}
