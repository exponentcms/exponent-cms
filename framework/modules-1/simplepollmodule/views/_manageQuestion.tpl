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

<div class="module simplepoll manage-question">
	<h1>{$question->question}</h1>
	<table cellspacing="0" cellpadding="0" style="border:none;" width="100%">
		<tr><th class="header">{'Answer'|gettext}</th><th class="header"></th></tr>
		{foreach name=loop from=$answers item=answer}
			<tr><td>
				{$answer->answer}
				</td><td>
				{if $permissions.manage_answer == 1}
					<a href="{link action=edit_answer id=$answer->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'edit.png'}" title="{'Edit'|gettext}" alt="{'Edit'|gettext}" /></a>
					<a href="{link action=delete_answer id=$answer->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'delete.png'}" title="{'Delete'|gettext}" alt="{'Delete'|gettext}" /></a>
					{if $smarty.foreach.loop.first}
						<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'up.disabled.png'}" />
					{else}
						{math assign=prev equation="x-1" x=$answer->rank}
						<a href="{link action=order_switch a=$answer->rank b=$prev qid=$question->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'up.png'}" title="{'Move Item Up'|gettext}" alt="{'Move Item Up'|gettext}" /></a>
					{/if}

					{if $smarty.foreach.loop.last}
						<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'down.disabled.png'}" />
					{else}
						{math assign=next equation="x+1" x=$answer->rank}
						<a href="{link action=order_switch a=$answer->rank b=$next qid=$question->id}"><img style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'down.png'}" title="{'Move Item Down'|gettext}" alt="{'Move Item Down'|gettext}" /></a>
					{/if}
				{/if}
			</td></tr>
		{foreachelse}
			<tr><td colspan="2" align="center"><em>{'No answers found'|gettext}</em></td></tr>
		{/foreach}
	</table>
	<br />
	{if $permissions.manage_answer == 1}
		{icon class=add action=edit_answer question_id=$question->id text="New Answer"}
	{/if}
	<br />
	<a href="{link action=manage_questions}">Back to Manager</a>
</div>