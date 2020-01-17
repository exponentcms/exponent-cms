{*
 * Copyright (c) 2004-2020 OIC Group, Inc.
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

{css unique="definable-field-edit" corecss="forms"}

{/css}

<div class="module expDefinableField edit">
    <div class="form_header">
        <h1>{if $is_edit == 1}{'Edit Definable Field'|gettext}{else}{'Create a New Definable Field'|gettext}{/if} - {$types}</h2>
    </div>
    {$form_html}
</div>

