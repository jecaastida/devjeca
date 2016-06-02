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
	elseif ($status == 'inactive')
	{
		$activeStatus = 'Inactive';
	}
	else
	{
		$activeStatus = 'All';
	}
?>

<h1 class="headingleft">
	Subscription Payments <small>(<a href="<?php echo site_url('/admin/shop/subscribers'); ?>">Back to Subscribers</a>)</small></h1>

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

</div>

<div class="message clear">
	<p>
		<strong>Subscription ID:</strong> #<?php echo $subscriber['referenceID']; ?><br />
		<strong>Email:</strong> <?php echo $subscriber['email']; ?><br />
		<strong>Status:</strong> <?php echo ($subscriber['active']) ? 'Active' : 'Cancelled'; ?>
	</p>
</div>

<?php if ($sub_payments): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default">
	<tr>
		<th><?php echo order_link('admin/shop/subscribers','dateCreated','Payment Date'); ?></th>
		<th><?php echo order_link('admin/shop/subscribers','amount','Amount ('.currency_symbol().')'); ?></th>		
		<th class="narrow">&nbsp;</th>
	</tr>
<?php foreach ($sub_payments as $sub): ?>
	<tr>
		<td><?php echo dateFmt($sub['dateCreated']); ?></td>
		<td><?php echo currency_symbol().number_format($sub['amount'],2); ?></td>
		<td><?php echo anchor('/shop/invoice/subscription/'.$sub['paymentID'], 'View Invoice'); ?></td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php endif; ?>

