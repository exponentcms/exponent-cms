{css unique="product-show" link="`$asset_path`css/storefront.css" corecss="button,tables"}

{/css}

{css unique="product-show" link="`$asset_path`css/ecom.css"}

{/css}

{css unique="giftcard-listing" link="`$asset_path`css/giftcards.css"}

{/css}

<div class="module store show giftcards">
	{form id="addtocart" controller=cart action=addItem} 
	{control type="hidden" name="product_type" value="giftcard"}
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tbody>
			<tr>
				<td>
					<h2>Select the style of your gift card</h2>
				</td>
			</tr>
	
			<tr>
				<td>
					<fieldset id="card-style">
						{foreach from = $giftcards item=giftcard}
							<div class="picwrapper">
								{img file_id=$giftcard->expFile.mainimage[0]->id w=250 alt=$giftcard->image_alt_tag|default:"Image of `$giftcard->title`" title="`$giftcard->title`"  class="gc-preview"}
								{control type="radio" name="product_id" label="`$giftcard->title`" value="`$giftcard->id`"}
							</div>
						{/foreach}
					</fieldset>
				</td>
			</tr>
	
			<tr>
				<td>
					<h2>Select the amount of the gift card</h2>
				</td>
			</tr>
			
			<tr>
				<td>
					<fieldset id="card-amount">
						
						<div class="radio control" id="25dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="25" name="card_amount" id="25d"></td><td><label class="label " for="25d">$25</label></td></tr></tbody></table></div>
						<div class="radio control" id="50dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="50" name="card_amount" id="50d"></td><td><label class="label " for="50d">$50</label></td></tr></tbody></table></div>
						<div class="radio control" id="75dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="75" name="card_amount" id="75d"></td><td><label class="label " for="75d">$75</label></td></tr></tbody></table></div>
						<div class="radio control" id="100dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="100" name="card_amount" id="100d"></td><td><label class="label " for="100d">$100</label></td></tr></tbody></table></div>
						<div class="radio control" id="150dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="150" name="card_amount" id="150d"></td><td><label class="label " for="150d">$150</label></td></tr></tbody></table></div>
						<div class="radio control" id="200dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="200" name="card_amount" id="200d"></td><td><label class="label " for="200d">$200</label></td></tr></tbody></table></div>
						<div class="text-control control  man_amount " id="card_amount_txtControl"><label class="label" for="card_amount_txt">Other Amount</label><input type="text" onchange="clearRadioButtons();" onpaste="return money_filter.onpaste(this, event);" onfocus="money_filter.onfocus(this);" onblur="money_filter.onblur(this);" onkeypress="return money_filter.on_key_press(this, event);" class="text man_amount" size="6" value="" name="card_amount_txt" id="card_amount_txt"></div>
						<em>There is a {currency_symbol}{$config.minimum_gift_card_purchase|number_format:2} Minimum on gift card purchases.</em>
					</fieldset>
				</td>
			</tr>
			
			<tr>
				<td>
					<h2>Personalize your gift card</h2>
				</td>
			</tr>
			
			<tr>
				<td>
					<fieldset>
						<em>The "To" and "From" name may be added at no additional charge.</em>
						<div class="text-control control  "><label class="label">To:</label><input type="text" class="text " size="20" value="" name="toname"></div>
						<div class="text-control control  "><label class="label">From:</label><input type="text" class="text " size="20" value="" name="fromname"></div>
						<br><em>Adding a custom message will add {currency_symbol}{$config.custom_message_product|number_format:2} to the price of your gift card.</em><br><br>
						<div class="text-control control "><label class="label">Custom Message (100 characters max)</label><textarea cols="45" rows="3" name="msg" id="msg" class="textarea"></textarea></div>
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>

	<a id="submit-giftcard" href="javascript:{ldelim}{rdelim}" class="awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE} exp-ecom-link" rel="nofollow"><strong><em>Add selected items to cart</em></strong></a>
	{/form}
</div>		

{script unique="giftcard-submit"}
	{literal}
		YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
			Y.one('#submit-giftcard').on('click',function(e){
				e.halt();
				var frm = Y.one('#addtocart');
				frm.submit();
			});
		});
	{/literal}
{/script}