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

<div class="module text edit">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Configuring Toolbars"|gettext) module="`$editor`-toolbar-configuration"}
        </div>
        {if $record->id != ""}
            <h2>{'Editing'|gettext}
        {else}
            <h2>{'New'|gettext}
        {/if}
        {if $editor == 'ckeditor'}
            CKEditor
        {elseif $editor == 'tinymce'}
            TinyMCE
        {/if}
        {'Toolbar Configuration'|gettext}</h2>
    </div>

    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=editor value=$editor}
        {control type=text name=name label="Configuration Name"|gettext value=$record->name focus=1}
	    {control type="checkbox" postfalse=1 name=active label="Make this Configuration Active?"|gettext checked=$record->active value=1}
        {if $editor == 'ckeditor'}
            {$skin = 'kama'}
        {elseif $editor == 'tinymce'}
            {$skin = 'lightgray'}
        {/if}
		{control type=dropdown name=skin label="Toolbar Skin"|gettext items=$skins values=$skins value=$record->skin default=$skin}
        {if $editor == 'ckeditor'}
            {control type="checkbox" postfalse=1 name=scayt_on label="Autostart Spell Check"|gettext checked=$record->scayt_on value=1}
            {control type="checkbox" postfalse=1 name=paste_word label="Allow Formatted Pasting from MS Word"|gettext checked=$record->paste_word value=1}
        {elseif $editor == 'tinymce'}
            {control type="checkbox" postfalse=1 name=scayt_on label="Disable Browser Spell Check"|gettext checked=$record->scayt_on value=1}
        {/if}
        <blockquote>
            <h4>{'Blank or empty entries in the following text boxes result in using the default setting'|gettext}</h4>
            {'Please visit the help page for entry format requirements!'|gettext}
        </blockquote>
		{control type=textarea cols=80 rows=10 name=data label="Toolbar Button Configuration"|gettext value=$record->data}
        {if $editor == 'ckeditor'}
            {control type=textarea cols=80 rows=2 name=stylesset label="Styles List"|gettext value=$record->stylesset}
            {control type=textarea cols=80 rows=2 name=formattags label="Format List"|gettext value=$record->formattags}
            {control type=textarea cols=80 rows=2 name=fontnames label="Font List"|gettext value=$record->fontnames}
        {elseif $editor == 'tinymce'}
            {control type=textarea cols=80 rows=2 name=stylesset label="Formats List"|gettext value=$record->stylesset}
            {control type=textarea cols=80 rows=2 name=formattags label="Paragraph List"|gettext value=$record->formattags}
            {control type=textarea cols=80 rows=2 name=fontnames label="Font Family List"|gettext value=$record->fontnames}
        {/if}
        {if $editor == 'ckeditor'}
            {control type=textarea cols=80 rows=2 name=plugins label="Load Custom Plugins (comma separated) MUST be installed first!"|gettext value=$record->plugins description='Adding an uninstalled plugin to this list may crash the site!'|gettext}
        {elseif $editor == 'tinymce'}
            {control type=textarea cols=80 rows=2 name=plugins label="Load Plugins (comma separated) MUST be installed first!"|gettext value=$record->plugins description='You must specifically include standard plugins here.'|gettext}
        {/if}
        {control type=textarea cols=80 rows=2 name=additionalconfig label="Additionial Configuration (comma separated javascript object)"|gettext value=$record->additionalconfig description='Adding an incorrectly formated configuration to this list may crash the site!'|gettext}
        {control type=buttongroup submit="Save Toolbar"|gettext cancel="Cancel"|gettext returntype="manageable"}
    {/form}   
</div>
