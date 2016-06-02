{include:header}

<div id="tpl-shop" class="container">

	<div class="row">

		<h1>{page:heading}</h1>
	
		{if category:description}
			{category:description}
		{/if}
		
		{if shop:products}
		
		{pagination}
		
        <div class="col-sm-2 col-xs-2">
		
				{shop:products}
					{product:rowpad}					
					
						<a href="{product:link}">
						<img src="{product:thumb-path}" alt="Product image" class="productpic" />
                                                {if product:sale_price == ''}
						<p><strong>{product:title}</strong><br />{product:price}</p></a>
                                                {/if}
					
				{/shop:products}
				{rowpad}
			
        </div>
		
		{pagination}

		{else}

			<p>No products found.</p>
		
		{/if}

	
	</div>

</div>
	
{include:footer}