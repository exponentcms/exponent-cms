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

<div class="module simplepoll manage-questions">
    <h1>{'Manage Polling Questions'|gettext}</h1>
    {icon class=add action=edit text="New Question"|gettext}
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th class="header">{'Question'|gettext}</th>
                <th class="header">{'Active'|gettext}</th>
                <th class="header">{'Open Results'|gettext}</th>
                <th class="header">{'Open Voting'|gettext}</th>
                <th class="header">{"Actions"|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$questions item=question}
                <tr class="{cycle values='odd,even'}"><td>
                    {$question->question}
                    {*({$question->answer_count} {plural plural=answers singular=answer count=$question->answer_count})*}
                    <a href="{link action=manage_question id=$question->id}" title="{'Manage Answers'|gettext}">
                        ({$question->simplepoll_answer|@count} {plural plural=answers singular=answer count=$question->simplepoll_answer|@count})
                    </a>
                    </td><td>
                        {if $question->active}
                            <span class="active">{'Active'|gettext}</span>
                        {else}
                            <a class="inactive" href="{link action=activate id=$question->id}" title="Activate this Question"|gettext>{'Activate'|gettext}</a>
                        {/if}
                    </td><td>
                        {if $question->open_results}{'yes'|gettext}{else}{'no'|gettext}{/if}
                    </td><td>
                        {if $question->open_voting}{'yes'|gettext}{else}{'no'|gettext}{/if}
                    </td>
                    <td>
                        {icon action=edit record=$question title="Edit this Question"|gettext}
                		{icon action=delete record=$question title="Delete this Question"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this question?"|gettext)|cat:"');"}
                    </td>
                </tr>
            {foreachelse}
                <tr><td colspan="2" align="center"><em>{'No questions found'|gettext}</em></td></tr>
            {/foreach}
        </tbody>
	</table>
</div>
