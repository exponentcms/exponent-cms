{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
    {if $smarty.const.DEVELOPMENT}
        <h4>{'This is the scaffold view'|gettext}</h4>
    {/if}
    <h1>{$moduletitle|default:"Browse Tags for"|gettext|cat:" `$model_name`"}</h1>

    {foreach from=$tags item=tag names=tags}
        <a href="{link action=browseTags tags=$tag->id}">{$tag->title}</a>
    {/foreach}
</div>
