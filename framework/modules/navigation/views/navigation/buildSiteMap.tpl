{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

<div class="module navigation buildsitemap">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with"|gettext|cat:" "|cat:("Building a Sitemap"|gettext) module="build-sitemap"}
        </div>
		<h2>{'Build Sitemap'|gettext}</h2>
        <blockquote>
            {'Select the options below to change the details in'|gettext} /sitemap.xml
            <div class="text-danger">{'Warning! This command will REPLACE the existing'|gettext} /sitemap.xml</div>
        </blockquote>
	</div>
    <div>
        {form action=generateSiteMap}
            {control type="checkbox" name="include_mobile" label='Include Mobile Pages?' value=1}
            {control type="checkbox" name="include_images" label='Include Images (Photo)?' value=1}
            {control type="checkbox" name="include_videos" label='Include Videos (Media)?' value=1}
            {if $smarty.const.ECOM}
                {control type="checkbox" name="include_products" label='Include Products?' value=1}
            {/if}
            {control type=buttongroup submit="Generate"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>