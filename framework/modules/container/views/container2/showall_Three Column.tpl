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
 
{css unique="container" link="`$asset_path`css/container.css"}

{/css}
 
<div class="containermodule three-column"{permissions}{if $hasParent != 0} style="border: 1px dashed darkgray;"{/if}{/permissions}>
    {viewcfile module=$singlemodule view=$singleview var=viewfile}
    <div class="col1">
    	{$container=$containers.0}
    	{$i=0}
		{$rerank=0}
    	{include file=$viewfile}
        {clear}
    </div>
    <div class="col2">
    	{$container=$containers.1}
    	{$i=1}
		{$rerank=0}
    	{include file=$viewfile}
        {clear}
    </div>
    <div class="col3">
    	{$container=$containers.2}
    	{$i=2}
    	{include file=$viewfile}
        {clear}
    </div>
    {clear}
</div>
