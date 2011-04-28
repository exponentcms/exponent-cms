{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

<h2>E-Alerts Settings</h2>
<blockquote>
    <p>
        E-Alerts allow your users to sign up to receive email versions of new content you create.  If you 
        want users to be able to sign up to  E-Alerts.
    </p>
    <p>
        The title and description you supply below is what will be displayed to your users on the E-Alerts
        sign-up form.
    </p>
</blockquote>
{control type="checkbox" name="enable_ealerts" label="Enable E-Alerts" value=1 checked=$config.enable_ealerts}
{control type="text" name="ealert_title" label="E-Alerts Title" value=$config.ealert_title}
{control type="textarea" name="ealert_desc" label="E-Alerts Description" value=$config.ealert_desc}
