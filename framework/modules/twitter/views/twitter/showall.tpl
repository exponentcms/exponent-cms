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
	            {icon class=add action=edit title="Add a Tweet" text="Add a Tweet"|gettext}
	        {/if}
	    </div>
	{/permissions}
	<dl>
		{foreach from=$items item=tweet}
			<div class="item">
				<p>
					{if $config.showimage}
						<div style="float:left;margin:2px 5px 2px 0px;">
							{img src="`$tweet.image`"}
							{if $tweet.retweetedbyme}{img src="`$smarty.const.URL_FULL`framework/modules/twitter/assets/images/tweeted.png" style="position: relative; top: -35px; left: -56px;"}{/if}
						</div>
					{/if}
					<dt><em class="date">{$tweet.created_at}{if $config.showattrib} via {$tweet.via}, {$tweet.screen_name} wrote:{/if}</em></dt>
					<dd>
						{$tweet.text}
						{permissions}
							{if $permissions.create == 1 && !$tweet.ours && !$tweet.retweetedbyme}
								&nbsp;{icon img='retweet.png' id=`$tweet.id` action=create_retweet title="Retweet" onclick="return confirm('Are you sure you want to retweet this item?');"}
							{/if}
							{if $permissions.delete == 1 && $tweet.ours && !$tweet.retweeted_status}
								&nbsp;{icon class=delete id=`$tweet.id` action=delete_retweet title="Delete Tweet" onclick="return confirm('Are you sure you want to delete this item?');"}
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
