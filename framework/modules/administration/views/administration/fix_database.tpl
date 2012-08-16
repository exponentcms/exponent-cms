{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<h1>{"Attempting to Fix the Exponent Database"|gettext}</h1>
<h2>{"Note: Some Error Conditions can NOT be repaired by this Procedure!"|gettext}</h2>
<hr>
<h3>{"Searching for sectionrefs which have lost their originals"|gettext}</h3>
<ul>
    {if $no_origs}
        {foreach from=$no_origs item=no_orig}
            <li>{$no_orig}</li>
        {/foreach}
    {else}
        <li>{"None Found: Good"|gettext}</li>
    {/if}
</ul>
<hr>
<h3>{"Searching for sectionrefs pointing to missing sections/pages, to fix Recycle Bin"|gettext}</h3>
<ul>
    {if $no_sections}
        {foreach from=$no_sections item=no_section}
            <li>{$no_section}</li>
        {/foreach}
    {else}
        <li>{"None Found: Good"|gettext}</li>
    {/if}
</ul>
<hr>
<h3>{"Searching for unassigned modules (no source)"|gettext}</h3>
<ul>
    {if $no_assigns}
        {foreach from=$no_assigns item=no_assign}
            <li>{$no_assign}</li>
        {/foreach}
        {br}<li><strong>{"NOTE: some hard-coded modules produce 'no source' sectionrefs automatically"|gettext}</strong></li>
    {else}
        <li>{"No Empties Found: Good!"|gettext}</li>
    {/if}
</ul>
<hr>
<h3>{"Searching for missing sectionrefs based on existing containers"|gettext}</h3>
<ul>
    {if $missing_sectionrefs}
        {foreach from=$missing_sectionrefs item=missing_sectionref}
            <li>{$missing_sectionref}</li>
        {/foreach}
    {else}
        <li>{"No Unassigned Modules Found: Good!"|gettext}</li>
    {/if}
</ul>
