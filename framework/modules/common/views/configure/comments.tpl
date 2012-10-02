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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Comment Settings"|gettext) module="comments"}
		</div>
        <h2>{'Comment Settings'|gettext}</h2>
	</div>
</div>
{control type=checkbox name=usescomments label="Disable Adding New Comments"|gettext value=1 checked=$config.usescomments}
{control type=checkbox name=disable_nested_comments label="Disable Nested Comments"|gettext value=1 checked=$config.disable_nested_comments}
{control type=checkbox name=hidecomments label="Hide Posted Comments"|gettext value=1 checked=$config.hidecomments}
{control type=editor name=commentinfo label="Comment Information"|gettext value=$config.commentinfo}
