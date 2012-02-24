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

 {css unique="yui-top-nav" link="`$smarty.const.YUI3_PATH`assets/skins/sam/node-menunav.css"}

 {/css}
 {uniqueid assign=id prepend="sub`$parent`"}
<div class="module navigationmodule flydown yui3-skin-sam">
    <div id="{$id}" class="yui3-menu yui3-menu-horizontal yui3-menubuttonnav">
        <div class="yui3-menu-content" style="visibility:hidden;">
    		<ul>
    		{assign var=startdepth value=$startdepth|default:0}
    		{foreach name="children" key=key from=$sections item=section}
    		{assign var=nextkey value=$key+1}
    		{assign var=previouskey value=$key-1}

    		{if $sections[$nextkey]->depth > $section->depth}
    		    {assign var=type value="label"}
    		{elseif $sections[$nextkey]->depth <= $section->depth}
		        {assign var=type value="content"}
    		{/if}
    		

    		{if $sections[$previouskey]->depth < $section->depth && $smarty.foreach.children.first!=true}

    		<div id="{$id}-{$sections[$previouskey]->id}" class="yui3-menu">
    			<div class="yui3-menu-content">
    				<ul>

    		{/if}

            {if $type=="label"}
                <li class="{if $section->id==$current->id}current{/if}">
                    <a class="yui3-menu-label" href="{$section->link}">
                    {if $section->depth==0}
                        <em>{$section->name}</em>
                    {else}
                        {$section->name}
                    {/if}
                    </a>
            {elseif $type=="content"}
                <li class="yui3-menuitem{if $section->id==$current->id} current{/if}">
                    <a class="yui3-menuitem-content" href="{$section->link}">{$section->name}</a>
                </li>
            {/if}
            
    		{if $sections[$nextkey]->depth < $section->depth}
    			{if $smarty.foreach.children.last==true}
    				{assign var=nextdepth value=$startdepth}
    			{else}
    				{assign var=nextdepth value=$sections[$nextkey]->depth}
    			{/if}
	
    			{math equation="x-y" x=$section->depth y=$nextdepth assign=looper}

    			{section name="close" loop=$looper}
    						</li>
    					</ul>
    				</div>	
    			</div>
    			{/section}

    			</li>
    		{/if}

    		{/foreach}
    		</ul>
        </div>
    </div>
</div>

{script yui3mods=1 unique=$id}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use("node-menunav", function(Y) {
    var menu = Y.one("#{/literal}{$id}{literal}").plug(Y.Plugin.NodeMenuNav).one('.yui3-menu-content').setStyle("visibility","visible");
});


{/literal}
{/script}

