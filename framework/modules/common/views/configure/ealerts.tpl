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

<h2>{'E-Alerts Subscription Settings'|gettext}</h2>
<blockquote>
    <p>
        {'E-Alerts allows users to sign up to receive email versions of new or updated content.'|gettext}&nbsp;&nbsp;
    </p>
    <p>
        {'The title and description below will be displayed to users on the E-Alerts sign-up form.'|gettext}
    </p>
</blockquote>
{control type="checkbox" name="enable_ealerts" label="Enable E-Alerts"|gettext value=1 checked=$config.enable_ealerts}
{control type="text" name="ealert_title" label="E-Alerts Title"|gettext value=$config.ealert_title}
{control type="textarea" name="ealert_desc" label="E-Alerts Description"|gettext value=$config.ealert_desc}
