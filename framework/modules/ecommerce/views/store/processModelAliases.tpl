{if $error}
<span style="color: red">{$error}</span>
{/if}
{br}
{form action=saveModelAliases controller=store}
	{control type="hidden" name="index" value=$index}
	{if $autocomplete}
		{control type="autocomplete" controller="store" action="search" name="product_title" label="Add a new item" value="Search title or SKU to add an item" schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" jsinject=$callbacks}
		{control type="buttongroup" submit="Save" cancel="Cancel"}
	{/if}
{/form}

{form action=processModelAliases controller=store}
	{control type="hidden" name="index" value=$index}
	{control type="hidden" name="next" value='1'}
	{if $autocomplete}
		{control type="buttongroup" submit="Next"}
	{else}
		{control type="buttongroup" submit="Next" cancel="Cancel"}
	{/if}
{/form}
<h3 style="float: right;">{$count} model aliases left.</h3>
{clear}
<style type="text/css">
{literal}

	#product_title, #resultsproduct_title {
		width: 450px;
	}
	
{/literal}
</style>
