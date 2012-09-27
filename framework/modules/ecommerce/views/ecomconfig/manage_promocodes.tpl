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

{css unique="managepromocodes" corecss="tables"}

{/css}

<div class="module ecomconfig promocodes">
	<div class="form_header">
        <h1>{'Promotional Codes'|gettext}</h1>
        <p>{'Here you can configure promotional codes to give to users.'|gettext}</p>
	</div>
	
	<h2>{'Add a new promo code'|gettext}</h2>
	<table class="exp-skin-table">
	<thead>
	    <tr>
	        <th>{'Name'|gettext}</th>
		    <th>{'Promo Code'|gettext}</th>
		    <th>{'Discount'|gettext}</th>
		    <th>{'Action'|gettext}</th>
		</tr>
	</thead>
	<tbody>
	    <tr>
	        {form action=update_promocode}
                <td>{control type=text name=title label=" "}</td>
                <td>{control type="text" name="promo_code" label="" label=" "}</td>
                <td>{control type="dropdown" name="discounts_id" items=$discounts key=id display=title label=" " includeblank="-- Select a Discount --"|gettext}</td>
                <td>{control type=buttongroup submit="Add Discount"|gettext}</td>
	        {/form}
	    </tr>
	</tbody>
	</table>
	
	{if $promo_codes|@count > 0}
	<h2>{'Modify existing group discount'|gettext}</h2>
	<table class="exp-skin-table">
	    <thead>
	    <tr>
	        <th>{'Name'|gettext}</th>
		    <th>{'Promo Code'|gettext}</th>
		    <th>{'Discount'|gettext}</th>
		    <th>{'Action'|gettext}</th>
	    </tr>
	    </thead>
	    {foreach from=$promo_codes item=code}
			<tr class="{cycle values='even,odd'}"">
			    {form action=update_promocode}
	                {control type="hidden" name="id" value=$code->id}
	                <td>{control type=text name=title label=" " value=$code->title}</td>
	                <td>{control type="text" name="promo_code" label="" label=" " value=$code->promo_code}</td>
                    <td>{control type="dropdown" name="discounts_id" items=$discounts key=id display=title label=" " value=$code->discounts_id}</td>
                    <td>{control type=buttongroup submit="Update"|gettext}</td>
	            {/form}
			</tr>
		{/foreach}
	</tbody>
	</table>
	{/if}
</div>
