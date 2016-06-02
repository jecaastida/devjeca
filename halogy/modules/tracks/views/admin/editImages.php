<script type="text/javascript">
$(function(){
	$('.toggle-image').click(function(event){ 
		event.preventDefault();		
		$('div#upload-image').slideToggle('400');
		$('div#upload-zip:visible, div#loader:visible').slideToggle('400');
	});

	$('a.lightbox').lightBox({imageLoading:'<?php echo $this->config->item('staticPath'); ?>/images/loading.gif',imageBtnClose: '<?php echo $this->config->item('staticPath'); ?>/images/lightbox_close.gif',imageBtnNext:'<?php echo $this->config->item('staticPath'); ?>/image/lightbox_btn_next.gif',imageBtnPrev:'<?php echo $this->config->item('staticPath'); ?>/image/lightbox_btn_prev.gif'});

});
</script>
<div id="upload-image" class="hidden clear">
	<form method="post" action="<?php echo site_url("/admin/tracks/editImages/".$id."/".$trackID); ?>" enctype="multipart/form-data" class="default">
		<input name="upload_photo" value="1" type="hidden">
		<label for="image">Image:</label>
		<div class="uploadfile">
			<?php echo $upload; ?>
		</div>
		<br class="clear" />
		
		<label for="imageName">Description:</label>
		<?php echo $title; ?>
		<br class="clear" />

		<input type="submit" value="Save Changes" class="button nolabel" id="submit" />
		<a href="#" class="button cancel grey">Cancel</a>		
		
	</form>
</div>


	<h2>Picture Manager</h2>
	
	<form method="post" action="<?php echo site_url("/admin/tracks/editImages/".$id."/".$trackID); ?>" enctype="multipart/form-data" class="default">
		<?php if($upload == TRUE): ?>
		<a href="#" class="button toggle-image blue">Upload Image</a>
		<?php endif; ?>
		
	<?php if ($images): ?>
		<input type="submit" value="Delete Image(s)" class="button nolabel" id="submit" />
		<input name="delete_photos" value="1" type="hidden">
		
	<?php echo $this->pagination->create_links(); ?>
	
		<table class="images clear">	
			<tr>
			<?php
				$numItems = sizeof($images);
				$itemsPerRow = 5;
				$i = 0;
							
				foreach ($images as $image)
				{
					if (($i % $itemsPerRow) == 0 && $i > 1)
					{
						echo '</tr><tr>'."\n";
						$i = 0;
					}
					echo '<td valign="top" align="center" width="'.floor(( 1 / $itemsPerRow) * 100).'%">';

					$imagePath = "/static/uploads/".$image['image'];
					$imageThumbPath = $imagePath;
			?>
					<div class="buttons">
						<input type="checkbox" name="imagedata[<?php echo $image['imgID']; ?>]" value="1" class='' />
					</div>					

					<a href="<?php echo $imagePath; ?>" title="<?php echo $image['title']; ?>" class="lightbox"><?php echo ($thumb = display_image($imageThumbPath, $image['title'], 100, 'class="pic"')) ? $thumb : display_image($imagePath, $image['title'], 100, 'class="pic"'); ?></a>

					<p><strong><?php echo $image['title']; ?></strong></p>
					
			<?php
					echo '</td>'."\n";
					$i++;
				}
			
				for($x = 0; $x < ($itemsPerRow - $i); $x++)
				{
					echo '<td width="'.floor((1 / $itemsPerRow) * 100).'%">&nbsp;</td>';
				}
			?>
			</tr>
		</table>
		
		<?php echo $this->pagination->create_links(); ?>
	<?php else: ?>

	<p class="clear">This track has no images yet.</p>

	<?php endif; ?>
	</form>
	