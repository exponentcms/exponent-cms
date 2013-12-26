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

<div id="manage_lang" class="module administration manage-lang">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Translations"|gettext) module="manage-translations"}
        </div>
        <h1>{"Manage Translations"|gettext}</h1>
    </div>
    {form action=update_language}
        {control type="dropdown" name="newlang" label="Select Display Language"|gettext items=$langs default=$smarty.const.LANGUAGE}
        {control type=buttongroup submit="Switch to this Display Language"|gettext}
    {/form}
    <hr>
    <h3>{"Current Display Language is"|gettext}: {$smarty.const.LANG}, {'with'|gettext} {$count} {"Phrases"|gettext}</h3>
    {if $smarty.const.LANG != 'English - US'}
        {if $missing}
            <p>{"There are"|gettext} {$missing} {"Missing Phrases in the"|gettext} {$smarty.const.LANG} {"Translation"|gettext}</p>
        {/if}
        {if $untrans}
            <p>{"There are"|gettext} {$untrans} {"Untranslated Phrases in the"|gettext} {$smarty.const.LANG} {"Translation"|gettext}</p>
        {/if}
        <p>{'You may use the \'lang_translate.php\' script to help correct this for the'|gettext} {$smarty.const.LANG} {"translation"|gettext}</p>
    {/if}
    <hr>
    <h2>{"Create a New Translation based on the Current Translation"|gettext} - {$smarty.const.LANG}</h2>
    {form action=save_newlangfile}
        {control type=text name=newlang label="New Translation Name"|gettext|cat:" (Español)"}
        {control type=text name=newauthor label="New Translation Author"|gettext}
        {control type=text name=newcharset label="New Translation Character Set"|gettext value="UTF-8"}
        {control type=text name=newlocale label="New Translation Locale"|gettext|cat:' (<a href="http://www.loc.gov/standards/iso639-2/php/English_list.php" target="_blank">ISO 639-1</a>)'}
        {control type=buttongroup submit="Create and Begin Using a New Translation"|gettext}
    {/form}
</div>