<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

<h1 class="headingleft">Edit Gateway Settings</h1>

<div class="headingright">
	<!--<input type="submit" name="view" value="View Post" class="button blue" /> -->
	<input type="submit" value="Save Changes" class="button" />
</div>

<div class="clear"></div>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>
<?php if (isset($message)): ?>
	<div class="message">
		<?php echo $message; ?>
	</div>
<?php endif; ?>


<label for="postName">API KEY:</label>
<?php echo @form_input('shop_api_key', $data['shop_api_key'], 'id="shop_api_key" class="formelement code half"'); ?>
<br class="clear" />

<label for="postName">Private KEY:</label>
<?php echo @form_input('shop_private_key', $data['shop_private_key'], 'id="shop_private_key" class="formelement code half"'); ?>
<br class="clear" />

<label for="postName">Subdomain:</label>
<?php echo @form_input('shop_subdomain', $data['shop_subdomain'], 'id="shop_subdomain" class="formelement code half"'); ?>
<br class="clear" />



	
<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	
</form>
