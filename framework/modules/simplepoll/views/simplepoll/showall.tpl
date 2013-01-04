{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div class="module simplepoll default">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
	{if $question->simplepoll_answer|@count != 0}
		{form action="vote"}
			<h2>{$question->question}</h2>
            {permissions}
                {if $permissions.edit == 1}
                    <div class="item-actions">
                        {icon action=edit record=$question title='Edit this question'|gettext}
                    </div>
                {/if}
            {/permissions}
			<ol>
				{foreach from=$question->simplepoll_answer item=answer}
					<li>
                        {control type="radio" name="choice" label=$answer->answer value=$answer->id}
                    </li>
				{/foreach}
			</ol>
			<div class="actions">
				{if $question->open_voting}
                    {control type=buttongroup submit="Vote"|gettext}
				{else}
					{'Voting has closed for this poll'|gettext}.
				{/if}
				{if $question->open_results}
					{icon img='view.png' action=results record=$question text='Poll Results'|gettext}
				{/if}
			</div>
		{/form}
	{/if}
	{permissions}
		{if $permissions.manage == 1}
			<div class="module-actions">
				{icon class=manage action=manage_questions text="Manage Questions"|gettext}
			</div>
		{/if}
	{/permissions}
</div>