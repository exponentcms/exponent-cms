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

<div class="module text edit">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Configuring Toolbars"|gettext) module="ckeditor-toolbar-configuration"}
        </div>
    {if $record->id != ""}
        <h1>{'Editing CKEditor Toolbar Configuration'|gettext}</h1>
    {else}
        <h1>{'New CKEditor Toolbar Configuration'|gettext}</h1>
    {/if}
    </div>

    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=text name=name label="Configuration Name"|gettext value=$record->name}
	    {control type="checkbox" postfalse=1 name=active label="Make this Configuration Active?"|gettext checked=$record->active value=1}
		{control type=dropdown name=skin label="Toolbar Skin"|gettext items=$skins values=$skins value=$record->skin default='kama'}
		{control type="checkbox" postfalse=1 name=scayt_on label="Autostart SpellCheck"|gettext checked=$record->scayt_on value=1 checked=1}
		{control type="checkbox" postfalse=1 name=paste_word label="Allow Formatted Pasting from MS Word"|gettext checked=$record->paste_word value=1}
        <h4><em>({'Blank or empty entries in the following text boxes result in using the default setting'|gettext})</em></h4>
        <p><em>({'Please visit the help page for entry format requirements!'|gettext})</em></p>
		{control type=textarea cols=80 rows=20 name=data label="Toolbar Button Configuration"|gettext value=$record->data}
        {control type=textarea cols=80 rows=2 name=stylesset label="Styles List (plus style sheet styles)"|gettext value=$record->stylesset}
        {control type=textarea cols=80 rows=2 name=formattags label="Formats List"|gettext value=$record->formattags}
	    {control type=textarea cols=80 rows=2 name=fontnames label="Fonts List"|gettext value=$record->fontnames}
        {control type=textarea cols=80 rows=2 name=plugins label="Load Custom Plugins (comma separated) MUST be installed first!"|gettext value=$record->plugins}
        {control type=buttongroup submit="Save Toolbar"|gettext cancel="Cancel"|gettext returntype="manageable"}
    {/form}   
</div>
