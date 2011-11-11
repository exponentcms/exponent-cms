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
        <h1>{"Manage Translations"|gettext}</h1>
    </div>
    <h4>{"Current Display Language is"|gettext}: {$smarty.const.LANG}, {'with'|gettext} {$count} {"Phrases"|gettext}</h4>
    {if $smarty.const.LANG != 'English - US'}
        {if $missing}
            <p>{"There are"|gettext} {$missing} {"Missing Phrases in the"|gettext} {$smarty.const.LANG} {"Translation"|gettext}</p>
            <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link action=update_lang}"><b>{"Add Missing Phrases"|gettext}</b></a>
            {br}{br}
        {/if}
        {if $untrans}
            <p>{"There are"|gettext} {$untrans} {"Untranslated Phrases in the"|gettext} {$smarty.const.LANG} {"Translation"|gettext}</p>
            <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link action=manage_lang_await}"><b>{"View Un-Translated Phrases"|gettext}</b></a>
            {br}{br}
            {form action=manage_lang_auto}
                {control type="text" name="lang" label="Language to Auto-Translate to"|gettext size=6}
                {control type=buttongroup submit="Auto-Translate Phrases"|gettext}
            {/form}
        {/if}
    {/if}
    <hr>
    <h2>{"Add Phrases to the Default Translation File"|gettext}</h2>
    <h3 style="color:red">{'WARNING! Turning this on will SLOW down the site and also turn on error reporting'|gettext}!</h3>
    <p>{'You must visit the pages in the site for the phrases to be captured. The captured phrases will be added to the /framework/core/lang/English - US.php file'|gettext}</p>
    {form action=update_langtemplate}
        {control type="checkbox" postfalse=1 name=writetemplate label="Build Phrase Library?"|gettext checked=$smarty.const.WRITE_LANG_TEMPLATE value=1}
        {control type=buttongroup submit="Change Setting"|gettext}
    {/form}
    <hr>
    <h2>{"Create a New Translation based on the Current Translation"|gettext} - {$smarty.const.LANG}</h2>
    {form action=save_newlangfile}
        {control type=text name=newlang label="New Translation Name"|gettext}
        {control type=buttongroup submit="Create and Begin Using a New Translation"|gettext}
    {/form}
</div>