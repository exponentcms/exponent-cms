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

<div class="module text edit">
    {if $record->id != ""}
        <h1>{'Editing'|gettext}: {$record->title}</h1>
    {else}
        <h1>{'New Text Item'|gettext}</h1>
    {/if}

    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=revision_id value=$record->revision_id}
        {if !empty($record->current_revision_id)}
        {control type=hidden name=current_revision_id value=$record->current_revision_id}
        {/if}
        {control type=hidden name=rank value=$record->rank}
        {control type=text name=title label="Title"|gettext value=$record->title|escape:"html"}
        {control type=html name=body label="Text Block"|gettext value=$record->body}
        {if $config.filedisplay}
            {control type="files" name="files" label="Files"|gettext value=$record->expFile}
        {/if}
        {control type=buttongroup submit="Save Text"|gettext cancel="Cancel"|gettext}
    {/form}
    {selectobjects table=$record->tablename where="id=`$record->id`" orderby='revision_id DESC' item=revisions}
    {if count($revisions) > 1}
        {toggle unique='text-edit' label='Revisons'|gettext collapsed=true}
            {foreach from=$revisions item=revision name=revision}
                {$class = ''}
                {if $revision->revision_id == $record->revision_id}{$class = 'current-revision revision'}{else}{$class = 'revision'}{/if}
                {$label = 'Revision'|gettext|cat:(' #'|cat:($revision->revision_id|cat:(' '|cat:('from'|gettext|cat:(' '|cat:($revision->edited_at|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT|cat:(' '|cat:('by'|gettext|cat:(' '|cat:($revision->editor|username))))))))))}
                {if $revision->revision_id == $record->revision_id}{$label = 'Editing'|gettext|cat:(' '|cat:$label)}{/if}
                {if !$revision->approved && $smarty.const.ENABLE_WORKFLOW}{$class = 'unapproved '|cat:$class}{/if}
                {$label = $label|cat:(' - '|cat:$revision->title)}
                {group label=$label class=$class}
                    {if $revision->revision_id != $record->revision_id}
                    <a class="revision" href="{link action=edit id=$revision->id revision_id=$revision->revision_id}" title="{'Click to Restore this revision'|gettext}">
                    {else}
                    <span title="{'Editing this revision'|gettext}">
                    {/if}
                        {$revision->body|summarize:"html":"parahtml"}
                    {if $revision->revision_id != $record->revision_id}
                    </a>
                    {else}
                    </span>
                    {/if}
                {/group}
            {/foreach}
        {/toggle}
    {/if}
</div>
