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

{literal}
<style type="text/css">
    .control .label {
        display: inline;
        font-size: 100%;
        font-weight: bold;
    }
</style>
{/literal}

<div class="module help select-version">
    <form>
        {control type="dropdown" name="version" label="Help Version: "|gettext items=$versions default=$selected onchange="switch_ver(this.value)"}
    </form>
</div>

{literal}
<script type="text/javascript">
	function switch_ver(id){
		location.href="index.php?module=help&action=switch_version&version=" + id
	}
</script>
{/literal}