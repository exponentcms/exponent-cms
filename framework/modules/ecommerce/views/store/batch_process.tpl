{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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
 
<div class="module importexport manage">
    <h1>{'Upload Your CSV File'|gettext}</h1>
    {form action=process_orders}
        <input type="file" name="batch_upload_file" size="50">
        {control type="dropdown" name="order_status_success" label="Set Status of Successfully Captured and Non-Authorized Orders (PayPal, Phone, etc) To:"|gettext size=4 multiple=false items=$order_status default=-1}
        {* control type="dropdown" name="order_status_fail" label="Set Status of Orders That Fail Capture To:"|gettext size=4 multiple=false items=$order_status default=-1 *}
        {control type="radiogroup" name="email_customer" label="Email Status to Customer?"|gettext flip=true items="No,Yes"}
        {control type="buttongroup" submit="Process Orders"|gettext cancel="Cancel"|gettext}
    {/form}
    {br}
</div>
