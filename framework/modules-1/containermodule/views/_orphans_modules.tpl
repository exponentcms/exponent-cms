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
{foreach from=$orphan_mods item=modname key=mod}
    <a class="navlink" href="{link module=$mod}">{$modname}</a><br />
{foreachelse}
    <i>{'No Archived Modules'|gettext}</i>
{/foreach}
