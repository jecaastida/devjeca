{include:header}

<div id="tpl-shop" class="container">

	<div class="row">

		{if errors}
			
            <div class="alert alert-danger error">
             <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				{errors}
			</div>
		{/if}
     
        
		{if message}
			<div class="alert alert-info">
             <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				{message}
			</div>
		{/if}			
		
        <div class="col-md-3">
            {include:side-cat}
        </div>

        <div class="col-md-9">
            <form method="post" action="/shop/cart/add" class="default">
            
                <div class="thumbnail">
                <div class="description caption-full">
            
                    <input type="hidden" name="productID" value="{product:id}" />
                    <div class="caption-full">
                    <h1>{product:title}</h1>

                    {if product:subtitle}
                    
                        <h2>{product:subtitle}</h2>
                        
                    {/if}
                    
                    {product:body}
                    </div>
                     <div class="purchase">
                
                    {if product:image-path}
                    
                        <p><a href="{product:image-path}" title="{product:title}" class="lightbox"><img src="{product:image-path}" alt="{product:title}" class="productpic" width="178" /></a></p>
            
                    {else}
                    
                        <p><img src='https://goo.gl/Kjcemo' alt="Product image" class="productpic img-responsive" /></p>

                    {/if}
                    
                    <p>{product:status}</p>
                    
                    <div class="text-right">
                        <h4><strong>{product:price}</strong></h4>
                    </div>
            
                    {product:variations}				
            
                    <br class="clear" />
                    
                    {if product:stock}
                                            
                        <input type="submit" value="Add to Cart" class="btn btn-info" style="margin-top:12px;" />
                    {/if}
                </div>
					
                            
                </div> 
                </div>
                            
               
                
                
                <div id="reviews panel panel-info">
                    <div class="text-right panel-title">
                    
                        <p>
                            <a href="/shop/recommend/{product:id}" class="loader">Recommend this product</a><br />
                            <a href="/shop/review/{product:id}" class="loader">Write a review</a>						
                        </p>
                    </div>
                    
                    <div class="panel-body">
                        <h3>Reviews</h3>

                        {if product:reviews}
                            
                            {product:reviews}
                            <div class="review {review:class}" id="review{review:id}">
        
                               
                
                                <div class="col2">
                                    <img class="pull-right" src="http://static.halogy.com/themes/default/images/rating{review:rating}.gif" alt="{review:rating} Rating" class="rating" />
                                    
                                    <p>By <strong>{review:author}</strong> <small>on {review:date}</small></p>
                                                
                                    <p>{review:body}</p>
                                </div>
        
                            </div>
                            <div class="clear"></div>
                            {/product:reviews}

                        {else}

                            <p><small>There are currently no reviews</small></p>

                        {/if}						

                    </div>
                </div>
            </form>

        </div>
	</div>
	

</div>
	
{include:footer}