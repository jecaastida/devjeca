{include:header}

<div class="container">
    <div id="tpl-shop-account" class="module row">
    
	<div class="col-md-3">
        {cart_info_leftbar}
        <h2 style="margin-top:0">Your Checkout Progress</h2>
        <div class="checkout_progress">BILLING ADDRESS {form:billingAddressEdit}</div>
        <div class="checkout_progress_info">{form:billingprogress}</div>
        
        <div class="checkout_progress">SHIPPING ADDRESS {form:shippingAddressEdit}</div>
        <div class="checkout_progress_info">{form:shippingprogress}</div>
        
        <div class="checkout_progress">SHIPPING {form:shippingEdit}</div>
        <div class="checkout_progress_info ">{form:shipping}</div>
        
        <div class="checkout_progress">PAYMENT METHOD</div>
        <div class="checkout_progress_info ">Pending</div>
    </div>
    
        <div class="col-md-9">
            <div class="checkout_steps" {methodstyle}>Checkout Method <a href="{site:url}shop/checkout_method">edit</a></div>
            <div class="checkout_steps">Billing Address  <a href="{site:url}shop/checkout_billing_address">edit</a></div>
            <div class="checkout_steps">Shipping Address  <a href="{site:url}shop/checkout_shipping_address">edit</a></div>
            <div class="checkout_box">
            
            	<h1 style="margin-top:4px">Shipping</h1>
                <div><strong>Shipping Rate {cart:postage}</strong></div>
                <br/>
                
                <form id="shipping_form" method="post">
                {if cart:items}
                
                <label for="shippingBand">Courier :</label><br />
                <select name="shippingBand" id="shippingBand" onchange="document.getElementById('shipping_form').submit();" class="formelement">
                    {cart:bands}
                </select>
                <br class="clear" /><br />
                {/if}
                
                
                {if cart:modifiers}
            
                    <label for="shippingModifier">Shipping Modifier:</label><br />
                    <select name="shippingModifier" id="shippingModifier" onchange="document.getElementById('shipping_form').submit();" class="formelement">
                        {cart:modifiers}
                    </select>
                    <br class="clear" /><br />
                    
                {/if}
                </form>
                <br/>
                <div><a href="{site:url}shop/checkout_shipping?continue=true" class="button nolabel">Continue</a></div>
                <br/>
            </div>
            <br /><br /><br />
            
            <div class="checkout_steps">Payment Method</div>
            <div class="checkout_steps">Order Review</div>
            
        </div>
    </div>
</div>
	
{include:footer}