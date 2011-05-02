{css unique="gallery-config"}
{literal}
small {
    display:block;
}
{/literal}
{/css}


<h3>{"Image Gallery Configuration"|gettext}</h3>
{control type="checkbox" label="Lightbox effect" name="lightbox" value=1 checked=$config.lightbox}
{control type="text" label="Width of Primary Image<small>Setting to 0 will default to <em>Thumbnail Box Size</em> settings</small>" name="piwidth" value=$config.piwidth|default:100 size=5}
{control type="checkbox" label="Only show primary image on listing pages" name="pio" value=1 checked=$config.pio}
{control type="text" name="thumb" label="Thumbnail Box Size"|gettext value=$config.thumb|default:100 size=5}
{control type="text" name="spacing" label="Thumbnail Spacing"|gettext value=$config.spacing|default:10 size=5}
{control type="dropdown" name="floatthumb" label="Float Thumbnails" items="No Float,Left,Right" value=$config.floatthumb}
{control type="text" name="tclass" label="Additional Thumbnail Class"|gettext value=$config.tclass}