<script type="text/javascript">

$(function(){
	$('a.showform').click(function(event){ 
		event.preventDefault();
		$('div.hidden div.inner').load('<?php echo site_url('/admin/moto/addTrack'); ?>', function(){ $('div.hidden').slideToggle('400'); });
	});
});
</script>

<h1 class="headingleft">Track Owners Manager</h1>

<div class="headingright">
		<a href="<?php echo site_url('/admin/tracks/addTrack'); ?>" class="button blue">Add Track Owner</a>
</div>		

<br class="clear" />

<div class="hidden"></div>

<?php if ($tracks): ?>

	<?php echo $this->pagination->create_links(); ?>
	
	<table class="default clear">
		<thead>
			<th>Owner Name</th>
			<th>Email</th>
			<th>Track Name</th>
			<th>Address</th>
			<th>State</th>
			<th>Country</th>
			<th>Subscription</th>
			<th>Status</th>
			<th class="tiny">&nbsp;</th>
			<th class="tiny">&nbsp;</th>
		</thead>
		<tbody class='order'>
	<?php foreach ($tracks as $track): ?>
		<tr id="tracks-<?php echo $track['tracksID']; ?>">
			<td><?php echo $track['firstName']." ".$track['lastName']; ?></td>
			<td><?php echo $track['email']; ?></td>
			<td><?php echo $track['trackname']; ?></td>	
			<td><?php echo $track['address']; ?></td>	
			<td><?php echo $track['t_state']; ?></td>	
			<td><?php echo $track['t_country']; ?></td>	
			<td><?php echo $track['subscriptionName']; ?></td>	
			<td><?php echo ($track['status'] == 1?'ACTIVE':'INACTIVE'); ?></td>	
			<td><?php echo anchor('admin/tracks/editTrack/'.$track['tracksID'], 'EDIT');?></td>
            <td ><?php echo anchor('/admin/tracks/delete_tracks/'.$track['tracksID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?></td>
		</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
	
	<?php echo $this->pagination->create_links(); ?>
	
	<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

	<p>There are no track owners yet.</p>

<?php endif; ?>

