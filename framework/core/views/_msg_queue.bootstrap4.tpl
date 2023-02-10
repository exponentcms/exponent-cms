{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{if $queues|@count!=0}
{foreach from=$queues item=queue key=qname}
    {* test for and convert non-twitter bootstrap names *}
    {if $qname == 'error' || $qname == 'danger'}
        {$qtype = 'danger'}
    {elseif $qname == 'info'}
        {$qtype = 'info'}
    {elseif $qname == 'notice' || $qname == 'warning'}
        {$qtype = 'warning'}
    {else}
        {$qtype = 'success'}
    {/if}
    <div class="alert alert-{$qtype} alert-dismissible fade show" role="alert">
        {foreach from=$queue item=msg}
            <div class="msg">{$msg}</div>
        {/foreach}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
{/foreach}

{script unique="alert" jquery=1 bootstrap="alert"}

{/script}
{/if}