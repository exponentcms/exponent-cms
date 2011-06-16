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

<div class="module ealert send-confirmation">
    <h1>Format Email Alert</h1>
    
    {form action="send_process"}
        <table>
        <tr>
            <td>From: {$smarty.const.SMTP_FROMADDRESS}</td>
        </tr>
        <tr>
            <td>To: {$number_of_subscribers} Subscribers</td>
        </tr>
        </table>
        {control type="hidden" name="id" value=$ealert->id}
        {control type="text" name="subject" label="Subject" size="50" value=$record->title}
        {control type="html" name="body" label="Message" value=$record->body}
        {control type="buttongroup" submit="Send Email" cancel="Cancel"}
    {/form}
            
</div>
