{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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

{if $is_email == 1}
    <style type="text/css">
        {$css}
    </style>
{else}
    {css unique="custom-report" corecss="tables,button"}

    {/css}
{/if}
<div class="module forms show report">

    {eval var=$template}

</div>
{if $is_email == 0}
    {*<a class="{button_style}" href="{$backlink}">{'Back'|gettext}</a>*}
    {icon button=true link=$backlink text='Back'|gettext}
{/if}