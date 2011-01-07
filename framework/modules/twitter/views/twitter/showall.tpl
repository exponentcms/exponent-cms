{*
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @copyright  2004-2009 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @link       http://www.exponent-docs.org/
 *}

<div class="module twitter showall">
	{if $moduletitle}<h2>{$moduletitle}</h2>{/if}
	<dl>
	{foreach from=$items item=tweet}
		<dt>{$tweet.created_at|date_format:"%A, %B %e, %Y %l:%M %p"}</dt>
		<dd>{$tweet.text}</dd>
	{/foreach}
	</dl>
</div>