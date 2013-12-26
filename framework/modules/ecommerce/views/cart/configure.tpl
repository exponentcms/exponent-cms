{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="module cart configure">
    <h1>{"Configure Shopping Cart"|gettext}</h1>
    {form action=saveconfig}
    {control type="text" name="min_order" label="Minimum order amount to require"|gettext value=$config.min_order filter=money}
    {control type="html" name="policy" label="Policies"|gettext value=$config.policy}
    {control type="buttongroup" submit="Save Store Configuration"|gettext cancel="Cancel"|gettext}
    {/form}
</div>