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

{if $container->info.clickable}
<div id="module{$container->id}" class="container_modulewrapper">
	<div class="container_moduleheader">
		<a style="text-align:center;display:block;color:#114;font-size:11px;padding-top:2px" href="{$dest|replace:"?":"index.php?"}&ss={$container->info.source}&sm={$container->info.class}">
			* {$container->info.module} - {$_TR.use_this_content} *
		</a>
	</div>
	{$container->output}
</div>
{else}
	{$container->output}
{/if}
