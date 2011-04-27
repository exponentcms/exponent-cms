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

<div class="module faq ask-question">
    <h1>{$moduletitle|default:"Ask a Question"}</h1>
    
    {form action=submit_question}
        {control type="text" name="submitter_name" label="Your Name" value=$record->submitter_name}
        {control type="text" name="submitter_email" label="Your Email Address" value=$record->submitter_email}      
        {control type="textarea" name="question" label="Question" value=$record->question}
        {control type="buttongroup" submit="Submit Question" cancel="Cancel"}
    {/form}
</div>
