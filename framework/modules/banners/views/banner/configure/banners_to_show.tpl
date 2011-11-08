{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

<h2>{'Show these banners'|gettext}</h2>
<table>
{foreach from=$banners item=banner}
    <tr>    					        
        <td>{control type="checkbox" name="banners[]" label=" " value=$banner->id checked=$config.banners}</td>
        <td>{img file_id=$banner->expFile[0]->id width=96 height=48}</td>
        <td>{$banner->title}{br}{$banner->company->title}</td>
    </tr>
{/foreach}
</table>
