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

<div class="module simplepoll results">
	<h1>{$question->question} </h1>
	<hr size="1" />
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		{foreach name=loop from=$answers item=answer}
			{if $smarty.foreach.loop.first}
				<tr>
					<td><strong>{$answer->answer}</strong></td>
					<td><strong>{$answer->vote_count}/{$vote_total}</strong></td>
					<td><strong>
						{math assign=percentage equation="x / y * 100" x=$answer->vote_count y=$vote_total}
						{$percentage|round:2}%
					</strong></td>
				</tr>
			{else}
				<tr>
					<td>{$answer->answer}</td>
					<td>{$answer->vote_count}/{$vote_total}</td>
					<td>
						{math assign=percentage equation="x / y * 100" x=$answer->vote_count y=$vote_total}
						{$percentage|round:2}%
					</td>
				</tr>
			{/if}
		{/foreach}
	</table>
</div>