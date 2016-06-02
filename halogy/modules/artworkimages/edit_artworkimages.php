<!--select artwork title-->

<?php

$this->db->select('artworkTitle');
$this->db->where('artworkID',$this->input->get('id'));
$artworkquery = $this->db->get('ha_artworks');
if ($artworkquery->num_rows())
{
	$artworkqueryresult = $artworkquery->result_array();			
	foreach($artworkqueryresult as $artworkqueryvar)
	{	
	
	$artworkTitle = $artworkqueryvar['artworkTitle'];
	}
	
}

?>


<?php
	if ($this->input->get('type'))
	{
		$urltype = "?type=1";
		$urltype2 = "&type=1";
		$urldraft = "_draft";
	}
	else
	{
		$urltype = "";
		$urltype2 = "";
		$urldraft = "";
	}
?>




<script type="text/javascript">
function preview(el){
	$.post('<?php echo site_url('/admin/artworkimages/preview'); ?>', { body: $(el).val() }, function(data){
		$('div.preview').html(data);
	});
}
$(function(){
	$("input.datebox").datebox();
	$('textarea#body').focus(function(){
		$('.previewbutton').show();
	});
	$('textarea#body').blur(function(){
		preview(this);
	});
	preview($('textarea#body'));	
});
</script>

<form name="form" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>?id=<?php echo $this->input->get('id'); ?><?php echo $urltype2; ?>" enctype="multipart/form-data" class="default">

	<h1 class="headingleft"><?php echo $artworkTitle;?> - Edit Image <small>(<a href="<?php echo site_url('/admin/artworkimages/viewall?id='.$this->input->get('id').''.$urltype2.''); ?>">Back to images</a>)</small></h1>
	
	<div class="headingright">
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
    
    <label for="image">Image:  ( jpg , gif , png only )</label>
    <div class="uploadfile">
	<?php if ($data['image']) { ?>
    <img src="<?php echo site_url(); ?>/thumb.php?src=/static/uploads/artists/artworkimages/<?php echo $data['image']; ?>&w=120">
    <?php } ?>
    
    <input type="file" name="image" value="" size="16" id="image" />	
    </div>
    <!--<span class="tip">Lorem ipsum dolor</span>-->
    <br class="clear" />

	<label for="image_alt_txt">Image Alt Text:</label>
	<?php echo @form_input('image_alt_txt', set_value('image_alt_txt', $data['image_alt_txt']), 'id="image_alt_txt" class="formelement"'); ?>
	<br class="clear" /><br />
   	<?php echo @form_hidden('artwork_id', set_value('artwork_id', $this->input->get('id')), 'id="artwork_id" class="formelement"'); ?>

        
	<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	
</form>
