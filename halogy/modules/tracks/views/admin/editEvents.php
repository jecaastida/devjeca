<script type="text/javascript">
$(function(){
	$('.toggle-event').click(function(event){ 
		event.preventDefault();		
		$('div#upload-event').slideToggle('400');
		$('div#upload-zip:visible, div#loader:visible').slideToggle('400');
	});

	$('a.lightbox').lightBox({imageLoading:'<?php echo $this->config->item('staticPath'); ?>/images/loading.gif',imageBtnClose: '<?php echo $this->config->item('staticPath'); ?>/images/lightbox_close.gif',imageBtnNext:'<?php echo $this->config->item('staticPath'); ?>/image/lightbox_btn_next.gif',imageBtnPrev:'<?php echo $this->config->item('staticPath'); ?>/image/lightbox_btn_prev.gif'});

	$('a.showform').click(function(event){ 
			event.preventDefault();
			$('div.hidden2 div.inner').load($(this).attr('href'), function(){ $('div.hidden').slideToggle('400'); });
		});
});
</script>
<div class="hidden2"></div>

<div id="upload-event" class="hidden clear">
	<form method="post" action="<?php echo site_url("/admin/tracks/editEvents/".$id."/".$trackID); ?>" enctype="multipart/form-data" class="default">
		<input name="add_event" value="1" type="hidden">
		
		<label for="imageName">Event Type:</label>
		
		<?php echo $type; ?>

		<br class="clear" />
		
		<label for="imageName">Event Schedule:</label>
		<?php echo $recur; ?>
		<br class="clear" />
		
		<label for="imageName">Event Title:</label>
		<?php echo $title; ?>
		<br class="clear" />
		
		<label for="imageName">Event Description:</label>
		<?php echo $description; ?>
		<br class="clear" />
		
		
		<?php echo $city1; ?>

		<?php echo $state; ?>
		
		
		<label for="imageName">Start Date:</label>
		<?php echo $start; ?>
		<br class="clear" />
		
		<label for="imageName">End Date:</label>
		<?php echo $end; ?>
		<br class="clear" />
		
		<label for="imageName">Time:</label>
		<?php echo $time; ?>
		<br class="clear" />
		
		<div id='recur-event'>
		<label for="imageName">Day of the Week:</label>
		<?php echo $day; ?>
		<br class="clear" />
		</div>

		<input type="submit" value="Save Changes" class="button nolabel" id="submit" />
		<a href="#" class="button cancel grey">Cancel</a>		
		
	</form>
</div>


	<h2>Event Manager</h2>
	
	<form method="post" action="<?php echo site_url("/admin/tracks/editEvents/".$id."/".$trackID); ?>" enctype="multipart/form-data" class="default">
		<a href="#" class="button toggle-event blue">Add Event</a>
		
	<?php if ($events): ?>
		<input type="submit" value="Delete Event(s)" class="button nolabel" id="submit" />
		<input name="delete_events" value="1" type="hidden">
		
	<?php echo $this->pagination->create_links(); ?>
	
	<table class="default clear">
		<thead>
			<th class='tiny'>DELETE?</th>
			<th>Event Type</th>
			<th>Title</th>
			<th>Description</th>
			<th>Start</th>
			<th>Time</th>
			<th>End</th>
			<!--th class="tiny">&nbsp;</th-->
		</thead>
		<tbody class='order'>
	<?php foreach ($events as $event): ?>
		<tr id="tracks-<?php echo $event['eventID']; ?>">
			<td><input name="eventdata[<?php echo $event['eventID']; ?>]" value="1" type="checkbox"></td>
			<td><?php echo $event['type']; ?></td>
			<td><?php echo $event['eventTitle']; ?></td>
			<td><?php echo $event['description']; ?></td>
			<td><?php echo $event['eventDate']; ?></td>
			<td><?php echo $event['time']; ?></td>
			<td><?php echo $event['eventEnd']; ?></td>
			<!--td><php echo anchor('admin/tracks/editSingleEvent/'.$event['eventID'].'/'.$trackID, 'EDIT');></td-->
		</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
		
		<?php echo $this->pagination->create_links(); ?>
	<?php else: ?>

	<p class="clear">This track has no events yet.</p>

	<?php endif; ?>
	</form>

<script src="/static/new/js/jquery-1.11.0.min.js"></script>
<script src="/static/new/js/jquery-ui.min.js"></script>
<link rel="stylesheet" href="/static/new/css/jquery-ui.min.css">
<script>
jq111 = jQuery.noConflict( true );
jq111( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
jq111(function(){
	jq111("#recur-event").css('display','none');
	jq111("#recur").change(function() {
		if($(this).val() == 'single'){
			jq111('#recur-event').hide();
		}else if($(this).val() == 'recur'){
			jq111('#recur-event').show();
			jq111('input[name="day"]')
		}
	});
});
</script>