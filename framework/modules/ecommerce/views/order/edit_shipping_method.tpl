{*
 * Copyright (c) 2004-2019 OIC Group, Inc.
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

<div class="module order edit">
    <h1>{'Editing order shipping method'|gettext}</h1>
    <div id="edit_shipping_method">
        {form action=save_shipping_method}
            {control type="hidden" name="id" value=$orderid}
            {control type="hidden" name="sid" value=$shipping->id}
            {control type="text" name="shipping_method_carrier" label='Carrier'|gettext value=$shipping->carrier}
            {control type="text" name="shipping_method_title" label='Shipping Method'|gettext value=$shipping->option_title focus=1}
            {control type="buttongroup" submit="Save Shipping Method"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>