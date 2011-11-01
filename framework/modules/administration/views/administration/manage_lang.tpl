{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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

<div id="manage_lang" class="module administration manage-lang">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help"|gettext|cat:" "|cat:("Managing Translations"|gettext) module="manage-language"}
        </div>
        <h1>{gettext str="Manage Translations"}</h1>
    </div>
    {gettext str="Current Display Language is"}: {$smarty.const.LANG}
    {if $smarty.const.LANG != 'English - US'}
        {br}{br}
        <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link action=update_lang}"><b>{gettext str="Add missing phrases to"|cat:" "|cat:($smarty.const.LANG)}</b></a>
    {/if}
    {br}{br}<hr>
    <h2>{gettext str="Add Phrases to the Default Translation File"}</h2>
    <h3 style="color:red">{gettext str='WARNING! Turning this on will SLOW down the site and also turn on error reporting'}!</h3>
    {form action=update_langtemplate}
        {control type="checkbox" postfalse=1 name=writetemplate label="Build Phrase Library?"|gettext checked=$smarty.const.WRITE_LANG_TEMPLATE value=1}
        {control type=buttongroup submit="Change Setting"}
    {/form}
    <hr>
    <h2>{gettext str="Create a New Translation based on the Current Translation"} - {$smarty.const.LANG}</h2>
    {form action=save_newlangfile}
        {control type=text name=newlang label="New Translation Name"|gettext}
        {control type=buttongroup submit="Create and Begin Using a New Translation"}
    {/form}
</div>