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

<div class="module faq ask-question">
    {if !$config.hidemoduletitle}<h1>{$moduletitle|default:"Ask a Question"|gettext}</h1>{/if}
	{permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit text="Add a New FAQ"|gettext}
			{/if}
			{br}
			{if $permissions.manage == 1}
				{icon action=manage text="Manage FAQs"|gettext}
			{/if}
		</div>
	{/permissions}
    {form action=submit_question}
        {*{control type="text" name="submitter_name" label="Your Name"|gettext value=$record->submitter_name}*}
        {*{control type="text" name="submitter_email" label="Your Email Address"|gettext value=$record->submitter_email}*}
        {if $user->id == 0 || $comment->id }
  	        {control type=text name="submitter_name" label="Your Name"|gettext required=true value=$record->submitter_name}
  		    {control type=text name="submitter_email" label="Your Email Address"|gettext required=true value=$record->submitter_email}
  		{else}
            {control type=text name="submitter_name" disabled=1 label="Your Name"|gettext value="`$user->firstname` `$user->lastname`"}
      	    {control type=text name="submitter_email" disabled=1 label="Your Email Address"|gettext value=$user->email}
  		{/if}
        {control type="textarea" name="question" label="Your Question"|gettext value=$record->question}
        {control type="buttongroup" submit="Submit Question"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
