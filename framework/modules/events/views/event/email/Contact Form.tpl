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

<div class="events email contact">
    {group label="Submit Feedback"|gettext}
        {form action=send_feedback}
            {control type=hidden name=formname value=$feedback_form}
            {control type=hidden name=id value=$event->id}
            {control type=email name=email label="Your Email Address"|gettext}
            {control type=text name=subject label="Subject"|gettext}
            {control type="textarea" name="message" label='Message'|gettext}
            {control type=buttongroup submit="Send"|gettext}
        {/form}
    {/group}
</div>
