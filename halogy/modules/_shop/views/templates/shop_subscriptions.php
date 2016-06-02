{include:header}

<div id="tpl-shop" class="module">

	<div class="col col1">

		<h1>Your Subscription Payments</h1>

		{if payments}
		
			{pagination}
			
			<table class="default">
				<tr>
					<th>Subscription ID</th>
					<th>Payment Date</th>
					<th>Amount</th>
					<th class="narrow">&nbsp;</th>
				</tr>
				{payments}
					<tr>
						<td>#{payment:subscription-id}</td>
						<td>{payment:date}</td>
						<td>{payment:amount}</td>
						<td><a href="{payment:link}">View Invoice</a></td>
					</tr>
				{/payments}
			</table>

			{pagination}

		{else}

			<p>You have no subscription payments yet.</p>

		{/if}

		<p><small><strong>Please note:</strong> To cancel subscriptions you will need to log in to your account with the payment gateway (PayPal or RBS Worldpay) and cancel them from there. Subscriptions will be automatically renewed unless cancelled at least 24 hours prior to the renewal date.</small></p>

	</div>
	<div class="col col2">
	
		<h3>Categories</h3>
	
		<ul class="menu">
			{shop:categories}
		</ul>
		
	</div>

</div>
	
{include:footer}