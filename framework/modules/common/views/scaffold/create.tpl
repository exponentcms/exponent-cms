{if $record->id != ""}
	<h1>Edit Information for {$modelname}</h1>
{else}
	<h1>New {$modelname}</h1>
{/if}

{form action=update}
	{control name=controller type=hidden value=$controller}
	{scaffold model=$table item=$record}
{/form}
