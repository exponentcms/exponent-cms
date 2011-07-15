{css unique="gallery-config"}
{literal}
small {
    display:block;
}
{/literal}
{/css}


<h3>{"Image ShowcaseConfiguration"|gettext}</h3>
<h4>{"Portolio listing page"|gettext}</h4>

{control type="checkbox" label="Only show primary image on listing pages" name="pio" value=1 checked=$config.pio}
{control type="text" label="Listing page image width" name="listingwidth" value=$config.listingwidth|default:100 size=5}

<h4>{"Portolio landing page"|gettext}</h4>
{control type="dropdown" name="lpfloat" label="File Display Box Float" items="No Float,Left,Right" value=$config.lpfloat}
{control type="text" label="Width of Landing Page File Display Box" name="lpfwidth" value=$config.lpfwidth size=5}
{control type="text" label="Width of main image" name="piwidth" value=$config.piwidth|default:100 size=5}
{control type="text" name="thumb" label="Thumbnail Box Size"|gettext value=$config.thumb|default:100 size=5}
{control type="radiogroup" columns=2 name="hoverorclick" label="Replace main image on click or hover?" items="Click,Hover" values="1,2"  default=$config.hoverorclick|default:"1"}
{control type="text" name="spacing" label="Thumbnail Spacing"|gettext value=$config.spacing|default:10 size=5}
{control type=text name="quality" label="Thumbnail JPEG Quality <small>0 - 99. 100 will use actual image without thumbnailing</small>" value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}
{control type="text" name="tclass" label="Stylesheet class to apply to images"|gettext value=$config.tclass}