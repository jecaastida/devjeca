<?php if (!$this->core->is_ajax()): ?>
	<h1>Add Subscription</h1>
<?php endif; ?>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<label for="subscriptionName">Subscription Name:</label>
	<?php echo @form_input('subscriptionName', set_value('subscriptionName', $data['subscriptionName']), 'class="formelement"'); ?>
	<br class="clear" />	

	<label for="price">CheddarGetter Product:</label>
	<?php echo @form_input('cgProduct',  set_value('cgProduct', $data['cgProduct']), 'class="formelement"'); ?>
	<br class="clear" />

	<label for="price">CheddarGetter Code:</label>
	<?php echo @form_input('cgCode', set_value('cgCode', $data['cgCode']), 'class="formelement"'); ?>
	<br class="clear" />

	<label for="currency">Currency:</label>
	<?php 
		$values = array(
			'GBP' => 'Pounds Sterling (GBP)',
			'USD' => 'US Dollar (USD)',
			'EUR' => 'Euro (EUR)',			
		);
		echo @form_dropdown('currency', $values, set_value('currency', $data['currency']), 'id="currency" class="formelement"'); 
	?>
	<br class="clear" />	

	<label for="price">Price</label>
	<?php echo @form_input('price', set_value('price', number_format($data['price'],2,NULL,'')), 'class="formelement"'); ?>
	<br class="clear" />

	<label for="term">Term:</label>
	<?php 
		$values = array(
			'M' => 'Per Month',
			'Y' => 'Per Year'
		);
		echo @form_dropdown('term', $values, set_value('term', $data['term']), 'id="term" class="formelement"'); 
	?>
	<br class="clear" />

	<label for="description">Description:</label>
	<?php echo @form_textarea('description', set_value('description', $data['description']), 'class="formelement small"'); ?>
	<br class="clear" />
	
	<label for="plan">Plan:</label>
	<?php echo @form_input('plan', set_value('plan', $data['plan']), 'class="formelement"'); ?>
	<br class="clear" />

	<label for="active">Status</label>
	<?php 
		$values = array(
			'1' => 'Active',
			'0' => 'Not Active'
		);
		echo @form_dropdown('active',$values,set_value('active', $data['active']), 'id="active" class="formelement"'); 
	?>
	<br class="clear" /><br />
		
	<input type="submit" value="Add Subscription" class="button nolabel" />
	<input type="button" value="Cancel" id="cancel" class="button grey" />
	
</form>

<br class="clear" />
