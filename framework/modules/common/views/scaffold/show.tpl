{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="scaffold show">
    {if $smarty.const.DEVELOPMENT}
        <h4>{'This is the scaffold view'|gettext}</h4>
    {/if}
    <h1>{'Showing'|gettext} {$model_name}, id: {$object->id}</h1>

    <div class="item" id="scaffold-object">
        {list_object object=$record}
        <a href="{link controller=$model_name action=showall}">{'Go back to Show All'|gettext} {$model_name}</a> or
        {permissions}
            <div class="item-actions">
                <a href="{link controller=$model_name action=edit id=$record->id}"> {'Edit this'|gettext} {$model_name}</a>
            </div>
        {/permissions}
    </div>
</div>
