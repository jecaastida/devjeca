{include:header}

<div id="tpl-shop" class="container">
    <div class="row">
    
    <div class="col-md-3">
               
                <div class="list-group">
                   <a href="/shop">Back to Shop</a>
                    <a href="/shop/account">My Account</a>
                    <a href="/shop/subscriptions">My Subscriptions</a>
                    <a href="/shop/orders">My Orders</a>
                </div>
                
                
               
        </div>
        
        <div class="col-md-9">

            <h1>Your Orders</h1>

            {if orders}
            
                {pagination}
                
                <table class="table">
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Amount ({site:currency})</th>
                        <th class="narrow">&nbsp;</th>
                    </tr>
                    {orders}
                        <tr>
                            <td>#{order:id}</td>
                            <td>{order:date}</td>
                            <td>{order:amount}</td>
                            <td><a href="{order:link}">View Order</a></td>
                        </tr>
                    {/orders}
                </table>

                {pagination}

            {else}

                <p>You have no orders yet.</p>

            {/if}

        </div>
        
    </div>

</div>
	
{include:footer}