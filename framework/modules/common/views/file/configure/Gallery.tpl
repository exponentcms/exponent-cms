<h3>{"Image Gallery Configuration"|gettext}</h3>
{control type="text" label="Width of Primary Image" name="piwidth" value=$config.piwidth|default:100 size=5}
{control type="text" name="thumb" label="Thumbnail Box Size"|gettext value=$config.thumb|default:100 size=5}
{control type="text" name="spacing" label="Thumbnail Spacing"|gettext value=$config.spacing|default:10 size=5}
{control type="text" name="tclass" label="Additional Thumbnail Class"|gettext value=$config.tclass}