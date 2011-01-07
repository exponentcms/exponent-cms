{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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

{foreach from=$pullable_modules item=mod key=src}
    {control type="checkbox" name="aggregate[]" label="`$mod->title` on page `$mod->section`" value=$src checked=$config.aggregate}
{foreachelse}
    There doesn't appear to be any other modules installed that you can aggregate data from
{/foreach}
