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

<div class="scaffold browseTags">
	<h1>{$moduletitle|default:""}</h1>

    {foreach from=$tags item=tag names=tags}
        <a href="{link action=browseTags tags=$tag->id}">{$tag->title}</a>
    {/foreach}
</div>
