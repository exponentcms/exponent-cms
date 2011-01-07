{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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

<div class="module file gallery">
	{if $title}<h2>{$title}</h2>{/if}
	{foreach from=$files item=image}
    {if $config.imagegallery_square!=0}
        {img id="img`$pic->id`" class=$params.class file_id=$image->id square="`$config.imagegallery_square`"}
    {elseif $config.imagegallery_constrain==1}
        {img id="img`$pic->id`" class=$params.class file_id=$image->id constrain="`$config.imagegallery_constrain`" width="`$config.imagegallery_width`" height="`$config.imagegallery_height`"}
    {else}
        {img id="img`$pic->id`" class=$params.class file_id=$image->id width="`$config.imagegallery_width`" height="`$config.imagegallery_height`"}
    {/if}
	{/foreach}
</div>
