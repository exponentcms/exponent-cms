{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
    {if $qname == 'error'}
        {$qtype = 'danger'}
    {elseif $qname == 'info'}
        {$qtype = 'info'}
    {elseif $qname == 'notice'}
        {$qtype = 'warning'}
    {else}
        {$qtype = 'success'}
    {/if}
    <div class="alert msg-queue alert-{$qtype}">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {foreach from=$queue item=msg}
            <div class="msg">{$msg}</div>
        {/foreach}
    </div>
{/foreach}

{script unique="alert" jquery=1 src="`$smarty.const.PATH_RELATIVE`external/bootstrap/js/bootstrap-alert.js"}
{literal}
    $(".alert").alert()
{/literal}
{/script}
{/if}