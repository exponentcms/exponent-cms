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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("FAQ Settings"|gettext) module="faq"}
		</div>
        <h2>{'FAQ Settings'|gettext}</h2>
	</div>
</div>
{control type="checkbox" name="allow_user_questions" label="Allow users to ask questions"|gettext value=1 checked=$config.allow_user_questions}
{control type="checkbox" name="use_toc" label="Show Table of Content when displaying FAQs"|gettext value=1 checked=$config.use_toc}

