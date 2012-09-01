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

<div class="module users extension subscriptions">
    <h3>{'You are currently subscribed to receive email alerts for the following topics'|gettext}</h3>
    {foreach from=$edit_user->expeAlerts item=ealert}
        {control type=checkbox name="expeAlert[]" value=$ealert->id label=$ealert->module|getcontrollerdisplayname|cat:' - '|cat:$ealert->ealert_title checked=true}
    {foreachelse}
        <p><em>{'No email alert subscriptions were found'|gettext}</em></p>
    {/foreach}
</div>


