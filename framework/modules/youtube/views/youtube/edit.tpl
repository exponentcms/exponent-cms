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

<div class="module text edit yui-skin-sam">
    {if $record->id != ""}
        <h1>Editing {$record->title}</h1>
    {else}
        <h1>New Code Snippet</h1>
    {/if}

    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=text name=title label="Title" value=$record->title class="title"}
        {control type=textarea name=embed_code label="Embed Code" value=$record->embed_code}
        {control type=html name=description label="Description" value=$record->description}
        {control type=buttongroup submit="Save Text" cancel="Cancel"}
    {/form}   
</div>
