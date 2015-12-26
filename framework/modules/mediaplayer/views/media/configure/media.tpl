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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Media Player Settings"|gettext) module="mediaplayer"}
		</div>
        <h2>{"Media Player Settings"|gettext}</h2>
        <blockquote>
            {"This is where you can configure the settings used by this Media Player module."|gettext}&#160;&#160;
            {"These settings only apply to this particular module."|gettext}
        </blockquote>
	</div>
</div>
{*{control type="checkbox" name="autoplay" label="Automatically Play Media Files"|gettext value=1 checked=$config.autoplay}*}
{control type=dropdown name="video_style" items="Default,TED,WMP"|gettxtlist values="mejs,mejs-ted,mejs-wmp" label="Player Style"|gettext value=$config.video_style|default:"mejs" focus=1}
{control type="text" name="video_width" label="Video Width"|gettext value=$config.video_width|default:200 size=4}
{control type="text" name="video_height" label="Video Height"|gettext value=$config.video_height|default:143 size=4}
{control type="checkbox" name="use_lightbox" label="Use Lightbox?"|gettext value=1 checked=$config.use_lightbox}
{group label="Lightbox Thumbnail"|gettext}
    {control type="text" name="thumb_width" label="Thumbnail Width"|gettext value=$config.thumb_width|default:64 size=4}
    {control type="text" name="thumb_height" label="Thumbnail Height"|gettext value=$config.thumb_height|default:64 size=4}
{/group}
{group label="Player Controls"|gettext}
    {control type="checkbox" name="control_play" label="Play/Pause"|gettext value=1 checked=$config.control_play|default:1}
    {control type="checkbox" name="control_stop" label="Stop"|gettext value=1 checked=$config.control_stop}
    {control type="checkbox" name="control_scrubber" label="Progress"|gettext value=1 checked=$config.control_scrubber}
    {control type="checkbox" name="control_time" label="Time"|gettext value=1 checked=$config.control_time}
    {control type="checkbox" name="control_volume" label="Volume"|gettext value=1 checked=$config.control_volume}
    {control type="checkbox" name="control_fullscreen" label="Fullscreen"|gettext value=1 checked=$config.control_fullscreen}
{/group}
{if $smarty.const.SITE_FILE_MANAGER == 'elfinder'}
    {control type="text" name="upload_folder" label="Quick Add Upload Subfolder"|gettext value=$config.upload_folder}
{else}
    {control type=dropdown name="upload_folder" label="Select the Quick Add Upload Folder"|gettext items=$folders value=$config.upload_folder}
{/if}
