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

{css unique="select-version"}
{literal}
    .select-version .control .label {
        display: inline;
        font-size: 100%;
        font-weight: bold;
    }
{/literal}
{/css}

<div class="module help select-version">
    <form>
        {control type="dropdown" name="version" label="Help Version"|gettext|cat:': ' items=$versions default=$selected onchange="switch_ver(this.value)"}
    </form>
</div>

{script unique="select-version"}
{literal}
	function switch_ver(id){
		location.href="index.php?module=help&action=switch_version&version=" + id
	}
{/literal}
{/script}
