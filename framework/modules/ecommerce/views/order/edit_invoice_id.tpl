{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{'Invoice #\'s should be numeric and although you may select any number you\'d like, ideally it should fall in line with the current sequence of invoice #\'s.'|gettext}
<div id="edit_shipping_method">
    {form action=save_invoice_id}
        {control type="hidden" name="id" value=$orderid}                     
        {control type="text" name="invoice_id" label='Invoice #:'|gettext value=$invoice_id}
        {control type="buttongroup" submit="Save Invoice Id"|gettext cancel="Cancel"|gettext}
    {/form}
</div>