<div class="module simplepoll results">
	<div class="moduletitle">{$question->question} </div>
	<hr size="1" />
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		{foreach name=loop from=$answers item=answer}
			{if $smarty.foreach.loop.first}
				<tr>
					<td><b>{$answer->answer}</b></td>
					<td><b>{$answer->vote_count}/{$vote_total}</b></td>
					<td><b>
						{math assign=percentage equation="x / y * 100" x=$answer->vote_count y=$vote_total}
						{$percentage|round:2}%
					</b></td>
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