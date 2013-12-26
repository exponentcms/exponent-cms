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

{if $container->info.clickable}
<div id="module{$container->id}" class="exp-container-module-wrapper">
	<div class="container-chrome module-chrome">
		{* <a style="text-align:center;display:block;font-size:11px;padding-top:2px" href="{$dest}&ss={$container->info.source}&sm={$container->info.class}"> *}
		<a style="text-align:center;display:block;text-decoration:none;font-weight:bold;text-transform:uppercase;font-size:11px;
					text-shadow: 0px -1px 0px #374683;padding:1px 15px 0 5px;top:0px; left:5px;line-height:15px;color:#fff;"
					href="{$dest}&cid={$container->id}">
			* {$container->info.module} - {'Link to this Module'|gettext} *
		</a>
	</div>
	{$container->output}
</div>
{else}
	{$container->output}
{/if}
