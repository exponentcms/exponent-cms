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

<div class="module ealerts pending">
    <h1>{'Account Settings Pending'|gettext}</h1>
    <p>
        {'Your request to modify your e-Alert settings has been received.'|gettext}&nbsp;&nbsp;
        {'A confirmation email has been sent to'|gettext} {$subscriber->email}.&nbsp;&nbsp;
        {'Once you have followed the instructions listed in the email you will start receiving email alerts for the following topics:'|gettext}
    </p>
    
    <ul>
    {foreach from=$ealerts item=ealert}
        <li>{$ealert->ealert_title}</li>
    {/foreach}
    </ul>
</div>




