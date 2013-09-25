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

<div id="mediaplayer-edit" class="module flowplayer edit yui3-skin-sam">
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

        <div id="alt-control" class="alt-control">
            <div class="control"><label class="label">{'Type of Media'|gettext}</label></div>
            <div class="alt-body">
                {control type=radiogroup columns=2 name="media_type" items="File,YouTube"|gettxtlist values="file,youtube" default=$record->media_type|default:"file"}
                <div id="file-div" class="alt-item" style="display:none;">
                    {control type="files" name="files" label="Media File"|gettext|cat:" (.flv, .f4v, .mp4, m4v, or .mp3)" subtype=media accept="audio/*,video/*" value=$record->expFile limit=1}
                </div>
                <div id="youtube-div" class="alt-item" style="display:none;">
                    {control type=url name=url label="YouTube Video URL"|gettext value=$record->url size=100 description='A link to a YouTube video.  YouTube takes precedence over an attached file.'|gettext}
                </div>
            </div>
        </div>

        {control type="files" name="splash" label="Video Splash Image"|gettext subtype=splash accept="image/*" value=$record->expFile limit=1 description='Initial image to display behind a video'|gettext}
        {if !$config.disabletags}
             {control type="tags" value=$record}
         {/if}
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="media-type" yui3mods="node,node-event-simulate"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','node-event-simulate', function(Y) {
    var radioSwitchers = Y.all('#alt-control input[type="radio"]');
    radioSwitchers.on('click',function(e){
        Y.all(".alt-item").setStyle('display','none');
        var curdiv = Y.one("#" + e.target.get('value') + "-div");
        curdiv.setStyle('display','block');
    });

    radioSwitchers.each(function(node,k){
        if(node.get('checked')==true){
            node.simulate('click');
        }
    });
});
{/literal}
{/script}