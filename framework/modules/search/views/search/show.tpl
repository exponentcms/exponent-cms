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
 
<div class="module search show-form">
    {if $moduletitle != ""}<h1>{$moduletitle}</h1>{/if}
    <form id="form" name="form" class="" method="POST" action="{$smarty.const.URL_FULL}index.php">
        <input type="hidden" name="action" id="action" value="search">
        <input type="hidden" name="module" id="module" value="search">
        {control type="text" name="search_string" id="search_string" label=" " value="Search"}
        {control type="buttongroup" submit="Search"}
    </form>
</div>
{script unique="search" yui3mods="node"}
{literal}

YUI({ base:EXPONENT.YUI3_PATH,loadOptional: true}).use('*', function(Y) {
    Y.get('#search_string').on('click',function(e){e.target.set('value','');});
});

{/literal}
{/script}
