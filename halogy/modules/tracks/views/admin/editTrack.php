<script type="text/javascript">

$(function(){
	$('a.showtab').click(function(event){
		event.preventDefault();
		var div = $(this).attr('href'); 
		$('div.tab').hide();
		$(div).show();
	});
	$('ul.innernav a').click(function(event){
		event.preventDefault();
		$(this).parent().siblings('li').removeClass('selected'); 
		$(this).parent().addClass('selected');
	});
	$('div.tab:not(#tab1)').hide();	

	$('#tab4').load("/admin/tracks/editImages/<?php echo $userid;?>/<?php echo $id;?>");
	$('#tab5').load("/admin/tracks/editVideos/<?php echo $userid;?>/<?php echo $id;?>");
	$('#tab6').load("/admin/tracks/editEvents/<?php echo $userid;?>/<?php echo $id;?>");
	
	$('#getNewPw').click(function () {
		$('#password').val( shuffle("AaBbCcDdEeFfGgHhiJjKkLMmNnoPpQqRrSsTtUuVvWwXxYyZz23456789!?$%#&@+-*=_.,:;()".split('')).slice(0, 10).join('') );
		alert('Copy the password:\n\n'+$('#password').val());
	});
	
});
	function shuffle(d) {
		for (var c = d.length - 1; c > 0; c--) {
			var b = Math.random() * (c + 1) | 0;
			var a = d[c];
			d[c] = d[b];
			d[b] = a;
		}
		return d
	};
</script>


	<h1 class="headingleft">Edit Track Owner <small>(<a href="<?php echo site_url('/admin/tracks'); ?>">Back to Track Owner</a>)</small></h1>

	<div class="headingright">
	<script>
		$(function(){
			$('#save-trackdata').click(function(){
				$('#trackdata').submit();
			});
		});
	</script>
		<input type="submit" value="Save Changes" class="button" id='save-trackdata' />
	</div>
	
	<div class="clear"></div>
	
	<?php if ($errors = validation_errors()): ?>
		<div class="error">
			<?php echo $errors; ?>
		</div>
	<?php endif; ?>
	<?php if (isset($message)): ?>
		<div class="message clear">
			<?php echo $message; ?>
		</div>
	<?php endif; ?>

<ul class="innernav clear">
	<li class="selected"><a href="#tab1" class="showtab">Owner Details</a></li>
	<li><a href="#tab2" class="showtab">Track Details</a></li>
	<li><a href="#tab3" class="showtab">Payment Details</a></li>
	<li><a href="#tab4" class="showtab">Picture Manager</a></li>
	<li><a href="#tab5" class="showtab">Video Manager</a></li>
	<li><a href="#tab6" class="showtab">Event Manager</a></li>
</ul>

