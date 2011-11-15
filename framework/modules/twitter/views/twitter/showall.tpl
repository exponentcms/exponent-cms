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
	{permissions}
	    <div class="module-actions">
	        {if $permissions.create == 1}
	            {icon class=add action=edit text="Add a Tweet"|gettext}
	        {/if}
	    </div>
	{/permissions}
	<dl>
		{foreach from=$items item=tweet}
			<div class="item">
				<p>
					{if $config.showimage}
						<div style="float:left;">
							{img src="`$tweet.image`" style="margin:2px 5px 2px 0px;"}
							{if $tweet.retweetedbyme}{img src="$smarty.const.URL_FULL|cat:'framework/modules/twitter/assets/images/tweeted.png'" style="position:relative;top:-37px;left:-60px;margin-right:-18px"}{/if}
						</div>
					{elseif $tweet.retweetedbyme}
						{img src="$smarty.const.URL_FULL|cat:'framework/modules/twitter/assets/images/tweeted.png'" style="float:left;margin:2px 5px 2px 0px;"}
					{/if}
					<dt><em class="date">{$tweet.created_at}{if $config.showattrib} via {$tweet.via}, {$tweet.screen_name} wrote:{/if}</em></dt>
					<dd>
						{$tweet.text}
						{permissions}
							{if $permissions.create == 1 && !$tweet.ours && !$tweet.retweetedbyme}
								&nbsp;{icon img='retweet.png' id=$tweet.id action=create_retweet title="Retweet"|gettext onclick="return confirm('"|cat:("Are you sure you want to retweet this item?"|gettext)|cat:"');"}
							{/if}
							{if $permissions.delete == 1 && $tweet.ours && !$tweet.retweeted_status}
								&nbsp;{icon class=delete id=$tweet.id action=delete_retweet}
							{/if}
						{/permissions}
					</dd>
				</p>
			</div>
			{clear}
		{/foreach}
	</dl>
	{br}
</div>
