{include:header}

<div id="tpl-shop" class="container">

	<div class="row">

        <div class="col-md-6 col-xs-12">
		<h1>Login</h1>

		{if errors}
			
            <div class="alert alert-danger error">
             <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				{errors}
			</div>
		{/if}
     

		<form action="{page:uri}" method="post" class="default">
						
						
			<label for="email">Email address:</label>
			<input type="text" id="email" name="email" value="" class="form-control" />
			<br class="clear" /><br />
		
			<label for="password">Password:</label>
			<input type="password" id="password" name="password" value="" class="form-control" />
		
			<input type="submit" id="login" name="login" value="Login" class="btn btn-primary" />
			<br class="clear" />
			
		</form>

		<br />

		<h3>Forgotten your password?</h3>

		<p>We can <a href="/shop/forgotten">reset it for you</a>.</p>

		<br />
        
        </div>
        
        <div class="col-md-6 col-xs-12">
        
        
		<h3><a href="/shop/create_account/">Create a new account?</a></h3>

		
		<br />
        
        </div>

	</div>
	

</div>
	
{include:footer}