<br class="clear" />

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default" enctype="multipart/form-data" id='trackdata'>
<div id="tab1" class="tab">

	<h2>Track Owner</h2>

	<label for="companyName">Track Owner:</label>
	<?php echo @form_input('firstName',set_value('firstName', $data['firstName']), 'id="firstName" class="formelement"'); ?>
	<?php echo @form_input('lastName',set_value('lastName', $data['lastName']), 'id="lastName" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Email Address:</label>
	<?php echo @form_input('email',set_value('email', $data['email']), 'id="email" class="formelement" required'); ?>
	<br class="clear" />
	
	<label for="companyName">Password:</label>
	<?php echo @form_password('password',set_value('password'), 'id="password" class="formelement"'); ?>
	<br class="clear" />
	<span href='#' id='getNewPw' class="button">Generate Password</span>
	<br class="clear" />
	
	<label for="companyName">Billing Address1:</label>
	<?php echo @form_input('billingAddress1',set_value('billingAddress1', $data['billingAddress1']), 'id="billingAddress1" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Billing Address2:</label>
	<?php echo @form_input('billingAddress2',set_value('billingAddress2', $data['billingAddress2']), 'id="billingAddress2" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Billing Address3:</label>
	<?php echo @form_input('billingAddress3',set_value('billingAddress3', $data['billingAddress3']), 'id="billingAddress3" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Billing State:</label>
	<?php echo @display_states('billingState',set_value('billingState', $data['billingState']), 'id="billingState" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Billing Country:</label>
	<?php echo @display_countries('billingCountry',set_value('billingCountry', $data['billingCountry']), 'id="billingCountry" class="formelement"'); ?>
	<br class="clear" />

	<label for="companyName">Subscription Plan:</label>
	<?php 
		echo @form_dropdown('subscriptionID',$subscriptions,set_value('subscriptionID', $data['subscriptionID']), 'id="subscriptionID" class="formelement"'); 
	?>
	<br class="clear" />
	
	<label for="companyName">Status:</label>
	<?php 
		$values = array(
			1 => 'Active',
			0 => 'Inactive'
		);
		echo @form_dropdown('status',$values,set_value('status', $data['status']), 'id="status" class="formelement"'); 
	?>
	<br class="clear" />

	
</div>

<div id='tab2' class='tab'>
	
	<label for="companyName">Track Profile Image:</label>
	<img src='/static/uploads/<?php echo $tracks['profile_img']; ?>' style='max-width:100px; max-height:100px;'>
	<input name="image" value="" type="file">
	<br class="clear" />
	
	<label for="companyName">Track Name:</label>
	<?php echo @form_input('_trackname',set_value('trackname', $tracks['trackname']), 'id="trackname" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Track Description:</label>
	<?php echo @form_input('_trackdesc',set_value('trackdesc', $tracks['trackdesc']), 'id="trackdesc" class="formelement"'); ?>
	<br class="clear" />

	<label for="companyName">Address:</label>
	<?php echo @form_input('_address',set_value('_address', $tracks['address']), 'id="address" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">City:</label>
	<?php echo @form_input('_city',set_value('_city', $tracks['city']), 'id="city" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">State:</label>
	<?php echo @display_states('_state',set_value('_state', $tracks['t_state']), 'id="state" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Country:</label>
	<?php echo @display_countries('_country',set_value('_country', $tracks['t_country']), 'id="country" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Phone:</label>
	<?php echo @form_input('_phone',set_value('_phone', $tracks['phone']), 'id="phone" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Email:</label>
	<?php echo @form_input('_email',set_value('_email', $tracks['email']), 'id="email" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Website:</label>
	<?php echo @form_input('_website',set_value('_website', $tracks['website']), 'id="website" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Facebook:</label>
	<?php echo @form_input('_facebook',set_value('_facebook', $tracks['facebook']), 'id="facebook" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Twitter ID:</label>
	<?php echo @form_input('_twitter',set_value('_twitter', $tracks['twitter']), 'id="twitter" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Instagram:</label>
	<?php echo @form_input('_instagram',set_value('_instagram', $tracks['instagram']), 'id="instagram" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Youtube:</label>
	<?php echo @form_input('_youtube',set_value('_youtube', $tracks['youtube']), 'id="youtube" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="companyName">Latitude:</label>
	<?php echo @form_input('_latitude',set_value('_latitude', $tracks['latitude']), 'id="latitude" class="formelement"'); ?>
	<br class="clear" />

	<label for="companyName">Longitude:</label>
	<?php echo @form_input('_longitude',set_value('_longitude', $tracks['longitude']), 'id="longitude" class="formelement"'); ?>
	<br class="clear" />
	
	<div style='width:100%;'>
		
		<div style='width:49%;float:left;'>
			<label>Track Categories</label>
			<div class="checkbox">
			<?php foreach($trackcats as $tc){
				echo $tc['data1'];
			}?>
			</div>
		</div>

		<div style='width:49%;float:right;'>
			<label>Machine Types</label>
			<div class="checkbox">
			<?php foreach($machinecats as $mc){
				echo $mc['data2'];
			}?>
			</div>
		</div>
		
	</div>

</div>

<div id="tab3" class="tab">

	<h2>Payment Details</h2>
	
	<label for="companyName">Amount:</label>
	<?php echo @$data['amount']; ?>
	<br class="clear" />

	<label for="companyName">Date:</label>
	<?php echo @$data['date_created']; ?>
	<br class="clear" />

	<label for="companyName">TXN Code:</label>
	<?php echo @$data['txn_code']; ?>
	<br class="clear" />
	
</div>
</form>

<div id="tab4" class="tab">

</div>
	
<div id="tab5" class="tab">

</div>


<div id="tab6" class="tab">

</div>





<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	