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

<div class="module headline headline-show">

    <h1>{$headline}</h1>

    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.edit == 1}
        <div class="module-actions">
            {icon action=edit id=$record->id title="Edit this `$modelname`"}
        </div>
        {/if}
    {/permissions}
</div>
