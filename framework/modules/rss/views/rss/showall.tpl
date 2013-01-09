{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div class="module rss showall">
    {if !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle|default:"RSS Feeds"|gettext}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {foreach from=$feeds item=feed}
		<div class="item">
			{*<a class="rsslink" href="{rsslink}" title="{$feed->feed_desc}">{'Subscribe to'|gettext} {$feed->feed_title}</a>*}
            {*<a class="rsslink" href="{rsslink}" title="{$feed->feed_desc}">{'Subscribe to'|gettext} {$feed->title}</a>*}
            {rss_link feed=$feed text=$feed->title}
		</div>
    {/foreach}    
</div>
