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
 * @copyright  2004-2018 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @link       http://www.exponent-docs.org/
 *}{*
  * Copyright (c) 2004-2018 OIC Group, Inc.
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
 	{if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h2>{$moduletitle}</h2>{/if}
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
     {if empty($config.consumer_key) && $permissions.configure}
         {permissions}
             <div class="module-actions">
                 <div class="msg-queue notice" style="text-align:center">
                     <p>{'You MUST configure this module to use it!'|gettext} {icon action="configure"}</p>
                 </div>
             </div>
         {/permissions}
     {/if}
     {if $config.enable_follow && $config.twitter_user}
         <a href="https://twitter.com/{$config.twitter_user}" class="twitter-follow-button" data-show-count="false" data-show-screen-name="{if $config.hideuser}false{else}true{/if}" data-lang="en">{'Follow'|gettext} @{$config.twitter_user}</a>
         {script unique='tweet_src'}
         {literal}
             !function(d,s,id){
                 var js,fjs=d.getElementsByTagName(s)[0];
                 if(!d.getElementById(id)){
                     js=d.createElement(s);
                     js.id=id;
                     js.src="https://platform.twitter.com/widgets.js";
                     fjs.parentNode.insertBefore(js,fjs);
                 }
             }(document,"script","twitter-wjs");
         {/literal}
         {/script}
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
                             &#160;{icon class=delete id=$tweet.id action=delete_tweet}
                         {/if}
                     {/permissions}
                 </dd>
 			</div>
 			{clear}
 		{/foreach}
 	</dl>
 </div>
