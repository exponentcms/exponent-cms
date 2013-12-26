{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="module ealert send-confirmation">
    <h1>{'Confirm Email-Alert'|gettext}</h1>
    {form action="send_process"}
        <table>
            <tr>
                <td>{'From'|gettext}: {$smarty.const.SMTP_FROMADDRESS}</td>
            </tr>
            <tr>
                <td>{'To:'|gettext} {$number_of_subscribers} Subscribers</td>
            </tr>
        </table>
        {control type="hidden" name="id" value=$ealert->id}
        {control type="hidden" name="model" value=$ealert->module}
        {control type="hidden" name="sef_url" value=$record->sef_url}
        {control type="text" name="subject" label="Subject"|gettext size="50" value='Notification of New Content Posted to'|gettext|cat:' '|cat:$ealert->ealert_title}
        {control type="html" name="body" label="Message"|gettext value="<h3>"|cat:"New content was added titled"|gettext|cat:" '"|cat:$record->title|cat:"'"|gettext|cat:".</h3><hr>"|cat:$record->body}
        {control type="buttongroup" submit="Send E-Alert"|gettext cancel="Cancel"|gettext}
    {/form}
            
</div>
