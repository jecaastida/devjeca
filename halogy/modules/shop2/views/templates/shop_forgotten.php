{include:header}
<div id="tpl-shop" class="container">
		
	<div class="row">

<h1>Forgotten Password</h1>

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
	

{else}

	<p>Enter the email which you used to sign up and we will send out an email with instructions on how to reset your password.</p>
	
		<div class="col-sm-8">
	<form method="post" action="{page:uri}" class="default">
	
		<label for="email">Email Address:</label>
        <input type="text" name="email" class="form-control" />
		<br class="clear" /><br />
		
		<input type="submit" value="Reset Password" class="btn btn-primary" />
		<br class="clear" />			
	
	</form>
        </div>

{/if}

</div></div>	
{include:footer}