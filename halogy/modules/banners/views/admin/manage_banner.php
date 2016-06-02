<script type="text/javascript">
function preview(el){
	$.post('<?php echo site_url('/admin/shop/preview'); ?>', { body: $(el).val() }, function(data){
		$('div.preview').html(data);
	});
}
$(function(){
	$('div.category>span, div.category>input').hover(
		function() {
			if (!$(this).prev('input').attr('checked') && !$(this).attr('checked')){
				$(this).parent().addClass('hover');
			}
		},
		function() {
			if (!$(this).prev('input').attr('checked') && !$(this).attr('checked')){
				$(this).parent().removeClass('hover');
			}
		}
	);	
	$('div.category>span').click(function(){
		if ($(this).prev('input').attr('checked')){
			$(this).prev('input').attr('checked', false);
			$(this).parent().removeClass('hover');
		} else {
			$(this).prev('input').attr('checked', true);
			$(this).parent().addClass('hover');
		}
	});
	$('a.showtab').click(function(event){
		event.preventDefault();
		var div = $(this).attr('href'); 
		$('div#details, div#desc, div#variations').hide();
		$(div).show();
	});
	$('ul.innernav a').click(function(event){
		event.preventDefault();
		$(this).parent().siblings('li').removeClass('selected'); 
		$(this).parent().addClass('selected');
	});
	$('.addvar').click(function(event){
		event.preventDefault();
		$(this).parent().parent().siblings('div').toggle('400');
	});
	$('div#desc, div#variations').hide();

	$('input.save').click(function(){
		var requiredFields = 'input#productName, input#catalogueID';
		var success = true;
		$(requiredFields).each(function(){
			if (!$(this).val()) {
				$('div.panes').scrollTo(
					0, { duration: 400, axis: 'x' }
				);					
				$(this).addClass('error').prev('label').addClass('error');
				$(this).focus(function(){
					$(this).removeClass('error').prev('label').removeClass('error');
				});
				success = false;
			}
		});
		if (!success){
			$('div.tab').hide();
			$('div.tab:first').show();
		}
		return success;
	});	
	$('textarea#body').focus(function(){
		$('.previewbutton').show();
	});
	$('textarea#body').blur(function(){
		preview(this);
	});
	preview($('textarea#body'));
});
</script>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">

<h1 class="headingleft">

           <span>Edit Banner</span>

<small>(<a href="<?php echo site_url('/admin/banners/viewall'); ?>">View all Banners</a>)</small></h1>

<div class="headingright">
	<input type="submit" value="Save Changes" class="button save" />
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

<div id="details" class="tab">

	<h2 class="underline"><?php echo $data['banner_name']; ?></h2>
	
	<label for="offerCode">Banner URL:</label>
	<?php echo @form_input('banner_url',set_value('banner_url', $data['banner_url']), ' id="banner_url" class="formelement"'); ?>
	<br class="clear" />
	
	<label for="banner_desc">Banner Description</label>
	<?php echo @form_input('headline',set_value('headline', $data['headline']), 'id="headline" value="-----" style="width:255px !important;height:75px !important;" class="formelement"'); ?>
	
	<?php echo @form_hidden('time_delay',set_value('time_delay', $data['time_delay']), ' id="time_delay" value="1" class="formelement small"'); ?>

	
	
	
	
	<label for="sequence_order">Order:</label>
	<?php 
		$values = array(
			'', '- Select order -',
			1 => '1',
			2 => '2',
			3 => '3',
			4 => '4',			
			5 => '5',
			6 => '6',
			7 => '7'
			
			
		);
		echo @form_dropdown('sequence_order',$values,set_value('sequence_order', $data['sequence_order']), 'id="sequence_order" class="formelement small"'); 
	?>
	<br class="clear" />

	<label for="image">Banner Image:</label>
			<?php if (isset($imagePath)) { ?>
			<img src="<?php echo $imagePath; ?>" alt="Banner Image" /><br /><br />	
			<?php } ?>

	<div class="uploadfile ir-upload">

		<?php echo @form_upload('image',$this->validation->image, 'size="16" id="image"'); ?>
                
	</div>
        <span class="tip">Image size should be 1280pixels wide and 480pixels high.</span>
	<br class="clear" />

	<!--<label for="image_nor">Banner Image (Norwegian):</label>
			<?php if (isset($imagePath_nor)) { ?>
			<img src="<?php echo $imagePath_nor; ?>" alt="Banner Image" /><br /><br />	
			<?php } ?>

	<div class="uploadfile ir-upload">

		<?php echo @form_upload('image_nor',$this->validation->image, 'size="16" id="image_nor"'); ?>
                
	</div>
        <span class="tip">Image size should be 982 pixels wide and 275 pixels high.</span>
	<br class="clear" />-->	
	
</div>

<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	
</form>
