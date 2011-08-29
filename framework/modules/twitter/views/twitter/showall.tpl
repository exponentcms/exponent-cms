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
 * @copyright  2004-2011 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @link       http://www.exponent-docs.org/
 *}

<div class="module twitter showall">
	{if $moduletitle}<h2>{$moduletitle}</h2>{/if}
	<dl>
		{foreach from=$items item=tweet}
			<div class="item">
				<p>
					{if $config.showimage}{img src=`$tweet.image` style="float:left;;margin:0 5px 0 0;"}{/if}
					<dt><em class="date">On {$tweet.created_at|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}{if $config.showattrib} via {$tweet.via}, {$tweet.screen_name} wrote:{/if}</em></dt>
					<dd>{$tweet.text}</dd>
				</p>
			</div>
		{/foreach}
	</dl>
	{br}
</div>