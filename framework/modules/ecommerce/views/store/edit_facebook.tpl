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

{if $record->parent_id == 0}
    {control type="hidden" name="tab_loaded[facebook]" value=1}
    {if count($record->childProduct)}
        <h4><em>({'Child products inherit these settings.'|gettext})</em></h4>
    {/if}
    <h2>{'Facebook Meta'|gettext}</h2>
    <blockquote>
        {'Also used for Twitter, Pinterest, etc...'|gettext}
    </blockquote>
    {*{control type="hidden" name="facebook[type]" value='product'}*}
    {control type="text" name="facebook[title]" label="Meta Title"|gettext value=$record->meta_fb.title size=88 description='Override the product title for social media'|gettext}
    {control type="textarea" name="facebook[description]" label="Meta Description"|gettext rows=5 cols=35 size=200 value=$record->meta_fb.description description='Override the product summary for social media'|gettext}
    {control type="text" name="facebook[url]" label="Meta URL"|gettext value=$record->meta_fb.url description='Canonical URL for social media if different than Canonical URL'|gettext}
{else}
	<h4><em>({'Facebook Meta Details'|gettext} {'are inherited from this product\'s parent.'|gettext})</em></h4>
{/if}