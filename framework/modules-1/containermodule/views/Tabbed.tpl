{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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
 
{css unique="tab-container" link="`$smarty.const.PATH_RELATIVE`framework/modules/container/assets/css/container.css"}

{/css}

{uniqueid assign=tabs}

<div class="containermodule tabbed yui3-skin-sam">
{viewfile module=$singlemodule view=$singleview var=viewfile} 
<div id="{$tabs}" class="yui-navset hide">
	<ul class="yui-nav">
		{foreach from=$containers item=container key=tabnum name=contain}
			{assign var=numcontainers value=$tabnum+1}
		{/foreach}
		{section name=contain loop=$numcontainers}
			{assign var=container value=$containers[$smarty.section.contain.index]}
			{assign var=containereditmode value=0}
			{if $container == null}
				{assign var=tabtitle value="(empty)"}
			{elseif $container->title == ""}
				{assign var=tabtitle value="(blank)"}
			{else}
				{assign var=tabtitle value=$container->title}
			{/if}
			{if $smarty.section.contain.first}
				<li class="selected"><a href="#tab{$smarty.section.contain.index+1}"><em>{$tabtitle}</em></a></li>
			{elseif $container != null}
				<li><a href="#tab{$smarty.section.contain.index+1}"><em>{$tabtitle}</em></a></li>
			{else}
				{permissions level=$smarty.const.UILEVEL_STRUCTURE}
					{if ($permissions.administrate == 1 || $permissions.edit_module == 1 || $permissions.delete_module == 1 || $permissions.add_module == 1)}
						<li><a href="#tab{$smarty.section.contain.index+1}"><em>{$tabtitle}</em></a></li>
					{/if}
				{/permissions}
			{/if}
		{/section}	
		{permissions level=$smarty.const.UILEVEL_STRUCTURE}
			{if ($permissions.administrate == 1 || $permissions.edit_module == 1 || $permissions.delete_module == 1 || $permissions.add_module == 1)}
				{if $smarty.section.contain.total != 0}
					<li>
				{else}
					<li class="selected">
				{/if}
				<a href="#tab{$smarty.section.contain.index+1}"><em>(Add New)</em></a></li>
			{/if}
		{/permissions}		
	</ul>            
	<div class="yui-content">
		{section name=contain loop=$numcontainers+1}	
			{assign var=container value=$containers[$smarty.section.contain.index]}
			{assign var=rank value=$smarty.section.contain.index}
			{assign var=menurank value=$rank+1}
			{assign var=index value=$smarty.section.contain.index}
			{if $container != null}	
				<div id="tab{$smarty.section.contain.index+1}"{if !$smarty.section.contain.first}{/if}>
					{assign var=container value=$containers.$index}
					{assign var=i value=$menurank}
					{assign var=rerank value=0}
					{include file=$viewfile}
				</div>
			{else}
				{permissions level=$smarty.const.UILEVEL_STRUCTURE}
					{if $permissions.add_module == 1 && $hidebox == 0}
						<div id="tab{$smarty.section.contain.index+1}"{if !$smarty.section.contain.first}{/if}>
							<a class="addmodule" href="{link action=edit rerank=0 rank=$rank}"><span class="addtext">Add Module</span></a>
						</div>
					{/if}
				{/permissions}	
			{/if}	
		{/section}		
	</div>
</div>
</div>
<div class="loadingdiv">Loading</div>

{script unique="`$tabs`" yui3mods="1"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
    var tabview = new Y.TabView({srcNode:'#{/literal}{$tabs}{literal}'});
    tabview.render();
	Y.one('#{/literal}{$tabs}{literal}').removeClass('hide');
	Y.one('.loadingdiv').remove();
});
{/literal}
{/script}
