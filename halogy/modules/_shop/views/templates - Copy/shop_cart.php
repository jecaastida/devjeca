{include:header}

<div id="tpl-shop" class="container">

	<div class="row">

		<h1>Shopping Cart</h1>
		
		{if errors}
			
            <div class="alert alert-danger error">
             <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				{errors}
			</div>
		{/if}		
		
		<form action="/shop/cart/update" method="post" id="cart_form" class="default">
		
			<p>Your shopping cart contains:</p>
            <div class="table-responsive">
			<table class="table">
				<tr>
					<th>&nbsp;&nbsp;</th><th>&nbsp;&nbsp;</th>
                    <th>Product</th>
					<th>Price ({site:currency})</th>
					<th>Quantity</th>
				</tr>
					{if cart:items}
						{cart:items}				
					{else}
						<tr>
							<td colspan="3">Your cart is empty!</td>
						</tr>
					{/if}
					<tr>
						<td colspan="3" ><hr /></td>
					</tr>
                    
                    <tr>
						<td><label for="shippingBand">Please select Your Courier:</label>
                        <br class="clear" /><br />
                        <select name="shippingBand" id="shippingBand" onchange="document.getElementById('cart_form').submit();" class="form-control">
                            {cart:bands}
                        </select>
                        </td>
					</tr>
                    
                    
                    
                    
                    <tr>
						<td colspan="3" ><hr /></td>
					</tr>
					{if cart:discounts}
						<tr>
							<td colspan="2">Discounts applied:</td>
							<td>({cart:discounts})</td>
						</tr>
					{/if}
				
                    
                    <tr>
						<td colspan="2">Sub total:</td>
						<td>{cart:subtotal}</td>
					</tr>
					<tr>
						<td colspan="2">Shipping:</td>
						<td>{cart:postage}</td>
					</tr>
					{if cart:tax}
						<tr>
							<td colspan="2">Tax:</td>
							<td>{cart:tax}</td>
						</tr>
					{/if}
					<tr>
						<td colspan="2"><strong>Total amount:</strong></td>
						<td><strong>{cart:total}</strong></td>
					</tr>					
					<tr>
						<td colspan="3" ><hr /></td>
					</tr>					
			</table>
	
			

			{if cart:modifiers}
		
				<label for="shippingModifier">Shipping Modifier:</label>
				<select required name="shippingModifier" id="shippingModifier" onchange="document.getElementById('cart_form').submit();" class="form-control">
					{cart:modifiers}
				</select>
				<br class="clear" /><br />
				
			{/if}

			<label for="discountCode">Discount Code:</label>
			<input type="text" name="discountCode" id="discountCode" value="{form:discount-code}" class="form-control small" />
			<br class="clear"><br />

			{if cart:items}
		
				<div style="float:right;">
					<input type="submit" value="Update Cart" class="btn btn-default" />
					<input name="checkout" type="submit" value="Checkout &gt;&gt;" class="btn btn-primary" />
				</div>
				<br class="clear" />					

			{/if}
		
		</form>

	</div>
	

</div>
	
{include:footer}