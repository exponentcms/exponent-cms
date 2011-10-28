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

<div class="module text edit">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with Toolbar Configuration" module="ckeditor-toolbar-configuration"}
        </div>
    {if $record->id != ""}
        <h1>Editing CKEditor Toolbar Configuration</h1>
    {else}
        <h1>New CKEditor Toolbar Configuration</h1>
    {/if}
    </div>

    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=text name=name label="Configuration Name" value=$record->name}
	    {control type="checkbox" postfalse=1 name=active label="Make this Configuration Active?" checked=$record->active value=1}
		{control type=dropdown name=skin label="Toolbar Skin" items="Kama, Office 2003, Version 2" values="kama,office2003,v2" value=$record->skin}
		{control type="checkbox" postfalse=1 name=scayt_on label="Autostart SpellCheck" checked=$record->scayt_on value=1}
		{control type="checkbox" postfalse=1 name=paste_word label="Allow Formatted Pasting from MS Word" checked=$record->paste_word value=1}
		{control type=textarea cols=80 rows=20 name=data label="Toolbar Button Configuration" value=$record->data}
	    {control type=textarea cols=80 rows=2 name=plugins label="Load Custom Plugins (comma separated) MUST be installed first!" value=$record->plugins}
        {control type=buttongroup submit="Save Toolbar" cancel="Cancel" returntype="manageable"}
    {/form}   
</div>
