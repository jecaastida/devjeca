<style type="text/css">
.ac_results { padding: 0px; border: 1px solid black; background-color: white; overflow: hidden; z-index: 99999; }
.ac_results ul { width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0; }
.ac_results li { margin: 0px; padding: 2px 5px; cursor: default; display: block; font: menu; font-size: 12px; line-height: 16px; overflow: hidden; }
.ac_results li span.email { font-size: 10px; } 
.ac_loading { background: white url('<?php echo $this->config->item('staticPath'); ?>/images/loader.gif') right center no-repeat; }
.ac_odd { background-color: #eee; }
.ac_over { background-color: #0A246A; color: white; }
</style>

<script language="javascript" type="text/javascript" src="<?php echo $this->config->item('staticPath'); ?>/js/jquery.fieldreplace.js"></script>
<script type="text/javascript">
$(function(){
    $('#searchbox').fieldreplace();
	function formatItem(row) {
		if (row[0].length) return row[1]+'<br /><span class="email">('+row[0]+')</span>';
		else return 'No results';
	}
	$('#searchbox').autocomplete("<?php echo site_url('/admin/shop/ac_subscribers'); ?>", { delay: "0", selectFirst: true, matchContains: true, formatItem: formatItem, minChars: 2 });
	$('#searchbox').result(function(event, data, formatted){
		$(this).parent('form').submit();
	});	
	$('select#filter').change(function(){
		var status = ($(this).val());
		window.location.href = '<?php echo site_url('/admin/shop/subscribers'); ?>/'+status;
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
	<?php echo $activeStatus; ?> Subscribers 
	<?php if (intval($this->uri->segment(5))): ?>
		<small>(<?php
				$subscription = $this->shop->get_subscription($this->uri->segment(5));
				echo anchor('/admin/shop/subscriptions/active/'.$subscription['subscriptionID'], $subscription['subscriptionName']);
		?>)</small>
	<?php endif; ?>
</h1>

<div class="headingright">

	<form method="post" action="<?php echo site_url('/admin/shop/subscribers'); ?>" class="default" id="search">
		<input type="text" name="q" id="searchbox" class="formelement inactive" title="Search Reference..." />
		<input type="image" src="<?php echo $this->config->item('staticPath'); ?>/images/btn_search.gif" id="searchbutton" />
	</form>

	<label for="filter">
		Filter
	</label> 

	<?php
		$options = array(
			'' => 'All Subscribers',
			'active' => 'Active',
			'cancelled' => 'Cancelled'
		);
		
		echo form_dropdown('filter', $options, $status,'id="filter"');
	?>

	<?php if (intval($this->uri->segment(5)) || $this->input->post('q')): ?>
		<a href="<?php echo site_url('/admin/shop/subscribers'); ?>" class="button blue">View All</a>
	<?php endif; ?>

</div>

<div class="clear"></div>

<?php if ($subscribers): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default">
	<tr>
		<th><?php echo order_link('admin/shop/subscribers','referenceID','Reference ID'); ?></th>
		<th><?php echo order_link('admin/shop/subscribers','dateCreated','Date Created'); ?></th>
		<th><?php echo order_link('admin/shop/subscribers','fullName','Full Name'); ?></th>
		<th><?php echo order_link('admin/shop/subscribers','email','Email'); ?></th>
		<th>Subscription</th>		
		<th><?php echo order_link('admin/shop/subscribers','active','Status'); ?></th>	
		<th class="narrow">&nbsp;</th>
	</tr>
<?php foreach ($subscribers as $sub): ?>
	<tr>
		<td><?php echo anchor('/admin/shop/sub_payments/'.$sub['referenceID'].'/'.$sub['subscriberID'], '#'.$sub['referenceID']); ?></td>
		<td><?php echo dateFmt($sub['dateCreated']); ?></td>		
		<td><?php echo $sub['fullName']; ?></td>
		<td><?php echo $sub['email']; ?></td>		
		<td>
			<?php
				$subscription = $this->shop->get_subscription($sub['subscriptionID']);
				echo anchor('/admin/shop/subscriptions/active/'.$subscription['subscriptionID'], $subscription['subscriptionName']);
			?>
		</td>
		<td>
			<?php
				if ($sub['active']) echo '<span style="color:green"><strong>Active</strong></span>';
				else echo 'Cancelled';
			?>
		</td>

		<td><?php echo anchor('/admin/shop/sub_payments/'.$sub['referenceID'].'/'.$sub['subscriberID'], 'View Payments'); ?></td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p>No subscribers were found.</p>

<?php endif; ?>

