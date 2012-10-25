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

<div class="module twitter showall">
	{if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
	{permissions}
	    <div class="module-actions">
	        {if $permissions.create == 1}
	            {icon class=add action=edit text="Add a Tweet"|gettext}
	        {/if}
	    </div>
	{/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
	<dl>
		{foreach from=$items item=tweet}
			<div class="item">
					{if $config.showimage}
						<div style="float:left;">
							{img src="`$tweet.image`" style="margin:2px 5px 100% 0px;"}
							{if $tweet.retweetedbyme}{img src="`$smarty.const.PATH_RELATIVE`framework/modules/twitter/assets/images/tweeted.png" style="position:relative;top:-37px;left:-60px;margin-right:-18px"}{/if}
						</div>
					{elseif $tweet.retweetedbyme}
						{img src="`$smarty.const.PATH_RELATIVE`framework/modules/twitter/assets/images/tweeted.png" style="float:left; margin:2px 5px 100% 0px;"}
					{/if}
					<dt><em class="date">{$tweet.created_at|relative_date}{if $config.showattrib} {'via'|gettext} {$tweet.via}, {$tweet.screen_name} {'wrote'|gettext}:{/if}</em></dt>
					<dd>
						{$tweet.text}
						{permissions}
							{if $permissions.create == 1 && !$tweet.ours && !$tweet.retweetedbyme}
								&#160;{icon img='retweet.png' id=$tweet.id action=create_retweet title="Retweet"|gettext onclick="return confirm('"|cat:("Are you sure you want to retweet this item?"|gettext)|cat:"');"}
							{/if}
							{if $permissions.delete == 1 && $tweet.ours && !$tweet.retweeted_status}
								&#160;{icon class=delete id=$tweet.id action=delete_retweet}
							{/if}
						{/permissions}
					</dd>
			</div>
			{clear}
		{/foreach}
	</dl>
</div>
