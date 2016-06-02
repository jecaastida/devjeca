<script type="text/javascript">
$(function(){
	$('.toggle-video').click(function(event){ 
		event.preventDefault();		
		$('div#upload-video').slideToggle('400');
		$('div#upload-zip:visible, div#loader:visible').slideToggle('400');
	});

	$('a.lightbox').lightBox({imageLoading:'<?php echo $this->config->item('staticPath'); ?>/images/loading.gif',imageBtnClose: '<?php echo $this->config->item('staticPath'); ?>/images/lightbox_close.gif',imageBtnNext:'<?php echo $this->config->item('staticPath'); ?>/image/lightbox_btn_next.gif',imageBtnPrev:'<?php echo $this->config->item('staticPath'); ?>/image/lightbox_btn_prev.gif'});

});
</script>
<div id="upload-video" class="hidden clear">
	<form method="post" action="<?php echo site_url("/admin/tracks/editVideos/".$id."/".$trackID); ?>" enctype="multipart/form-data" class="default">
		<input name="upload_vids" value="1" type="hidden">
		<label for="image">Youtube Link:</label>
		<?php echo $upload; ?>
		<br class="clear" />
		
		<label for="imageName">Title:</label>
		<?php echo $title; ?>
		<br class="clear" />

		<input type="submit" value="Save Changes" class="button nolabel" id="submit" />
		<a href="#" class="button cancel grey">Cancel</a>		
		
	</form>
</div>


	<h2>Video Manager</h2>
	
	<form method="post" action="<?php echo site_url("/admin/tracks/editVideos/".$id."/".$trackID); ?>" enctype="multipart/form-data" class="default">
		<?php if($upload == TRUE): ?>
		<a href="#" class="button toggle-video blue">Embed Video</a>
		<?php endif; ?>
		
	<?php if ($videos): ?>
		<input type="submit" value="Delete Video(s)" class="button nolabel" id="submit" />
		<input name="delete_videos" value="1" type="hidden">
		
	<?php echo $this->pagination->create_links(); ?>
	
		<table class="images clear">	
			<tr>
			<?php
				$numItems = sizeof($videos);
				$itemsPerRow = 5;
				$i = 0;
							
				foreach ($videos as $image)
				{
					if (($i % $itemsPerRow) == 0 && $i > 1)
					{
						echo '</tr><tr>'."\n";
						$i = 0;
					}
					echo '<td valign="top" align="center" width="'.floor(( 1 / $itemsPerRow) * 100).'%">';

			?>
					<div class="buttons">
						<input type="checkbox" name="videodata[<?php echo $image['vidID']; ?>]" value="1" class='' />
					</div>					

					<a href="http://youtu.be/<?php echo $image['video']; ?>" title="<?php echo $image['title']; ?>" class="lightbox" target="_blank">
						<img src="http://img.youtube.com/vi/<?php echo $image['video']; ?>/default.jpg" class='pic' width='100' />
					</a>

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

	<p class="clear">This track has no videos yet.</p>

	<?php endif; ?>
	</form>
	