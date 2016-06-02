<script type="text/javascript">
$(function(){
	$.listen('click', 'a.showform', function(event){showForm(this,event);});
	$.listen('click', 'input#cancel', function(event){hideForm(this,event);});

	$('select#filter').change(function(){
		var status = ($(this).val());
		window.location.href = '<?php echo site_url('/admin/shop/subscriptions'); ?>/'+status;
	});	
});	
</script>

<?php
	if ($status == 'active')
	{
		$activeStatus = 'Active';
	}
	elseif ($status == 'cancelled')
	{
		$activeStatus = 'Cancelled';
	}
	else
	{
		$activeStatus = 'All';
	}
?>

<h1 class="headingleft">
	<?php if (intval($this->uri->segment(5))): ?>
		Subscription: <?php
				$subscription = $this->shop->get_subscription($this->uri->segment(5));
				echo $subscription['subscriptionName'];
		?>
	<?php else: ?>
		<?php echo $activeStatus; ?> Subscriptions
	<?php endif; ?>	
</h1>

<div class="headingright">

	<?php if (intval($this->uri->segment(5))): ?>
		<a href="<?php echo site_url('/admin/shop/subscriptions'); ?>" class="button blue">View All</a>
	<?php else: ?>
	
		<label for="filter">
			Filter
		</label> 
	
		<?php
			$options = array(
				'' => 'All Subscriptions',
				'active' => 'Active',
				'cancelled' => 'Cancelled'
			);
			
			echo form_dropdown('filter', $options, $status,'id="filter"');
		?>
	<?php endif; ?>

	<a href="<?php echo site_url('/admin/shop/add_subscription'); ?>" class="showform button blue">Add Subscription</a>

</div>

<div class="clear"></div>
<div class="hidden"></div>

<?php if ($subscriptions): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default">
	<tr>
		<th><?php echo order_link('admin/shop/subscriptions','subscriptionName','Name'); ?></th>
		<th><?php echo order_link('admin/shop/subscriptions','dateCreated','Date Created'); ?></th>
		<th><?php echo order_link('admin/shop/subscriptions','price','Price'); ?></th>
		<th class="narrow">Subscribers</th>
		<th class="narrow"><?php echo order_link('admin/shop/subscriptions','active','Status'); ?></th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>		
	</tr>
<?php foreach ($subscriptions as $sub): ?>
	<tr>
		<td>
			<?php echo anchor('/admin/shop/edit_subscription/'.$sub['subscriptionID'], $sub['subscriptionName'], 'class="showform"'); ?>
			<small>(<?php echo $sub['subscriptionRef']; ?>)</small>
		</td>
		<td><?php echo dateFmt($sub['dateCreated']); ?></td>		
		<td>
			<?php echo currency_symbol(NULL, $sub['currency']).number_format($sub['price'],2); ?>
			/per <?php echo ($sub['term'] == 'M') ? 'month' : 'year'; ?>
		</td>
		<td><?php echo $this->shop->get_num_subscribers($sub['subscriptionID']); ?> [<?php echo anchor('/admin/shop/subscribers/active/'.$sub['subscriptionID'], 'view'); ?>]</td>
		<td>
			<?php
				if ($sub['active']) echo '<span style="color:green"><strong>Active</strong></span>';
				else echo 'Not Active';
			?>
		</td>
		<td><?php echo anchor('/admin/shop/edit_subscription/'.$sub['subscriptionID'], 'Edit', 'class="showform"'); ?></td>
		<td><?php echo anchor('/admin/shop/delete_subscription/'.$sub['subscriptionID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?></td>		
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p>No subscribers were found.</p>

<?php endif; ?>

