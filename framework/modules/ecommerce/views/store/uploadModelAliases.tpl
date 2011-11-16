{form action=uploadModelAliases controller=store}
	{if $continue}
	<h2>{"There\'s a sudden interruption on the process, you can continue by clicking it"|gettext}  <a href="{link controller=store action=deleteProcessedModelAliases}">{"here"|gettext}</a> {"or upload a new file below."|gettext}</h2>
	{/if}
	<h4>{"Upload model aliases"|gettext}</h4>
	<p>{"Excel File for the model/skus aliases"|gettext}</p>
	<input type="file" name="modelaliases" size="50">{br}{br}
	{control type="buttongroup" submit="Upload Aliases"|gettext cancel="Cancel"|gettext}
{/form}