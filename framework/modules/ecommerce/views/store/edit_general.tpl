{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{control type="hidden" name="tab_loaded[general]" value=1}
{if $record->parent_id == 0}
	{control type="hidden" name="general[parent_id]" value=$record->parent_id}
	{control type="text" name="general[model]" label="Model # / SKU"|gettext value=$record->model focus=1} {* FIXME not in parent product*}
	{control type="text" class="title" name="general[title]" label="Product Name"|gettext value=$record->title}
	{control type="dropdown" name="general[companies_id]" label="Manufacturer"|gettext includeblank=true frommodel=company value=$record->companies_id}
    {icon class="manage" controller="company" action="showall" text="Manage Manufacturers"|gettext}
	{*{control type="textarea" name="general[summary]" label="Product Summary"|gettext rows=5 cols=85 value=$record->summary}*}
	{control type="editor" name="general[body]" label="Product Description"|gettext height=450 value=$record->body}
	{control type="text" class="title" name="general[feed_title]" label="Product Title for Data Feeds"|gettext value=$record->feed_title}
	{control type="textarea" name="general[feed_body]" label="Product Description for Data Feeds"|gettext rows=5 cols=85 value=$record->feed_body description="Description ONLY! - no HTML, no promotional language, no email addresses, phone numbers, or references to this website"|gettext}
	{if $product_types}
	{foreach from=$product_types key=key item=item}
		{control type="text" class="title" name="general[`$item`]" label="`$key` Product Type" value=$record->$item}
	{/foreach}
	{/if}
{else}
	{control type="text" name="general[child_rank]" label="Rank"|gettext value=$record->child_rank}
	{control type="hidden" name="general[parent_id]" value=$record->parent_id}
	{control type="hidden" name="general[product_type]" value='childProduct'}
	{control type="text" name="general[model]" label="Model # / SKU"|gettext value=$record->model}
	{control type="text" class="title" name="general[title]" label="Product Name"|gettext value=$record->title} {* FIXME not in child product*}
	{control type="dropdown" name="general[companies_id]" label="Manufacturer"|gettext includeblank=true frommodel=company value=$record->companies_id} {* FIXME not in child product*}
    {icon class="manage" controller="company" action="showall" text="Manage Manufacturers"|gettext} {* FIXME not in child product*}
	{*{control type="textarea" name="general[summary]" label="Product Summary"|gettext rows=3 cols=45 value=$record->summary}*}
	{control type="editor" name="general[body]" label="Product Description"|gettext height=250 value=$record->body} {* FIXME not in child product*}
{/if}

{script unique="general" yui3mods="node,event-custom"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.Global.fire('lazyload:cke');
    });
{/literal}
{/script}
