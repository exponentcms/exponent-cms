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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Simple Poll Settings"|gettext) module="simplepoll"}
		</div>
        <h2>{"Simple Poll Settings"|gettext}</h2>
	</div>
</div>
<blockquote>
    {"This is where you can configure the settings used by this Simple Poll module."|gettext}&#160;&#160;
    {"These settings only apply to this particular module."|gettext}
</blockquote>
{control type="html" name="thank_you_message" label='\'Thank You\' Message'|gettext value=$config.thank_you_message|default:'Thank you for voting.'|gettext}
{control type="html" name="already_voted_message" label='\'Already Voted\' Message'|gettext value=$config.already_voted_message|default:'You have already voted in this poll.'|gettext}
{control type="html" name="voting_closed_message" label='\'Voting Closed\' Message'|gettext value=$config.voting_closed_message|default:'Voting has been closed for this poll.'|gettext}
{control type="text" name="anonymous_timeout" label='Anonymous Block Timeout (hours)'|gettext size=4 filter=integer value=$config.anonymous_timeout|default:'5'}
