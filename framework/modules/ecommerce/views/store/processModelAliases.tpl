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

{if $error}
<span style="color: red">{$error}</span>
{/if}
{br}
{form action=saveModelAliases controller=store}
	{control type="hidden" name="index" value=$index}
	{if $autocomplete}
		{control type="autocomplete" controller="store" action="search" name="product_title" label="Add a new item"|gettext value="Search title or SKU to add an item" schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" jsinject=$callbacks}
		{control type="buttongroup" submit="Save" cancel="Cancel"}
	{/if}
{/form}

{form action=processModelAliases controller=store}
	{control type="hidden" name="index" value=$index}
	{control type="hidden" name="next" value='1'}
	{if $autocomplete}
		{control type="buttongroup" submit="Next"}
	{else}
		{control type="buttongroup" submit="Next"|gettext cancel="Cancel"|gettext}
	{/if}
{/form}
<h3 style="float: right;">{$count} {'model aliases left'|gettext}.</h3>
{clear}

{css unique="processModelAliases"}
{literal}
	#product_title, #resultsproduct_title {
		width: 450px;
	}
{/literal}
{/css}