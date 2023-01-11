{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{if !empty($extra_data)}
    <div class="extra-data">
        <blockquote>
            <strong>{'To'|gettext}:</strong> {$extra_data.to} <strong>{'From'|gettext}:</strong> {$extra_data.from}
            {br}{$extra_data.msg}
        </blockquote>
    </div>
{/if}
