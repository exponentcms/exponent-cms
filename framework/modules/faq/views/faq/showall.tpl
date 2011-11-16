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

<div class="module faq showall">
    <h1>{$moduletitle|default:"Frequently Asked Questions"|gettext}</h1>
    {permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=create text="Add a New FAQ"|gettext}
			{/if}
			{br}
			{if $permissions.manage == 1}
				{icon action=manage text="Manage FAQs"|gettext}
			{/if}
		</div>
    {/permissions}    
    
    {if $config.allow_user_questions}
        <a href="{link action="ask_question"}">{'Ask a question'|gettext}</a>
    {/if}
    
    {if $config.use_toc}
        <ol>
        {foreach from=$questions item=question}
            <li><em><a href="#faq_{$question->id}">{$question->question}</a></em></li>
        {/foreach}
        </ol>
    {/if}
    
    {foreach from=$questions item=question}        
        <div>
            <a name="faq_{$question->id}"></a>
            <h3>{$question->question}</h3>
			<div class="bodycopy">
				<p>{$question->answer}</p>
			</div>
            <span class="editicons">
                {permissions}
					<div class="item-actions">
						{if $permissions.edit == 1}
							{icon action=edit record=$question title="Edit FAQ"|gettext}
						{/if}
						{if $permissions.delete == 1}
							{icon action=delete record=$question title="Delete this FAQ"|gettext|cat:"?" onclick="return confirm('"|cat:("Are you sure you want to delete this FAQ?"|gettext)|cat:"');"}
						{/if}                  
					</div>
                {/permissions}
            </span>
        </div>
    {foreachelse}
        <em>{'There are currently no FAQ\'s'|gettext}</em>
    {/foreach}
</div>
