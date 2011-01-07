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

<div class="module store action">
    <h1>{$moduletitle|default:"Select type of product"}</h1>
    
    {form controller=store action=edit}
        {control type="hidden" name="id" value=0}
        {control type="dropdown" name="product_type" label="Select Product Type" items=$product_types default="product"}
        {control type="buttongroup" submit="Submit" cancel="Cancel"}
    {/form}
</div>
