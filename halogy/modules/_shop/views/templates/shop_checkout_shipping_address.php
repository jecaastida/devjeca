{include:header}

<div id="one-column">
    <div id="tpl-shop-account" class="module">
    
	<div class="col col_left">
        {cart_info_leftbar}
        <h2 style="margin-top:0">Your Checkout Progress</h2>
        <div class="checkout_progress">BILLING ADDRESS {form:billingAddressEdit}</div>
        <div class="checkout_progress_info">{form:billingprogress}</div>
        
        <div class="checkout_progress">SHIPPING ADDRESS {form:shippingAddressEdit}</div>
        <div class="checkout_progress_info">{form:shippingprogress}</div>
        
        <div class="checkout_progress">SHIPPING</div>
        <div class="checkout_progress_info ">Pending</div>
        
        <div class="checkout_progress">PAYMENT METHOD</div>
        <div class="checkout_progress_info ">Pending</div>
    </div>
    
        <div class="col col_right">
            <div class="checkout_steps" {methodstyle}>Checkout Method  <a href="{site:url}shop/checkout_method">edit</a></div>
            <div class="checkout_steps">Billing Address  <a href="{site:url}shop/checkout_billing_address">edit</a></div>
            <div class="checkout_box">
        
                {if errors}
                    <div class="error">
                        {errors}
                    </div>
                {/if}
                {if message}
                    <div class="message">
                        {message}
                    </div>
                {/if}
        
        
                <form method="post" action="{page:uri}" class="checkout">
                
        
                    <h1 style="margin-top:4px">Shipping Address</h1>
        
                    <div id="billing">
                        <label for="address1">Address 1:</label>
                        <input type="text" name="address1" value="{form:address1}" id="address1" class="formelement" />
                        <br class="clear" />
                    
                        <label for="address2">Address 2:</label>
                        <input type="text" name="address2" value="{form:address2}" id="address2" class="formelement" />
                        <br class="clear" />
                    
                        <label for="address3">Address 3:</label>
                        <input type="text" name="address3" value="{form:address3}" id="address3" class="formelement" />
                        <br class="clear" />
                    
                        <label for="city">City:</label>
                        <input type="text" name="city" value="{form:city}" id="city" class="formelement" />
                        <br class="clear" />
            
                        <label for="state">State:</label>
                        {select:state}
                        <br class="clear" /> 
                        
                        
                        <label for="state">region:</label>
                        {select:region}
                        <br class="clear" />
                    
                        <label for="postcode">ZIP/Post code:</label>
                        <input type="text" name="postcode" value="{form:postcode}" id="postcode" class="formelement" />
                        <br class="clear" />
                    
                        <label for="country">Country:</label>
                        {select:country}
                        <br class="clear" />
                    </div>
                        
                    <input type="submit" value="Continue" class="button nolabel" />
                    <br class="clear" />
                    
                </form>
    
            </div>
            <div class="checkout_steps">Shipping</div>
            <div class="checkout_steps">Payment Method</div>
            <div class="checkout_steps">Order Review</div>
            
        </div>
    </div>
</div>
	
{include:footer}