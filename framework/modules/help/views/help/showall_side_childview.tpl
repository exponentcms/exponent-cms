{*
 * Copyright (c) 2007-2013 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

<div class="module help side-column childview">
    <ul>
        {foreach from=$page->records item=doc name=docs}
            <li{if $doc->sef_url==$smarty.get.title} class="current"{/if}>
                <a href={link action=show version=$doc->help_version->version title=$doc->sef_url src=$doc->loc->src}>{$doc->title}</a>
            </li>
        {/foreach}
    </ul>
</div>
