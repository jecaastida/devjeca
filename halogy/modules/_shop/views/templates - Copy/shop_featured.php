{include:header}

<div id="tpl-shop" class="container">

	<div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#feat">Featured Products</a></li>
          <li><a  data-toggle="tab" href="#pop">Popular Products</a></li>
          <li><a  data-toggle="tab" href="#latest">Latest Products</a></li>
          
        </ul>
		
        
       <div class="tab-content">
            <div id="feat" class="tab-pane fade in active">
                {if shop:featured}
                
                <div class="row">
                   
                        {shop:featured}
                            {product:rowpad}			
                            <div class="col-xs-2">
                            
                                <a href="{product:link}">
                                <img src="{product:thumb-path}" alt="Product image" class="productpic" />
                                <p><strong>{product:title}</strong><br />{product:price}</p></a>
                            
                            </div>
                        {/shop:featured}
                        {rowpad:featured}
                </div>

                {else}

                    <p><small>There are currently no featured products.</small></p>
                
                {/if}

            </div>
        
            <div id="pop" class="tab-pane fade">
        <!--popular-->
		{if shop:popular}
		
		<div class="row">
                   
				{shop:popular}
					{product:rowpad}					
                    <div class="col-xs-2">
					
						<a href="{product:link}">
						<img src="{product:thumb-path}" alt="Product image" class="productpic" />
						<p><strong>{product:title}</strong><br />{product:price}</p></a>
					
                    </div>
				{/shop:popular}
				{rowpad:popular}
        </div>
            

		{else}

			<p><small>There are currently no products here.</small></p>
		
		{/if}
        
        
            </div>
        
            <div id="latest" class="tab-pane fade">
        
        <!--latest-->
		<h3>Latest Products</h3>

		{if shop:latest}
		
		<div class="row">
                   
				{shop:latest}
					{product:rowpad}					
					
                    <div class="col-xs-2">
						<a href="{product:link}">
						<img src="{product:thumb-path}" alt="Product image" class="productpic" />
						<p><strong>{product:title}</strong><br />{product:price}</p></a>
					
                    </div>
				{/shop:latest}
				{rowpad:latest}
        </div>

		{else}

			<p><small>There are currently no products here.</small></p>
		
		{/if}
		
        
         </div>
        </div>
         
         
		<!--h3>Most Viewed</h3>

		{if shop:mostviewed}
		
		<table class="default">
			<tr>
				{shop:mostviewed}
					{product:rowpad}					
					<td align="center" valign="top" width="{product:cell-width}%">
						<a href="{product:link}">
						<img src="{product:thumb-path}" alt="Product image" class="productpic" />
						<p><strong>{product:title}</strong><br />{product:price}</p></a>
					</td>
				{/shop:mostviewed}
				{rowpad:mostviewed}
			</tr>
		</table>

		{else}

			<p><small>There are currently no products here.</small></p>
		
		{/if}-->

	</div>
	

</div>
	
{include:footer}