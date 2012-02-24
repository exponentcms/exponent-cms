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

<h2>{"Configure this Module"|gettext}</h2>
{control type="checkbox" name="autoplay" label="Automatically Play Videos"|gettext value=1 checked=$config.autoplay}
{control type="text" name="video_width" label="Video Width"|gettext value=$config.video_width|default:200 size=4}
{control type="text" name="video_height" label="Video Height"|gettext value=$config.video_height|default:143 size=4}
{control type=dropdown name="video_style" items="Modern,Air,Tube" values="1,2,3" label="Player Style"|gettext value=$config.video_style|default:""}
<h3>{"Player Controls"|gettext}</h3>
{control type="checkbox" name="control_stop" label="Stop"|gettext value=1 checked=$config.control_stop}
{control type="checkbox" name="control_play" label="Play/Pause"|gettext value=1 checked=$config.control_play|default:1}
{control type="checkbox" name="control_scrubber" label="Progress"|gettext value=1 checked=$config.control_scrubber}
{control type="checkbox" name="control_time" label="Time"|gettext value=1 checked=$config.control_time}
{control type="checkbox" name="control_mute" label="Mute"|gettext value=1 checked=$config.control_mute}
{control type="checkbox" name="control_volume" label="Volume"|gettext value=1 checked=$config.control_volume}
{control type="checkbox" name="control_fullscreen" label="Fullscreen"|gettext value=1 checked=$config.control_fullscreen}
