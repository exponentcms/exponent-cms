{form action=uploadModelAliases controller=store}
	{if $continue}
	<h2>There's a sudden interruption on the process, you can continue by clicking it  <a href="{link controller=store action=deleteProcessedModelAliases}">here</a> or upload a new file below.</h2>
	{/if}
	<h4>{gettext str="Upload model aliases"}</h4>
	<p>{gettext str="Excel File for the model/skus aliases"}</p>
	<input type="file" name="modelaliases" size="50">{br}{br}
	{control type="buttongroup" submit="Upload Aliases"|gettext cancel="Cancel"|gettext}
{/form}