{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div class="module flowplayer edit yui3-skin-sam">
    <h1>
        {if $record->id}{'New Media Piece'|gettext}{else}{'Editing'|gettext} {$record->title}{/if}
    </h1>

	{if !$config.video_width}
        {$width="200"}
	{else}
        {$width=$config.video_width}
	{/if}
	{if !$config.video_height}
        {$height="143"}
	{else}
        {$height=$config.video_height}
	{/if}
    {form action=update}
        {control type="hidden" name="id" value=$record->id}
        {control type="hidden" name=rank value=$record->rank}
        {control type="text" name="title" label="Title"|gettext value=$record->title}
        {control type="html" name="body" label="Description"|gettext value=$record->body}
        {control type="text" name="width" label="Width"|gettext filter=integer value=$record->width|default:$width}
        {control type="text" name="height" label="Height"|gettext filter=integer value=$record->height|default:$height}
        {*{control type=text name=url label="YouTube Video URL"|gettext value=$record->url size=25 description='A link to a YouTube video.  YouTube takes pecedence over an attached file.'|gettext}*}
        {control type=url name=url label="YouTube Video URL"|gettext value=$record->url size=25 description='A link to a YouTube video.  YouTube takes pecedence over an attached file.'|gettext}
        {control type="files" name="files" label="Media File"|gettext|cat:" (.flv, .f4v, .mp4, or .mp3)" subtype=media value=$record->expFile limit=1}
        {control type="files" name="splash" label="Video Splash Image"|gettext subtype=splash value=$record->expFile limit=1 description='Initial image to display behind a video'|gettext}
        {if !$config.disabletags}
             {control type="tags" value=$record}
         {/if}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
