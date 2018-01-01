{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

{css unique="submit-form-buttons" corecss="button"}

{/css}

<div class="module forms submit-data">
    {messagequeue}
    <div style="padding: 1em;">
        {$response_html}
        {clear}
    </div>
    {*<a class="{button_style}" href="{$backlink}">{'Back'|gettext}</a>*}
    {icon button=true link=$backlink text='Back'|gettext}
</div>