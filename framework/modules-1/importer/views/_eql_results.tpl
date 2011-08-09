{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

{if $success == 1}
	<h2>{$_TR.success}</h2>
{else}
	<h2>{$_TR.failure}</h2>
	<div style='padding-left: 25px;'>
		{foreach from=$errors item=error}
			{$error}<br />
		{/foreach}
	</div>
{/if}