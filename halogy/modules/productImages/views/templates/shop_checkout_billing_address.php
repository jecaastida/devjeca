{include:header}

<div id="one-column">
    <div id="tpl-shop-account" class="module">
    
	<div class="col col_left">
        {cart_info_leftbar}
        <h2 style="margin-top:0">Your Checkout Progress</h2>
        <div class="checkout_progress">BILLING ADDRESS {form:billingAddressEdit}</div>
        <div class="checkout_progress_info">{form:billingprogress}</div>
        
        <div class="checkout_progress">SHIPPING ADDRESS</div>
        <div class="checkout_progress_info">Pending</div>
        
        <div class="checkout_progress">SHIPPING</div>
        <div class="checkout_progress_info ">Pending</div>
        
        <div class="checkout_progress">PAYMENT METHOD</div>
        <div class="checkout_progress_info ">Pending</div>
    </div>
    
        <div class="col col_right">
        
            <div class="checkout_steps" {methodstyle}>Checkout Method  <a href="{site:url}shop/checkout_method">edit</a></div>
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
                
        
                    <h1 style="margin-top:4px">Billing Address</h1>
        
                    <div id="billing">
                    
                        <label for="firstName">First Name:</label>
                        <input type="text" name="firstName" value="{form:firstName}" id="firstName" class="formelement" />
                        <br class="clear" />
                    
                        <label for="lastName">Last Name:</label>
                        <input type="text" name="lastName" value="{form:lastName}" id="lastName" class="formelement" />
                        <br class="clear" />
                        
                        <!-- for guest users -->
                        {form:email}
                        <!-- for guest users -->
                        
                        <!-- for those guests who will register -->
                        {form:password}
                        {form:confirmPassword}
                        <!-- for those guests who will register -->
        
                        <label for="billingAddress1">Address 1:</label>
                        <input type="text" name="billingAddress1" value="{form:billingAddress1}" id="billingAddress1" class="formelement" />
                        <br class="clear" />
                    
                        <label for="billingAddress2">Address 2:</label>
                        <input type="text" name="billingAddress2" value="{form:billingAddress2}" id="billingAddress2" class="formelement" />
                        <br class="clear" />
                    
                        <label for="billingAddress3">Address 3:</label>
                        <input type="text" name="billingAddress3" value="{form:billingAddress3}" id="billingAddress3" class="formelement" />
                        <br class="clear" />
                    
                        <label for="billingCity">City:</label>
                        <input type="text" name="billingCity" value="{form:billingCity}" id="billingCity" class="formelement" />
                        <br class="clear" />
        
                        <label for="billingState">State:</label>
                        {select:billingState}
                        <br class="clear" />
                    
                        <label for="billingPostcode">ZIP/Post code:</label>
                        <input type="text" name="billingPostcode" value="{form:billingPostcode}" id="billingPostcode" class="formelement" />
                        <br class="clear" />
                    
                        <label for="billingCountry">Country:</label>
                        {select:billingCountry}
                        <br class="clear" />
                        
                        <label for="phone">Phone:</label>
                        <input type="text" name="phone" value="{form:phone}" id="phone" class="formelement" />
                        <br class="clear" />                        
                        
                    </div>
                        
                    <!-- for guest users -->
                    {form:sameAddress}
                    <!-- for guest users -->
                        
                    <input type="submit" value="Continue" class="button nolabel" />
                    <br class="clear" />
                    
                </form>
    
            </div>
            <div class="checkout_steps">Shipping Address</div>
            <div class="checkout_steps">Shipping</div>
            <div class="checkout_steps">Payment Method</div>
            <div class="checkout_steps">Order Review</div>
            
        </div>
    </div>
</div>
	
{include:footer}