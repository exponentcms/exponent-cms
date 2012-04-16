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

<div class="module simplepoll manage-questions">
	<table cellpadding="0" cellspacing="0" style="border:none;" width="100%">
		<tr><th class="header">{'Question'|gettext}</th>
			<th class="header">{'Active'|gettext}?</th>
			<th class="header">{'Open Results?'|gettext}</th>
			<th class="header">{'Open Voting?'|gettext}</th>
			<th class="header"></th>
		</tr>
		{foreach from=$questions item=question}
			<tr class="row {cycle values='odd_row,even_row'}"><td>
				<a href="{link action=manage_question id=$question->id}">{$question->question}</a>
				({$question->answer_count} {plural plural=answers singular=answer count=$question->answer_count})
				</td><td>
					{if $question->is_active}{'yes'|gettext}{else}{'no'|gettext}{/if}
				</td><td>
					{if $question->open_results}{'yes'|gettext}{else}{'no'|gettext}{/if}
				</td><td>
					{if $question->open_voting}{'yes'|gettext}{else}{'no'|gettext}{/if}
				</td><td>
				{if $question->is_active}
					<a href="{link action=activate_question id=$question->id activate=0}">{'Deactivate'|gettext}</a>
				{else}
					<a href="{link action=activate_question id=$question->id activate=1}">{'Activate'|gettext}</a>
				{/if}
				<a href="{link action=edit_question id=$question->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'edit.png'}" title="{'Edit'|gettext}" alt="{'Edit'|gettext}" /></a>
				<a href="{link action=delete_question id=$question->id}" onclick="return confirm('Are you sure you want to delete this question and all associated answers / responses?');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'delete.png'}" title="{'Delete'|gettext}" alt="'Delete'|gettext}" /></a>
			</td></tr>
		{foreachelse}
			<tr><td colspan="2" align="center"><em>{'No questions found'|gettext}</em></td></tr>
		{/foreach}
	</table>
	<br />
	{icon class=add action=edit_question text="New Question"|gettext}
</div>
