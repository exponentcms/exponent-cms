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

{css unique="install-upgrades"}
{literal}
    .install-upgrades h3 {
        display       : inline;
    }
    .success {
        color      : green;
        font-style : italic;
    }

    .warning {
        color      : orange;
        font-style : italic;
    }

    .failed, .critical {
        color      : red;
        font-style : italic;
        font-weight   : bold;
    }

{/literal}
{/css}

<div class="module administration install-upgrades">
    <h1>{'Upgrade Scripts Results'|gettext}</h1>
    <blockquote>
        {'The results of running selected upgrade scripts.'|gettext}
    </blockquote>
    <ol>
        {foreach from=$scripts item=upgradescript key=name}
            <li>
                <label class="label "><h3>{$upgradescript->name()}</h3></label>
                {if !empty($upgradescript->results)}
                    <p class="success">{$upgradescript->results}</p>
                {else}
                    <p class="failed">{'Not Selected to Run'|gettext}</p>
                {/if}
            </li>
        {/foreach}
    </ol>
</div>
