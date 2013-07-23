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
		    {help text="Get Help with"|gettext|cat:" "|cat:("Facebook Like Button Settings"|gettext) module="facebook-button"}
		</div>
        <h2>{'Facebook Like Button Settings'|gettext}</h2>
	</div>
</div>
{control type="checkbox" name="enable_facebook_like" label="Enable Facebook Like Button"|gettext value=1 checked=$config.enable_facebook_like description='Displays the \'Like\' button with each item'|gettext}
{control type="dropdown" name="layout" items="Standard,Button Count,Box Count"|gettxtlist values=",button_count,box_count" label="Layout Style"|gettext value=$config.layout|default:""}
{control type="text" name="width" label="Width"|gettext filter=integer size=3 value=$config.width|default:"450"}
{control type="checkbox" name="showfaces" label="Show Faces"|gettext value=1 checked=$config.showfaces}
{control type="dropdown" name="font" items="Arial,Lucida Grande,Segoe UI,Tahoma,Trebuchet MS,Verdana" values="arial,lucida grande,segoe ui,tahoma,trebuchet ms,verdana" label="Font"|gettext value=$config.font|default:""}
{control type="dropdown" name="color_scheme" items="Light,Dark"|gettxtlist values=",dark" label="Color Scheme"|gettext value=$config.color_scheme|default:""}
{control type="dropdown" name="verb" items="Like,Recommend"|gettxtlist values=",recommend" label="Verb to Display"|gettext value=$config.verb|default:""}
