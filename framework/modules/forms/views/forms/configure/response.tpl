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
            {help text="Get Help with"|gettext|cat:" "|cat:("Form Response Settings"|gettext) module="form-response-settings"}
        </div>
        <h2>{"Form Submission Response Settings"|gettext}</h2>
    </div>
</div>
{control type=html name='response' label='Submission Response Display'|gettext value=$config.response description='Message to display on the site after submitting a form'|gettext}
{group label='Submission Response Email'|gettext description='A Response Email is only sent if the form contains a control named \'email\''|gettext}
    {control type="checkbox" name="is_auto_respond" label="Send an email to the user after form submission?"|gettext value=1 checked=$config.is_auto_respond}
    {control type="text" name="auto_respond_subject" label="Response Email Subject"|gettext value=$config.auto_respond_subject}
    {control type="html" name="auto_respond_body" label="Response Email Content"|gettext value=$config.auto_respond_body}
{/group}