{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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
{if $ancestors|@count>0}
<div class="module store category-breadcrumb">
    {foreach from=$ancestors item=ancestor name=path}
        <a href="{link controller=store action=showall title=$ancestor->sef_url}">{$ancestor->title}</a>
        {*if $smarty.foreach.path.last}{br}{else}>&nbsp;{/if*}
    {/foreach}
</div>
{/if}