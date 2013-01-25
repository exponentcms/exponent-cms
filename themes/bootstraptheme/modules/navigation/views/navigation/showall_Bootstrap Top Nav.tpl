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

<div class="navigation navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand" href="{$smarty.const.URL_FULL}">{$smarty.const.ORGANIZATION_NAME}</a>
            <ul class="nav">
                {getnav type='hierarchy' assign=hierarchy}
                {bootstrap_navbar menu=$hierarchy}
            </ul>
        </div>
    </div>
</div>
<div class="navbar-spacer"></div>
