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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Tweet Button Settings"|gettext) module="tweet-button"}
		</div>
        <h2>{'Tweet Button Settings'|gettext}</h2>
	</div>
</div>
{control type="checkbox" name="enable_tweet" label="Enable Tweet Button"|gettext value=1 checked=$config.enable_tweet description='Displays the \'Tweet\' button with each item'|gettext}
{control type="dropdown" name="layout" items="Standard,Horizontal,Vertical"|gettxtlist values=",horizontal,vertical" label="Layout Style"|gettext value=$config.layout|default:""}
{control type="dropdown" name="size" items="Medium,Large"|gettxtlist values=",large" label="Button Size"|gettext value=$config.size|default:""}
{*{control type="text" name="default_text" label="Default Tweet Text"|gettext value=$config.default_text}*}
