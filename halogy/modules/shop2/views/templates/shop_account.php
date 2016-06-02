{include:header}

<!-- jQuery needed for this -->
<script type="text/javascript">
function hideAddress(){
	if (
		$('input#billingAddress1').val() == $('input#address1').val() &&
		$('input#billingAddress2').val() == $('input#address2').val() &&
		$('input#billingAddress3').val() == $('input#address3').val() &&
		$('input#billingCity').val() == $('input#city').val() &&
		$('select#billingState').val() == $('select#state').val() &&
		$('input#billingPostcode').val() == $('input#postcode').val() &&
		$('select#billingCountry').val() == $('select#country').val()										
	){
		$('div#billing').hide();
		$('input#sameAddress').attr('checked', true);
	}
}
$(function(){
	$('input#sameAddress').click(function(){
		$('div#billing').toggle(200);
		$('input#billingAddress1').val($('input#address1').val());
		$('input#billingAddress2').val($('input#address2').val());
		$('input#billingAddress3').val($('input#address3').val());
		$('input#billingCity').val($('input#city').val());
		$('select#billingState').val($('select#state').val());
		$('input#billingPostcode').val($('input#postcode').val());
		$('select#billingCountry').val($('select#country').val());
	});
	hideAddress();
});
</script>

<div id="tpl-shop" class="container">

	<div class="row">
      <div class="col-md-10 ">

		

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


		

		<form method="post" action="{page:uri}" class="default">
		
			
            
            <div class="col-md-6 col-xs-12">
                <h3>Change Password</h3>
		
                <label for="password">Password:</label>
                <input type="password" name="password" value="" id="password" class="form-control" />
                <br class="clear" />
            
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" name="confirmPassword" value="" id="confirmPassword" class="form-control" />
                <br class="clear" /><br />	
                    
                
                <br class="clear" />  <br class="clear" />  <br class="clear" />
                
            </div>
            
            <div class="col-md-6 col-xs-12">
            <h3>Your Details</h3>
			<label for="email">Email:</label>
			<input type="text" name="email" value="{form:email}" id="email" class="form-control" />
			<br class="clear" />
		
			<label for="firstName">First Name:</label>
			<input type="text" name="firstName" value="{form:firstName}" id="firstName" class="form-control" />
			<br class="clear" />
		
			<label for="lastName">Last Name:</label>
			<input type="text" name="lastName" value="{form:lastName}" id="lastName" class="form-control" />
			<br class="clear" />

			<label for="phone">Phone:</label>
			<input type="text" name="phone" value="{form:phone}" id="phone" class="form-control" />
			<br class="clear" /><br />
            
            </div>
            
            
            
            
            
          <br class="clear" /><br class="clear" /> 
            
            <div class="col-md-6 col-xs-12">
		<br class="clear" />
			<h2>Delivery Address</h2>

                <label for="address1">Address 1:</label>
                <input type="text" name="address1" value="{form:address1}" id="address1" class="form-control" />
                <br class="clear" />
            
                <label for="address2">Address 2:</label>
                <input type="text" name="address2" value="{form:address2}" id="address2" class="form-control" />
                <br class="clear" />
            
                <label for="address3">Address 3:</label>
                <input type="text" name="address3" value="{form:address3}" id="address3" class="form-control" />
                <br class="clear" />
            
                <label for="city">City:</label>
                <input type="text" name="city" value="{form:city}" id="city" class="form-control" />
                <br class="clear" />

                <label for="state">State:</label>
                {select:state}
                <br class="clear" />
            
                <label for="postcode">ZIP/Post code:</label>
                <input type="text" name="postcode" value="{form:postcode}" id="postcode" class="form-control" />
                <br class="clear" />
            
                <label for="country">Country:</label>
                {select:country}
                <br class="clear" /><br />
            
            </div>
            
            <div class="col-md-6 col-xs-12">
            
                <h2>Billing Address</h2>

                <div class="checkbox">
                <label><input type="checkbox" name="sameAddress" value="1" class="checkbox" id="sameAddress" />
                My billing address is the same as my delivery address.</label>
                
                </div>

                <div id="billing">

                    <label for="billingAddress1">Address 1:</label>
                    <input type="text" name="billingAddress1" value="{form:billingAddress1}" id="billingAddress1" class="form-control" />
                    <br class="clear" />
                
                    <label for="billingAddress2">Address 2:</label>
                    <input type="text" name="billingAddress2" value="{form:billingAddress2}" id="billingAddress2" class="form-control" />
                    <br class="clear" />
                
                    <label for="billingAddress3">Address 3:</label>
                    <input type="text" name="billingAddress3" value="{form:billingAddress3}" id="billingAddress3" class="form-control" />
                    <br class="clear" />
                
                    <label for="billingCity">City:</label>
                    <input type="text" name="billingCity" value="{form:billingCity}" id="billingCity" class="form-control" />
                    <br class="clear" />

                    <label for="billingState">State:</label>
                    {select:billingState}
                    <br class="clear" />
                
                    <label for="billingPostcode">ZIP/Post code:</label>
                    <input type="text" name="billingPostcode" value="{form:billingPostcode}" id="billingPostcode" class="form-control" />
                    <br class="clear" />
                
                    <label for="billingCountry">Country:</label>
                    {select:billingCountry}
                    <br class="clear" />
                    
                </div>
            
            </div>

			<br /><br class="clear" />
            
            <input type="submit" value="Save Details" class="btn btn-primary" />

			
			
		</form>

	</div>
	<div class="col-md-2">
	
		<ul class="menu">
			<li><a href="{site:url}shop">Back to Shop</a></li>
			<li><a href="{site:url}shop/account">My Account</a></li>				
			<li><a href="{site:url}shop/subscriptions">My Subscriptions</a></li>
			<li><a href="{site:url}shop/orders">My Orders</a></li>						
		</ul>
		
	</div>

	</div>
</div>
	
{include:footer}