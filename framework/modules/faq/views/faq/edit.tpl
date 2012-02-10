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

<div class="module faq edit">
    <h1>{if $record->id}{'Edit'|gettext} {$record->question}{else}{'Create new FAQ'|gettext}{/if}</h1>

    {form action="update"}
        {control type="hidden" name="id" value=$record->id}
        {control type="text" name="submitter_name" label="Name of submitter"|gettext value=$record->submitter_name|default:$user->username}
        {control type="text" name="submitter_email" label="Submitter\'s Email"|gettext value=$record->submitter_email|default:$user->email}
        {control type="checkbox" name="send_email" label="Send email to user"|gettext|cat:"?" value=1}
        {control type="textarea" name="question" label="Question"|gettext value=$record->question}
        {control type="html" name="answer" label="Answer"|gettext value=$record->answer}
        {if $config.usecategories}
            {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='' OR module='`$modelname`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
        {/if}
        {control type="checkbox" name="include_in_faq" label="Post to FAQs"|gettext|cat:"?" value=1 checked=$record->include_in_faq}
        {control type="buttongroup" submit="Save FAQ"|gettext cancel="Cancel"|gettext}
    {/form} 
</div>
