{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Phillip Ball and  Adam Kessler
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
 *{math equation="x*20" x=$section->depth}
 *}

{css unique="yui-top-nav" link="`$smarty.const.YUI2_PATH`assets/skins/sam/menu.css"}

{/css}
{uniqueid assign=id prepend="sub`$parent`"}

<div class="module navigation yui-top-nav yui-skin-sam">
    <div id="{$id}" class="yuimenubar yuimenubarnav">
    	<div class="bd">
    		<ul class="first-of-type">
                {assign var=startdepth value=$startdepth|default:0}
                {foreach name="children" key=key from=$sections item=section}
                    {assign var=nextkey value=$key+1}
                    {assign var=previouskey value=$key-1}

                    {if $sections[$previouskey]->depth < $section->depth && $smarty.foreach.children.first!=true}

                    <div id="childfly_{$key}_{$section->id}" class="yuimenu">
                        <div class="bd">
                            <ul>

                    {/if}

                    <li class="{if $section->depth == 0}yuimenubaritem{else}yuimenuitem{/if}{if $section->id==$current->id} current{/if}{if $section->active == 1} {/if}">
                    <a class="{if $section->depth == 0}yuimenubaritemlabel{else}yuimenuitemlabel{/if}" href="{$section->link}">{$section->name}</a>
                    {if $sections[$nextkey]->depth == $section->depth}</li>{/if}


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
YUI(EXPONENT.YUI3_CONFIG).use('yui2-container','yui2-menu', function(Y) {
    var YAHOO=Y.YUI2;

	var menubar = new YAHOO.widget.MenuBar(
		                "{/literal}{$id}{literal}", 
						{
							hidedelay: 750, 
							lazyload: true,
							autosubmenudisplay: true
						}
					);
	menubar.render(); 
});


{/literal}
{/script}

