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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("e-alerts settings"|gettext) module="e-alerts"}
		</div>
        <h2>{'Email-Alerts Subscription Settings'|gettext}</h2>
	</div>
</div>
<blockquote>
    <p>{'E-Alerts allow users to sign up to receive email notification of new or updated content.'|gettext}</p>
</blockquote>
{control type="checkbox" name="enable_ealerts" label="Enable E-Alerts"|gettext value=1 checked=$config.enable_ealerts}
{control type="checkbox" name="autosend_ealerts" label="Automatically Send E-Alerts"|gettext value=1 checked=$config.autosend_ealerts}
{control type="radiogroup" name="ealert_usebody" label="E-Alert Auto-Send Body Text"|gettext value=$config.ealert_usebody|default:0 items="Full,Summary,None"|gettxtlist values="0,1,2"}
{control type="text" name="ealert_title" label="E-Alerts Title"|gettext value=$config.ealert_title}
{*{control type="textarea" name="ealert_desc" label="E-Alerts Description"|gettext value=$config.ealert_desc}*}
