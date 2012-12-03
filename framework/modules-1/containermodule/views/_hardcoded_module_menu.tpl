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

{permissions}
	<div class="exp-container-module-wrapper">
		<div class="container-chrome module-chrome hardcoded-chrome">
			<a href="#" class="trigger" title="{$container->info.module|gettext}">{$container->info.module|gettext} ({if $container->info.scope == 'top-sectional'}{'Top'|gettext}{else}{$container->info.scope|gettext}{/if})</a>
            {nocache}{getchromemenu module=$container rank=$i rerank=$rerank last=$last}{/nocache}
		</div>
	</div>
{/permissions}