{include:header}

<div id="one-column">
    <div id="tpl-shop-account" class="module">
    
	<div class="col col_left">
        <h2 style="margin-top:0">Your Checkout Progress</h2>
        <div class="checkout_progress">BILLING ADDRESS</div>
        <div class="checkout_progress_info">{form:billingprogress}</div>
        
        <div class="checkout_progress">SHIPPING ADDRESS</div>
        <div class="checkout_progress_info">{form:shippingprogress}</div>
        
        <div class="checkout_progress">SHIPPING</div>
        <div class="checkout_progress_info ">{form:shipping}</div>
        
        <div class="checkout_progress">PAYMENT METHOD</div>
        <div class="checkout_progress_info ">{form:paymentmethod}</div>
    </div>
     
        <div class="col col_right">
            <div class="checkout_steps" {methodstyle}>Checkout Method</div>
            <div class="checkout_steps">Billing Address </div>
            <div class="checkout_steps">Shipping Address  </div>
            <div class="checkout_steps">Shipping </div>
            <div class="checkout_steps">Payment Method </div>
            <div class="checkout_box">
            
            	<h1 style="margin-top:4px">Order Review</h1>
                {order:transMsg}
				<h2>Order ID #: <strong>{order:id}</strong></h2>

                <table class="default">
                    <tr>
                        <th width="50%" align="left">Delivery Address</th>
                        
                  </tr>
                    <tr>
                        <td valign="top">		
                            <p>
                                {order:first-name} {order:last-name}<br />
                                {order:address1}<br />
                                {order:address2}<br />
                                {order:address3}<br />
                                {order:city}<br />
                                {order:country}<br />
                                {order:postcode}<br />
                                <small>Phone:</small> {order:phone}<br />
                                <small>Email:</small> {order:email}
                            </p>
                        </td>
                        					
                    </tr>
        
                </table>
            
                <br />
            
                <h3>Products Ordered</h3>
                
                <table class="default">
                    <tr>
                        <th align="left">Product</th>
                        <th align="left">Quantity</th>
                        <th width="80" align="left">Price ({site:currency})</th>
                  </tr>
                    {if items}
                        {items}		
                        <tr>
                            <td><a href="{item:link}">{item:title}</a> <small>{item:details}</small></td>
                            <td>{item:quantity}</td>
                            <td>{item:amount}</td>
                        </tr>				
                        {/items}
                    {/if}
                    <tr>
                        <td colspan="3" ><hr /></td>
                    </tr>
                    {if order:discounts}
                        <tr>
                            <td colspan="2">Discounts applied:</td>
                            <td>({order:discounts})</td>
                        </tr>
                    {/if}
                    <tr>
                        <td colspan="2">Sub total:</td>
                        <td>{order:subtotal}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Shipping:</td>
                        <td>{order:postage}</td>
                    </tr>
                    {if order:tax}
                        <tr>
                            <td colspan="2">Tax:</td>
                            <td>{order:tax}</td>
                        </tr>
                    {/if}
                    <tr>
                        <td colspan="2"><strong>Total amount:</strong></td>
                        <td><strong>{order:total}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3" ><hr /></td>
                    </tr>								
                </table>

            </div>
        </div>
    </div>
</div>
	
{include:footer}