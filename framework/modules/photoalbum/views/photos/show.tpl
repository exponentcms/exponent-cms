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
 
{css unique="photo-album" link="`$asset_path`css/photoalbum.css"}

{/css}

<div class="module photoalbum show">
    <h1>{$record->title}</h1>
    {permissions}
    <div class="item-actions">
        {if $permissions.edit == 1}
            {icon action=edit record=$record title="Edit"|gettext|cat:" `$record->title`"}
        {/if}
    </div>
    {/permissions}
    <div class="next-prev">
        <a href="{link action=show id=$previous}">{"Previous Image"|gettext}</a>
         | {$imgnum} {"of"|gettext} {$imgtot}| 
        <a href="{link action=show id=$next}">{"Next Image"|gettext}</a>
    </div>
    <div class="bodycopy">
        {capture assign="float"}{$config.pa_float_enlarged|lower|replace:" ":""}{/capture}
        {img alt=$record->alt file_id=$record->expFile[0]->id w=$config.pa_showall_enlarged class="img-large float-`$float`" title=$record->alt|default:$record->expFile[0]->title style="float:`$float`;"}    
        {$record->body}
    </div>
    
    {*if $config.usescomments}
        {comments content_type="blog" content_id=$record->id title="Comments"}
    {/if*}
</div>
