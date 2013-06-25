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

<div class="module links edit">
    {if $record->id != ""}
    	<h1>{'Edit Information for'|gettext} {$modelname}</h1>
        {$newwin = $record->new_window}
    {else}
    	<h1>{'New'|gettext} {$modelname}</h1>
        {$newwin = $config.opennewwindow}
    {/if}
    {form action=update}
    	{control name=id type=hidden value=$record->id}
        {control type="text" name="title" label="Title"|gettext value=$record->title}
        {*{control type="text" name="url" label="URL"|gettext value=$record->url}*}
        {control type="text" name="url" label="URL"|gettext value=$record->url}
        {control type="checkbox" name="new_window" label="Open in New Window"|gettext checked=$newwin value="1"}
        {control type="files" name="image" label="Image"|gettext value=$record->expFile limit=2}
        {control type="editor" name="body" label="URL Description"|gettext value=$record->body}
        {if $config.usecategories}
            {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='`$modelname`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
        {/if}
        {control type="buttongroup" submit="Save Link"|gettext cancel="Cancel"|gettext}
    {/form}
</div>