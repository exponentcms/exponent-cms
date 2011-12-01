{css unique="current_carts" corecss="tables"}

{/css}


<div class="module report current_carts">
	{br}
	<div class="exp-ecom-table exp-skin-table">
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
					<td>{$summary.valueproducts}</td>
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
	
	{br}
	{br}
	<h2 style="text-align: center;">{"Active Carts w/ Products"|gettext}</h2>
	<div class="exp-ecom-table exp-skin-table">
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
	
	{br}
	{br}
	<h2 style="text-align: center;">{"Active Carts w/ Products and User Information"|gettext}</h2>
	<div class="exp-ecom-table exp-skin-table">
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
</div>