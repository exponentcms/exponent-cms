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

<h1>
    {if $editor == 'ckeditor'}
        CKEditor
    {elseif $editor == 'tinymce'}
        TinyMCE
    {/if}
    '{$demo->name}' {'Toolbar Configuration Preview'|gettext}
</h1>
<blockquote>{'Using the'|gettext} '{$demo->skin}' {'skin'|gettext}.<blockquote>
{control type="editor" name="xxx" label="" value="This is an example of what this editor toolbar configuration looks and works like"|gettext editor=$editor toolbar=$demo->id}
{control type="buttongroup" name="done" cancel="Done"|gettext returntype="manageable"}