{include:header}

<div id="tpl-shop" class="module">

	<div class="col col1">

		<h1>Login</h1>

		
		{if errors}
			<div class="error">
				{errors}
			</div>
		{/if}	
		
		<form action="{page:uri}" method="post" class="default">
						
						
			<label for="email">Email address:</label>
			<input type="text" id="email" name="email" value="" class="formelement" />
			<br class="clear" /><br />
		
			<label for="password">Password:</label>
			<input type="password" id="password" name="password" value="" class="formelement" />
		
			<input type="submit" id="login" name="login" value="Login" class="button" />
			<br class="clear" />
			
		</form>

		<br />

		<h3><a href="/shop/forgotten">Forgotten your password?</a></h3>

		<p>That's ok, we can <a href="/shop/forgotten">reset it for you</a>.</p>

		<br />

		<h3><a href="/shop/create_account/checkout">Want to create a new account?</a></h3>

		<p>Alternatively you can <a href="/shop/create_account/checkout">create a new account</a> if you want to.</p>		

		<br />

	</div>
	

</div>
	
{include:footer}