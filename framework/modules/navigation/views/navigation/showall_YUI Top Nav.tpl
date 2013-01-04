{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{nocache}
{css unique="yui-top-nav" link="`$smarty.const.YUI2_RELATIVE`assets/skins/sam/menu.css"}

{/css}
{uniqueid assign=id prepend="sub`$parent`"}
<div class="module navigation yui-top-nav yui-skin-sam">
    <div id="{$id}" class="yuimenubar yuimenubarnav">
    	<div class="bd">
    		<ul class="first-of-type">
                {$startdepth=$startdepth|default:0}
                {foreach name="children" key=key from=$sections item=section}
                    {$nextkey=$key+1}
                    {$previouskey=$key-1}

                    {if $sections[$previouskey]->depth < $section->depth && $smarty.foreach.children.first!=true}

                    <div id="childfly_{$key}_{$section->id}" class="yuimenu">
                        <div class="bd">
                            <ul>

                    {/if}

                    <li class="{if $section->depth == 0}yuimenubaritem{else}yuimenuitem{/if}{if $section->id==$current->id} current{/if}{if $section->active == 1} {/if}">
                    <a class="{if $section->depth == 0}yuimenubaritemlabel{else}yuimenuitemlabel{/if}" href="{if $section->active == 1}{$section->link}{else}#{/if}" {if $section->new_window} target="_blank"{/if}>{if !empty($section->expFile[0]->id)}{img file_id=$section->expFile[0]->id w=16 h=16} {/if}{$section->name}</a>
                    {if $sections[$nextkey]->depth == $section->depth}</li>{/if}

                    {if $sections[$nextkey]->depth < $section->depth}
                        {if $smarty.foreach.children.last==true}
                            {$nextdepth=$startdepth}
                        {else}
                            {$nextdepth=$sections[$nextkey]->depth}
                        {/if}

                        {$looper=$section->depth-$nextdepth}

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
{/nocache}