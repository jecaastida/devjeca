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
        
        <div class="checkout_progress">SHIPPING {form:shippingEdit}</div>
        <div class="checkout_progress_info ">{form:shipping}</div>
        
        <div class="checkout_progress">PAYMENT METHOD {form:paymentEdit}</div>
        <div class="checkout_progress_info ">{form:paymentmethod}</div>
    </div>
    
        <div class="col col_right">
            <div class="checkout_steps" {methodstyle}>Checkout Method <a href="{site:url}shop/checkout_method">edit</a></div>
            <div class="checkout_steps">Billing Address  <a href="{site:url}shop/checkout_billing_address">edit</a></div>
            <div class="checkout_steps">Shipping Address  <a href="{site:url}shop/checkout_shipping_address">edit</a></div>
            <div class="checkout_steps">Shipping  <a href="{site:url}shop/checkout_shipping">edit</a></div>
            <div class="checkout_box">
            
            	<h1 style="margin-top:4px">Payment Method</h1>
                
                {if errors}
                    <div class="error">
                        {errors}
                    </div>
                {/if}
                
                {paymentmethods}
                
                
                    <form action="{site:url}shop/checkout" method="post" class="default">
                        
                        <div >
                            <input type="submit" value="Continue" class="button" />
                        </div>
                    </form>
              
            </div>
            
            <div class="checkout_steps">Order Review</div>
            
        </div>
    </div>
</div>
	
{include:footer}