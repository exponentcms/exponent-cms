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
 
{css unique="tab-container" link=$smarty.const.PATH_RELATIVE|cat:'framework/modules/container/assets/css/container.css'}

{/css}

{uniqueid assign=tabs}

<div class="containermodule tabbed"{permissions}{if $hasParent != 0} style="border: 1px dashed darkgray;"{/if}{/permissions}>
{viewfile module=$singlemodule view=$singleview var=viewfile} 
<div id="{$tabs}" class="yui-navset exp-skin-tabview hide">
	<ul class="yui-nav">
		{foreach from=$containers item=container key=tabnum name=contain}
			{assign var=numcontainers value=$tabnum+1}
		{/foreach}
		{section name=contain loop=$numcontainers}
			{assign var=container value=$containers[$smarty.section.contain.index]}
			{assign var=containereditmode value=0}
			{if $container == null}
				{assign var=tabtitle value="(empty)"|gettext}
			{elseif $container->title == ""}
				{assign var=tabtitle value="(blank)"|gettext}
			{else}
				{assign var=tabtitle value=$container->title}
			{/if}
			{if $smarty.section.contain.first}
				<li class="selected"><a href="#tab{$smarty.section.contain.index+1}"><em>{$tabtitle}</em></a></li>
			{elseif $container != null}
				<li><a href="#tab{$smarty.section.contain.index+1}"><em>{$tabtitle}</em></a></li>
			{else}
				{permissions}
					{if ($permissions.manage == 1 || $permissions.edit == 1 || $permissions.delete == 1 || $permissions.create == 1 || $permissions.configure == 1)}
						<li><a href="#tab{$smarty.section.contain.index+1}"><em>{$tabtitle}</em></a></li>
					{/if}
				{/permissions}
			{/if}
		{/section}	
		{permissions}
			{if ($permissions.manage == 1 || $permissions.edit == 1 || $permissions.delet == 1 || $permissions.create == 1 || $permissions.configure == 1)}
				{if $smarty.section.contain.total != 0}
					<li>
				{else}
					<li class="selected">
				{/if}
				<a href="#tab{$smarty.section.contain.index+1}"><em>({'Add New'|gettext})</em></a></li>
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
				{permissions}
					{if $permissions.create == 1 && $hidebox == 0}
						<div id="tab{$smarty.section.contain.index+1}"{if !$smarty.section.contain.first}{/if}>
							<a class="addmodule" href="{link action=edit rerank=0 rank=$rank}"><span class="addtext">{'Add Module'|gettext}</span></a>
						</div>
					{/if}
				{/permissions}	
			{/if}	
		{/section}		
	</div>
</div>
</div>
<div class="loadingdiv">{'Loading'|gettext}</div>

{script unique="`$tabs`" yui3mods="1"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#{/literal}{$tabs}{literal}'});
		Y.one('#{/literal}{$tabs}{literal}').removeClass('hide');
		Y.one('.loadingdiv').remove();
	});
{/literal}
{/script}
