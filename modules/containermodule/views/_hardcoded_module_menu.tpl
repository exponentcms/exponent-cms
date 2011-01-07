{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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

{permissions}
<div class="exp-container-wrapper">
    <div class="container-chrome module-chrome hardcoded-chrome">
    <a href="#" class="trigger" title="{$container->info.module}">{$container->info.module}</a>
    {getchromemenu module=$container rank=$i last=$last}
    </div>
</div>
{/permissions}