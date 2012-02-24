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

{css unique="breadcrumb" link="`$smarty.const.PATH_RELATIVE`framework/modules-1/navigationmodule/assets/css/breadcrumb.css"}

{/css}

{assign var=i value=0}

<div class="module navigationmodule breadcrumb">
{foreach from=$sections item=section}
	{if $current->numParents >= $i && ($current->id == $section->id || $current->parents[$i] == $section->id)}
		{math equation="x+1" x=$i assign=i}
		{if $section->active == 1}
  			{if $section->id == $current->id} 
				<a class="current"
  			{else} 
				<a class="trail"
  			{/if}
  				href="{$section->link}"{if $section->new_window} target="_blank"{/if}>{makecase type=ucwords value=$section->name}</a>&nbsp;
		{else}
			<span>{makecase type=ucwords value=$section->name}</span>&nbsp;
		{/if}
		{if $section->id != $current->id}&gt;{/if}
	{/if}
{/foreach}
</div>