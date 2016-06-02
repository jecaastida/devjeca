<?php if (!$this->core->is_ajax()): ?>
	<h1><?php echo (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Shipping postage</h1>
<?php endif; ?>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">
    
    <label for="courier">Courier Name:</label>
	<span class="courier_name"></span><?php echo @form_input('courier_name', $data['courier_name'], 'class="formelement" id="courier_name"'); ?>
	<br class="clear" />
    
	<label for="total">Total:</label>
	<span class="price"><?php echo currency_symbol(); ?></span><?php echo @form_input('total', $data['total'], 'class="formelement small" id="total"'); ?>
	<span class="tip">When the shopping cart total reaches the given amount, then this rate will be applied.</span>
	<br class="clear" />
		
	<label for="cost">Cost:</label>
	<span class="price"><?php echo currency_symbol(); ?></span><?php echo @form_input('cost', $data['cost'], 'class="formelement small" id="cost"'); ?>
	<span class="tip">What do you want to charge for this rate?</span>
	<br class="clear" /><br />
    
    <label for="region">Region:</label>
	<span class="region"></span><?php 
    
    $options = array(
        
            1 => 'NCR',
            2 => 'Luzon',
            3 => 'Viz/Min',
            4 => 'International'
        );

    
    echo  @form_dropdown('region',$options, set_value('region', $data['region']), 'class="formelement" id="region"'); ?>
	<br class="clear" /><br />
		
	<input type="submit" value="Save Changes" class="button nolabel" />
	<input type="button" value="Cancel" id="cancel" class="button grey" />
	
</form>

<br class="clear" />
