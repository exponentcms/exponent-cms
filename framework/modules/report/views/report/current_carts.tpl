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

{include file='menu.inc'}

{css unique="current_carts" link="`$asset_path`/css/accordion.css" corecss="tables"}

{/css}

{*FIXME needs to be converted to yui3*}
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/utilities/utilities.js"></script>
<script type="text/javascript" src="{$asset_path}/js/bubbling.js"></script>
<script type="text/javascript" src="{$asset_path}/js/accordion.js"></script>

<div class="rightcol">
	<div class="module report current_carts">
		{br}
		<div class="exp-skin-table">
			<table border="0" cellspacing="0" cellpadding="0" width="50%">
				<thead>
					<th colspan="2">
						<h1 style="text-align: center;">{"Current Cart Summary"|gettext}</h1>
					</th>
					</tr>
				</thead>
				<tbody>

					<tr class="odd">
						<td>{"Total No. of Carts"|gettext}</td>
						<td>{$summary.totalcarts}</td>
					</tr>
					<tr class="even">
						<td>{"Value of Products in the Carts"|gettext}</td>
						{*<td>{currency_symbol}{$summary.valueproducts}</td>*}
                        <td>{$summary.valueproducts|currency:0}</td>
					</tr>
					<tr class="odd">
						<td>{"Active Carts w/out Products"|gettext}</td>
						<td>{$summary.cartsWithoutItems}</td>
					</tr>
					<tr class="even">
						<td>{"Active Carts w/ Products"|gettext}</td>
						<td>{$summary.cartsWithItems}</td>
					</tr>
					<tr class="odd">
						<td>{"Active Carts w/ Products and User Info"|gettext}</td>
						<td>{$summary.cartsWithItemsAndInfo}</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		{if $cartsWithoutItems|@count gt 1}
			{br}
			{br}
			<div class="exp-skin-table yui-cms-accordion multiple fade fixIE">
				<div class="yui-cms-item yui-panel">
			
					<div class="hd"><h2>{"Active Carts w/out Products and User Information"|gettext}</h2></div>
					<div class="bd" id="yuievtautoid-0" style="height: 0px;">
						<table border="0" cellspacing="0" cellpadding="0" width="50%">
							<thead>
								<tr>
									<th>Length of Time</th>
									<th>IP/location</th>
									<th>Referring URL</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$cartsWithoutItems item=item} 
							
								{if is_object($item)}
								<tr>
									<td>{$item->length_of_time}</td>
									<td>{$item->ip_address}</td>
									<td>{$item->referrer}</td>
								</tr>
							
								{/if}
							{/foreach}
							</tbody>
						</table>
					</div>
					<div class="actions">
						<a class="accordionToggleItem" href="#">&#160;</a>
					</div>
				</div>
			</div>
		{/if}
		
		{if $cartsWithItems|@count gt 1}
			{br}
			{br}
			<div class="exp-skin-table yui-cms-accordion multiple fade fixIE">
				<div class="yui-cms-item yui-panel">
					<div class="hd"><h2>{"Active Carts w/ Products"|gettext}</h2></div>
					<div class="bd" id="yuievtautoid-0" style="height: 0px;">
						<table border="0" cellspacing="0" cellpadding="0" width="50%">
							<thead>
								<tr>
									<th>Length of Time</th>
									<th>IP/location</th>
									<th>Referring URL</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$cartsWithItems item=item} 
								{if is_array($item)}
								<tr>
									<td>{$item.length_of_time}</td>
									<td>{$item.ip_address}</td>
									<td>{$item.referrer}</td>
								</tr>
								<tr>
									<table>
										<thead>
											<tr>
												<td colspan="3"><h3 style="margin:0; padding: 0;">Products</h3></td>
											</tr>
											<tr>
												<td><strong>Product Title</strong></td>
												<td><strong>Quantity</strong></td>
												<td><strong>Price</strong></td>
											</tr>
										</thead>
										<tbody>
										{foreach from=$item item=item2}  
											{if isset($item2->products_name)}
												<tr>
													<td>{$item2->products_name}</td>
													<td>{$item2->quantity}</td>
													<td>{$item2->products_price_adjusted}</td>
												</tr>
											{/if}
										{/foreach}
										</tbody>
									</table>
								</tr>
								{/if}
							{/foreach}
							</tbody>
						</table>
					</div>
					<div class="actions">
						<a class="accordionToggleItem" href="#">&#160;</a>
					</div>
				</div>	
			</div>	
		{/if}
		
		{if $cartsWithItemsAndInfo|@count gt 1}
			{br}
			{br}
			<div class="exp-skin-table yui-cms-accordion multiple fade fixIE">
				<div class="yui-cms-item yui-panel">
                    <div class="hd"><h2>{"Active Carts w/ Products and User Information"|gettext}</h2></div>
                    <div class="bd" id="yuievtautoid-0" style="height: 0px;">
                        <table border="0" cellspacing="0" cellpadding="0" width="50%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Length of Time</th>
                                    <th>IP/location</th>
                                    <th>Referring URL</th>
                                </tr>
                            </thead>
                            <tbody>
                            {foreach from=$cartsWithItemsAndInfo item=item}
                                {if is_array($item)}
                                <tr>
                                    <td>{$item.name}</td>
                                    <td>{$item.email}</td>
                                    <td>{$item.length_of_time}</td>
                                    <td>{$item.ip_address}</td>
                                    <td>{$item.referrer}</td>
                                </tr>
                                <tr>
                                    <table>
                                        <thead>
                                            <tr>
                                                <td colspan="3"><h3 style="margin:0; padding: 0;">Products</h3></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Product Title</strong></td>
                                                <td><strong>Quantity</strong></td>
                                                <td><strong>Price</strong></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        {foreach from=$item item=item2}
                                            {if isset($item2->products_name)}
                                                <tr>
                                                    <td>{$item2->products_name}</td>
                                                    <td>{$item2->quantity}</td>
                                                    <td>{$item2->products_price_adjusted}</td>
                                                </tr>
                                            {/if}
                                        {/foreach}
                                        </tbody>
                                    </table>
                                </tr>
                                {/if}
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                    <div class="actions">
                        <a class="accordionToggleItem" href="#">&#160;</a>
                    </div>
				</div>
			</div>
		{/if}
	</div>
</div>
{clear}
</div>