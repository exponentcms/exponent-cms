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

<div class="module flowplayer edit">
    <h1>
        {if $record->id}New Video{else}Editing {$record->title}{/if}
    </h1>

    {form action=update}
        {control type="hidden" name="id" value=$record->id}
        {control type="text" name="title" label="Video Title" value=$record->title}
        {control type="html" name="body" label="Video Description" value=$record->body}
        {control type="text" name="width" label="width" filter=integer value=$record->width|default:$config.video_width}
        {control type="text" name="height" label="height" filter=integer value=$record->height|default:$config.video_height}
        {control type="files" name="files" label="Video File" subtype=video value=$record->expFile limit=1}
        {control type="files" name="splash" label="Splash Image" subtype=splash value=$record->expFile limit=1}
        {control type="buttongroup" submit="Submit" cancel="Cancel"}
    {/form}
</div>
