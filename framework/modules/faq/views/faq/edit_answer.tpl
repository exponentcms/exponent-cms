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

<div class="module faq edit-answer">
    <h1>{'Format Email Reply'|gettext}</h1>
    
    {form action="update_answer"}
        <table>
        <tr>
            <td>{'From'|gettext}: {$config.answer_from_name} &lt;{$from}&gt;</td>
        </tr>
        <tr>
            <td>{'To:'|gettext} {$faq->submitter_name} &lt;{$faq->submitter_name}&gt;</td>
        </tr>
        </table>
        {control type="hidden" name="id" value=$faq->id}
        {control type="text" name="subject" label="Subject"|gettext size="50" value=$config.answer_subject}
        {control type="html" name="body" label="Message"|gettext value=$reply}
        {control type="buttongroup" submit="Send Email"|gettext cancel="Cancel"|gettext}
    {/form}
            
</div>
