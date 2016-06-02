{include:header}

<div class="container">

    <div id="tpl-shop-account" class="module row">

	<div class="col-md-3">
        {cart_info_leftbar}<br /><br />
        <h2 style="margin-top:50px">Your Checkout Progress</h2>
        <div class="checkout_progress">BILLING ADDRESS</div>
        <div class="checkout_progress_info">Pending</div>
        
        <div class="checkout_progress">SHIPPING ADDRESS</div>
        <div class="checkout_progress_info">Pending</div>
        
        <div class="checkout_progress">SHIPPING</div>
        <div class="checkout_progress_info ">Pending</div>
        
        <div class="checkout_progress">PAYMENT METHOD</div>
        <div class="checkout_progress_info ">Pending</div>
    </div>

	<div class="col-md-9">
		<div class="checkout_box">
        	<div class="checkout_signin">
                <h1 style="margin-top:0">Checkout Method</h1>
                <h2>Sign In</h2>
            
        
                {if errors}
                    <div class="error">
                        {errors}
                    </div>
                {/if}	
                
                <form action="{page:uri}" method="post" class="default">
                    <p>
                    <label for="email">Email Address:</label><br />						
                    <input type="text" name="email" id="email" value="{if user:email}{user:email}{/if}" class="form-control" />
                    </p>
                    <p>
                    <label for="password">Password:</label><br />
                    <input type="password" id="password" name="password" value="" class="form-control" />
                    </p>
                    <p>
                    <input type="submit" id="login" name="login" value="Signin" class="button" />
                    <a style="color:#257F97;" href="/shop/forgotten">Forgot password?</a>
                    </p>
                    
                </form>
			</div>    
            <div style="width:300px;height:217px;float:left">
				<form name="guest_form" action="{site:url}shop/guestcheckout" method="post" class="default">
                    <h2 style="margin-top:40px">Create An Account</h2>
                    <label>
                      <input type="radio" name="guestcheckout" value="1" id="guestcheckout_0" />
                      Register and checkout together
                    </label>
                      
                    <!--h2 style="margin-top:25px">Guest Checkout</h2>
                    <label>
                      <input type="radio" name="guestcheckout" value="2" id="guestcheckout_1" />
                      Checkout without registering
                    </label>
                    <br/><br/-->
                    <input type="submit" id="login" name="login" value="Continue" class="button" />
				</form>
            </div>           
		</div>
        
        <br /><br /><br />
        <div class="checkout_steps">Billing Address</div>
        <div class="checkout_steps">Shipping Address</div>
        <div class="checkout_steps">Shipping</div>
        <div class="checkout_steps">Payment Method</div>
        <div class="checkout_steps">Order Review</div>
        
	</div>

    </div>

</div>	
{include:footer}