{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

    {pagelinks paginate=$page top=1}
	<span class="searched_for">
	{'Your search for'|gettext} <span class="terms">"{$terms}"</span> {'returned'|gettext} <span class="result-count">{$page->total_records}</span> {'results'|gettext}<br />
	</span>
	{ecomconfig var=ecom_search_results assign=results_type}
	{if $config.is_categorized == 0}
		{foreach from=$page->records item=result}
			{*if $result->canview == 1*}
			{if empty($results_type)}
				<div class="item {cycle values="odd,even"}">
					<a href="{$smarty.const.PATH_RELATIVE}{$result->view_link}">{$result->title|highlight:$terms}</a> <span class="attribution">({$result->category}{if $user->isAdmin()}, {'Score'|gettext}:{$result->score|number_format:"2"}{/if})</span>
					{if $result->body != ""}{br}<span class="summary">{$result->body|strip_tags|truncate:240|highlight:$terms}</span>{/if}
					{clear}
				</div>
			{else}
				<div class="searchwrapper">
					<div class="prod-img search-img img-responsive">
						<a href="{link controller=store action=show title=$result->sef_url}">{img file_id=$result->expFile.mainimage[0]->id w=64}</a>
					</div>
					<div class="item {cycle values="odd,even"} searchbody">
					   <span class="searchtitle">
						   <a href="{link controller=store action=show title=$result->sef_url}">{$result->title|highlight:$terms}{if $result->model}{br}SKU: {$result->model}{/if}</a>{if $user->isAdmin()} <span class="attribution">({$result->category}, {'Score'|gettext}:{$result->score|number_format:"2"})</span>{/if}
					   </span>
					   {if $result->body != ""}
						   {br}<span class="summary">{$result->body|strip_tags|truncate:240|highlight:$terms}</span>
					   {/if}
					</div>
					<div class="searchrightcol">
						<div class="searchprice">
						{if $result->availability_type == 3}
							{'Call for Price'|gettext}
						{else}
							{if $result->base_price}
								{if $result->use_special_price}
									<div style="font-size:14px; text-decoration: line-through;">{$result->base_price|currency}</div>
									<span style="color:red;">{$result->special_price|currency}</span>
								{else}
									<span class="regular-price price">{$result->base_price|currency}</span>
								{/if}
						    {else}
						 	    <span>{'No Cost'|gettext}</span>
						    {/if}
						{/if}
						</div>
						<div style="text-align: right;">
							<a href="{link controller=store action=show title=$result->sef_url}" class="exp-ecom-link {button_style color=blue}" rel="nofollow"><strong><em>{'View Item'|gettext}</em></strong></a>
						</div>
					</div>
					{clear}
				</div>
			{/if}
			{*/if*}
		{/foreach}
	{else}{* categorized, list of crap is two levels deep *}
		{foreach from=$results key=category item=subresults}
			<h2 id="#{$category}">{$category}</h2>
			{foreach from=$subresults item=result}
				<div class="item {cycle values="odd,even"}">
					<a href="{$smarty.const.PATH_RELATIVE}{$result->view_link}">{$result->title|highlight:$terms}</a> <span class="attribution">({$result->category}{if $user->isAdmin()}, {'Score'|gettext}:{$result->score|number_format:"2"}{/if})</span>
					{if $result->body != ""}{br}<span class="summary">{$result->body|strip_tags|truncate:240|highlight:$terms}</span>{/if}
					{clear}
				</div>
			{/foreach}
		{/foreach}
	{/if}
    {pagelinks paginate=$page bottom=1}
