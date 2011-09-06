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

<div class="module login stacked">
    {if $loggedin == false}
		{if $moduletitle}
			<h2>{$moduletitle}</h2>
		{/if}
		<div>
			{assign var=usertype value="Users"}
			{assign var=label value="Username:"}
			{form action=login}
				{control type="text" name="username" label=$label size=25}
				{control type="password" name="password" label="Password:" size=25}
				{control type="buttongroup" submit="Login Now!"}
			{/form}
		</div>
	{else}
		<h2>{$displayname}</h2>
		<div class="bodycopy">
			<ul>
				<li><a href="{link action=logout}">{"Log Out"|gettext}</a></li>
			</ul>
		</div>
    {/if}
</div>





