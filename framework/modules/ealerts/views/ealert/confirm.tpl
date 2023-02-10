{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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
    <h1>{'Subscriptions Activated'|gettext}</h1>
    <blockquote>
        {'Thank you for confirming your subscriptions.  You are now subscribed to receive email alerts for the following topics.'|gettext}
    </blockquote>

    <ul>
        {foreach from=$ealerts item=ealert}
            <li>{$ealert->ealert_title}</li>
        {/foreach}
    </ul>
</div>




