{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="module simplepoll results">
	<h1>{'Polling Results'|gettext}</h1>
    <h2>{$question->question}</h2>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th class="header">{'Answer'|gettext}</th>
                <th class="header">{'Count'|gettext}</th>
                <th class="header">{'%'|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach name=loop from=$question->simplepoll_answer item=answer}
                {if $smarty.foreach.loop.first}
                    <tr>
                        <td><strong>{$answer->answer}</strong></td>
                        <td><strong>{$answer->vote_count}/{$vote_total}</strong></td>
                        <td><strong>
                            {$percentage=$answer->vote_count / $vote_total * 100}
                            {$percentage|round:2}%
                        </strong></td>
                    </tr>
                {else}
                    <tr>
                        <td>{$answer->answer}</td>
                        <td>{$answer->vote_count}/{$vote_total}</td>
                        <td>
                            {$percentage=$answer->vote_count / $vote_total * 100}
                            {$percentage|round:2}%
                        </td>
                    </tr>
                {/if}
            {/foreach}
        </tbody>
	</table>
</div>