{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

<div class="module forms edit edit-control">
    <div class="form_title">
        <h1>{if $is_edit == 1}{'Edit Control'|gettext}{else}{'Create a New Control'|gettext}{/if} - {$type}</h1>
    </div>
    {$form_html}
    {if $is_edit != 1 && $type != "htmlcontrol" && $type != "pagecontrol" && $type != "static"}{br}<em>
        <strong>** {'Adding this control will reset the default report to all fields'|gettext} **</strong></em>
    {/if}
</div>

