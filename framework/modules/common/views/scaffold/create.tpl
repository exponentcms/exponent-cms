{if $record->id != ""}
	<h1>{'Edit Information for'|gettext} {$modelname}</h1>
{else}
	<h1>{'New'|gettext} {$modelname}</h1>
{/if}

{form action=update}
	{control name=controller type=hidden value=$controller}
	{scaffold model=$table item=$record}
{/form}
