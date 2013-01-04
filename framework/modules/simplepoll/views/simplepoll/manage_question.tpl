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

{css unique='simplepoll' corecss='admin-global,tables'}

{/css}

<div class="module simplepoll manage-question">
    <h1>{'Manage Polling Question'|gettext}</h1>
	<h2>{$question->question}</h2>
    {icon action=edit record=$question title='Edit the question'|gettext}
    {permissions}
        <div class="module-actions">
            {if $permissions.edit == 1}
                {ddrerank module="simplepoll_answer" model="simplepoll_answer" where="simplepoll_question_id=`$question->id`" sortfield="answer" label="Poll Question Answers"|gettext}
            {/if}
        </div>
    {/permissions}
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
		<thead>
            <tr>
                <th class="header">{'Answer'|gettext}</th>
                <th class="header">{"Actions"|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach name=loop from=$question->simplepoll_answer item=answer}
                <tr>
                    <td>
                        {$answer->answer}
                    </td>
                    <td>
                        {if $permissions.edit == 1}
                            {icon class=edit action=edit_answer record=$answer title='Edit this answer'|gettext}
                            {icon class=delete action=delete_answer record=$answer title='Delete this answer'|gettext}
                        {/if}
                    </td>
                </tr>
            {foreachelse}
                <tr><td colspan="2" align="center"><em>{'No answers found'|gettext}</em></td></tr>
            {/foreach}
        </tbody>
	</table>
	{if $permissions.create == 1}
		{icon class=add action=edit_answer rank=$answer->rank+1 question_id=$question->id text="New Answer"}
	{/if}
	<br />
	<a href="{link action=manage_questions}">{'Back to Manager'|gettext}</a>
</div>