{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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

<div class="events email rsvp">
    {group label="RSVP to this Event"|gettext}
        {form name=send_feedback|cat:$event->id action=send_feedback}
            {if !empty($event->event->title)}
                {$title = $event->event->title}
            {elseif !empty($event->title)}
                {$title = $event->title}
            {else}
                {$title = ''}
            {/if}
            {control type=hidden name=formname value=$feedback_form}
            {control type=hidden name=id value=$event->id}
            {control type=hidden name=subject value="RSVP for `$title`"}
            {control type=text name=name label="Your Name"|gettext}
            {control type=email name=email label="Your Email Address"|gettext}
            {control type=tel name=phone label="Your Phone"|gettext}
            {control type=text name=attendees label="Number of Attendees"|gettext}
            {control type="textarea" name="comments" label='Comments'|gettext}
            {control type=antispam}
            {control type=buttongroup submit="Send"|gettext}
        {/form}
    {/group}
</div>
