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

<div class="scaffold edit">
    {if $smarty.const.DEVELOPMENT}
        <h4>{'This is the scaffold view'|gettext}</h4>
    {/if}
    {if $record->id != ""}
        <h1>{'Edit Information for'|gettext} {$model_name}</h1>
    {else}
        <h1>{'New'|gettext} {$model_name}</h1>
    {/if}
    {form action=update}
        {control name=controller type=hidden value=$controller}
        {scaffold model=$model_table item=$record}
    {/form}
</div>