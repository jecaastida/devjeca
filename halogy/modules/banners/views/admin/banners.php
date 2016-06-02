<h1 class="headingleft">Banners</h1>

<?php if ($banners): ?>

<table class="default clear">
	<thead>
		<tr>
			<th style="width:500px;" class="narrow">Banner Name</th>
			<th style="width:500px;" class="narrow">Banner URL</th>
			<!--<th >Banner Description</th>-->
			<th style="width:500px;" class="narrow">Order</th>
			<th style="width:500px;" "narrow">Banner Image</th>
			<th class="tiny">&nbsp;</th>
		</tr>
	</thead>
	<tbody id="shop_products">
	<?php foreach ($banners as $banner): ?>
		<tr id="banner-<?php echo $banner['banner_id']; ?>">
			<td class="col1"><?php echo (in_array('banners_edit', $this->permission->permissions)) ? anchor('/admin/banners/edit_banner/'.$banner['banner_id'], $banner['banner_name']) : $banner['banner_name']; ?></td>
			<td class="col2"><?php echo $banner['banner_url']; ?></td>
			<!--<td class="col3"><?php echo $banner['headline']; ?></td>-->
			<td class="col4"><?php echo $banner['sequence_order']; ?></td>
			<td class="col5"><img style="height:40px;" src="/static/uploads/<?php echo $banner['banner_file']; ?>" alt="<?php echo $banner['banner_name']; ?>" /></td>
			<td class="col6">
				<?php if (in_array('banners_edit', $this->permission->permissions)): ?>	
					<?php echo anchor('/admin/banners/edit_banner/'.$banner['banner_id'], 'Edit'); ?>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->create_links(); ?>
<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p>You haven't set up any Banners yet.</p>

<?php endif; ?>