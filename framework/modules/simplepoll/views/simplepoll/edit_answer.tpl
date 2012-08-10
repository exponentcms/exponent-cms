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

 <div class="form_header">
    {if $answer->id != ""}
        <h1>{'Editing Poll Answer'|gettext}</h1>
    {else}
        <h1>{'New Poll Answer'|gettext}</h1>
    {/if}
    <h2>{'for Question'|gettext}: {$question->question}</h2>
    {form action=update_answer}
        {control type=hidden name=id value=$answer->id}
        {control type=hidden name=simplepoll_question_id value=$question->id}
        {control type=hidden name=rank value=$answer->rank}
        {control type=hidden name=vote_count value=$answer->vote_count}
        {control type=html name=answer label="Answer"|gettext value=$answer->answer|escape:"html"}
        {control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

