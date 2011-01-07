{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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

<div class="module navigationmodule children-only">
    <h2>{if $current->id==1}Menu{else}{$current->name}{/if}</h2>         
    <ul>
        {assign var=islastdepth value="false"}
        {foreach from=$sections item=section}
            {if $section->parent == $current->id}
            {assign var=islastdepth value="true"}
                 {if $section->public == 1}
                 	{if $current->id==1 && $section->id==1}
                 	{else}
                 	<li{if $section->id==$current->id || $isparent==1} class="current"{/if}>
                    	{if $section->active == 1}
                           <a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>
                    	{else}
                           <span class="navlink">{$section->name}</span>&nbsp;
           			    {/if}
    			    {/if}
                   </li>
                {/if}
            {/if}
        {/foreach}
        
        
        {if $islastdepth=="false"}
        {foreach from=$sections item=section}
            {if $section->parent == $current->parent}
            	{if $section->public == 1}
            	{if $current->id==1 && $section->id==1}
            	{else}
                 	<li{if $section->id==$current->id || $isparent==1} class="current"{/if}>
                        {if $section->active == 1}
                             <a href="{$section->link}" class="navlink {if $section->id==$current->id || $isparent==1}current{/if}"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>
                        {else}
                              <span class="navlink">{$section->name}</span>&nbsp;
                        {/if}
                    </li>   
             		{/if}
         		{/if}
         	{/if}
        {/foreach}
        {/if}
    </ul>
</div>