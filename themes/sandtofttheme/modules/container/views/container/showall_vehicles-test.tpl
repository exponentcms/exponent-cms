{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
 
<div class="containermodule full-width" style="border: 0;
 	-moz-border-radius: 5px; 
    -webkit-border-radius: 5px; 
            border-radius: 5px;	
	behavior: url(../js/PIE.php);"{permissions}{if $hasParent != 0} {/if}{/permissions}>
    {viewfile module=$singlemodule view=$singleview var=viewfile}
	
    <div class="full-width">
    	{$container=$containers.1}
    	{$i=0}
		{$rerank=0}
    	{include file=$viewfile}
		{clear}
    </div>
    
    {clear}
</div>
