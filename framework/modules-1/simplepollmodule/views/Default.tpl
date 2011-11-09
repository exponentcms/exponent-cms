{*
 *
 * Copyright (c) 2004-2011 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * Exponent is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU
 * General Public License along with Exponent; if
 * not, write to:
 *
 * Free Software Foundation, Inc.,
 * 59 Temple Place,
 * Suite 330,
 * Boston, MA 02111-1307  USA
 *
 *}

<div class="module simplepoll default">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
	
	{if $have_answers != 0}
		<form method="post" action="{$smarty.const.URL_FULL}index.php">
			<input type="hidden" name="module" value="simplepollmodule" />
			<input type="hidden" name="action" value="vote" />
			<h2>{$question->question}</h2>
			
			<ol>
				{foreach from=$answers item=answer}
					<li><input class="radio" type="radio" name="choice" value="{$answer->id}" /><span class="answer">{$answer->answer}</span>
				{/foreach}
			</ol>
			
			<div class="actions">
				{if $question->open_voting}
					<input class="awesome button {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" type="submit" value="Vote!"|gettext />
				{else}
					{'Voting has closed for this poll'|gettext}.
				{/if}
				{br}{br}
				{if $question->open_results}
					<a href="{link action=results id=$question->id}">{'Results'|gettext}</a>
				{/if}
			</div>
		</form>
	{/if}
	
	{permissions level=$smarty.const.UILEVEL_NORMAL}
		{if $permissions.manage_question == 1 || $permissions.manage_answer == 1}
			<div class="module-actions">
				{icon class=manage action=manage_questions text="Manage Questions"|gettext}
			</div>
		{/if}
	{/permissions}
	
</div>