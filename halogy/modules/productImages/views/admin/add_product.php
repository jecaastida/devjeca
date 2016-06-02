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
		$('div#details, div#desc, div#variations,div#variations2').hide();
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
	$('div#desc, div#variations,div#variations2').hide();

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

<h1 class="headingleft">Add Product <small>(<a href="<?php echo site_url('/admin/shop/products'); ?>">Back to Products</a>)</small></h1>

<div class="headingright">
	<input type="submit" value="Save Changes" class="button save" />
</div>

<div class="clear"></div>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<ul class="innernav clear">
	<li class="selected"><a href="#details" class="showtab">Details</a></li>
	<li><a href="#desc" class="showtab">Description</a></li>
	<li><a href="#variations" class="showtab">Settings</a></li>
    <li><a href="#variations2" class="showtab">Variations</a></li>
    <li><a href="#images" class="showtab">Product Images</a></li>	
	
</ul>

<br class="clear" />

<div id="details" class="tab">

	<h2 class="underline">Product Details</h2>
	
	<label for="productName">Product name:</label>
	<?php echo @form_input('productName',set_value('productName', $data['productName']), 'id="productName" class="formelement"'); ?>
	<br class="clear" />
        
        <label for="category">Category: <small>[<a href="<?php echo site_url('/admin/shop/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
	<div class="categories">
		<?php if ($categories): ?>
		<?php foreach($categories as $category): ?>
			<div class="category">
				<?php echo @form_checkbox('catsArray['.$category['catID'].']', $category['catName']); ?><span><?php echo ($category['parentID']) ? '<small>'.$category['parentName'].' &gt;</small> '.$category['catName'] : $category['catName']; ?></span>
			</div>
		<?php endforeach; ?>
		<?php else: ?>
			<div class="category">
				<strong>Warning:</strong> It is strongly recommended that you use categories or this may not appear properly. <a href="<?php echo site_url('/admin/blog/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')"><strong>Please update your categories here</strong></a>.
			</div>
		<?php endif; ?>
	</div>
	<br class="clear" /><br />
        
        <!--
	<label for="catalogueID">Catalogue ID:</label>
	<?php echo @form_input('catalogueID',set_value('catalogueID', $data['catalogueID']), 'id="catalogueID" class="formelement"'); ?>
	<span class="tip">This is for your own catalogue reference and stock keeping.</span>
	<br class="clear" />

	<label for="subtitle">Sub-title / Author:</label>
	<?php echo @form_input('subtitle',set_value('subtitle', $data['subtitle']), 'id="subtitle" class="formelement"'); ?>
	<br class="clear" />
	-->
	<label for="tags">Tags: <br /></label>
	<?php echo @form_input('tags', set_value('tags', $data['tags']), 'id="tags" class="formelement"'); ?>
	<span class="tip">Separate tags with a comma (e.g. &ldquo;places, hobbies, favourite work&rdquo;)</span>
	<br class="clear" />
	
	
	<label for="price">Retail Price (<?php echo currency_symbol(); ?>):</label>
	
	<?php echo @form_input('price',number_format(set_value('price', $data['price']),2,'.',''), 'id="price" class="formelement small"'); ?>
	<br class="clear" />
        
        <label for="price">Sale Price (<?php echo currency_symbol(); ?>):</label> 
	
	<?php echo @form_input('sale_price',number_format(set_value('sale_price', $data['sale_price']),2,'.',''), 'id="sale_price" class="formelement small"'); ?>
        <span class="tip">If 0.00 then no sale price is shown.</span>
	<br class="clear" />
        
        <label for="price">Wholesale Price (<?php echo currency_symbol(); ?>):</label>	
	<?php echo @form_input('wholesale_price',number_format(set_value('wholesale_price', $data['wholesale_price']),2,'.',''), 'id="wholesale_price" class="formelement small"'); ?>
	<span class="tip">Only visible to Wholesale customers who are logged-in.</span>
        <br class="clear" />
        
        <label for="price">Our Cost (<?php echo currency_symbol(); ?>):</label>
	
	<?php echo @form_input('ourcost_price',number_format(set_value('ourcost_price', $data['ourcost_price']),2,'.',''), 'id="ourcost_price" class="formelement small"'); ?>
	<br class="clear" />

	<label for="image"  style="width: 250px;">Image: (Allowed Formats: gif,jpg,png,jpeg)</label> <span class="tip">Image size should be 800 pixels wide and 450 pixels high.</span>
	<br class="clear" />
        <br />
        <div class="uploadfile">
		<?php if (isset($imagePath)):?>
			<img src="<?php echo $imagePath; ?>" alt="Product image" />
		<?php endif; ?>
		<?php echo @form_upload('image',$this->validation->image, 'size="16" id="image"'); ?>
	</div>
        <span class="tip" >Use this easy site to get you image to the <br /> desired dimensions <a target="_blank" href="http://resizeyourimage.com">http://resizeyourimage.com</a></span>
	<br class="clear" />
	
	

	<h2 class="underline">Availability</h2>
	
	<label for="status">Status:</label>
	<?php 
		$values = array(
			'S' => 'In stock',
			'O' => 'Out of stock',
			'P' => 'Pre-order'
		);
		echo @form_dropdown('status',$values,set_value('status', $data['status']), 'id="status" class="formelement"'); 
	?>
	<br class="clear" />
	
	<?php if ($this->site->config['shopStockControl']): ?>
		<label for="stock">Stock:</label>
		<?php echo @form_input('stock', set_value('stock', $data['stock']), 'id="stock" class="formelement small"'); ?>
		<br class="clear" />
	<?php endif; ?>	

	<label for="featured">Featured?</label>
	<?php 
		$values = array(
			'N' => 'No',
			'Y' => 'Yes',
		);
		echo @form_dropdown('featured',$values,set_value('featured', $data['featured']), 'id="featured" class="formelement"'); 
	?>
	<span class="tip">Featured products will show on the shop front page.</span>
	<br class="clear" />
    
    <label for="featured">Featured in Homepage?</label>
	<?php 
		$values = array(
			'0' => 'No',
			'1' => 'Yes',
		);
		echo @form_dropdown('featured_inhome',$values,set_value('featured_inhome', $data['featured_inhome']), 'id="featured" class="formelement"'); 
	?>
	<span class="tip">Featured products will show on home page.</span>
	<br class="clear" />
	
	<label for="published">Visible:</label>
	<?php 
		$values = array(
			1 => 'Yes',
			0 => 'No (hide product)',
		);
		echo @form_dropdown('published',$values,set_value('published', $data['published']), 'id="published"'); 
	?>
	<br class="clear" />



</div>

<div id="desc" class="tab">	

	<h2 class="underline">Product Description</h2>
	
	<div class="buttons">
		<a href="#" class="boldbutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_bold.png" alt="Bold" title="Bold" /></a>
		<a href="#" class="italicbutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_italic.png" alt="Italic" title="Italic" /></a>
		<a href="#" class="h1button"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_h1.png" alt="Heading 1" title="Heading 1"/></a>
		<a href="#" class="h2button"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_h2.png" alt="Heading 2" title="Heading 2" /></a>
		<a href="#" class="h3button"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_h3.png" alt="Heading 3" title="Heading 3" /></a>	
		<a href="#" class="urlbutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_url.png" alt="Insert Link" title="Insert Link" /></a>
		<a href="#" class="halogycms_imagebutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_image.png" alt="Insert Image" title="Insert Image" /></a>
		<a href="#" class="halogycms_filebutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_file.png" alt="Insert File" title="Insert File" /></a>
		<a href="#" class="previewbutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_save.png" alt="Preview" title="Preview" /></a>	
	</div>
	<label for="body">Body:</label>
	<?php echo @form_textarea('description', set_value('description', $data['description']), 'id="body" class="formelement code half"'); ?>
	<div class="preview"></div>
	<br class="clear" /><br />

	<label for="excerpt">Excerpt:</label>
	<?php echo @form_textarea('excerpt',set_value('excerpt', $data['excerpt']), 'id="excerpt" class="formelement short"'); ?>
	<br class="clear" />
	<span class="tip nolabel">The excerpt is a brief description of your product which is used in some templates.</span>
	<br class="clear" /><br />

</div>

<div id="variations" class="tab">	
	
	<h2 class="underline">Settings</h2>
        <label for="freePostage">Oversize?</label>
	<?php 
		$values = array(
			0 => 'No',
			1 => 'Yes',
		);
		echo @form_dropdown('oversize',$values,set_value('oversize', $data['oversize']), 'id="oversize"'); 
	?>
	<br class="clear" />
	
	<label for="freePostage">Free Shipping?</label>
	<?php 
		$values = array(
			0 => 'No',
			1 => 'Yes',
		);
		echo @form_dropdown('freePostage',$values,set_value('freePostage', $data['freePostage']), 'id="freePostage"'); 
	?>
	<br class="clear" />

	<label for="files">File:</label>
	<?php
		$options = '';
		$options[0] = 'This product is not a file';			
		if ($files):
			foreach ($files as $file):
				$ext = @explode('.', $file['filename']);
				$options[$file['fileID']] = $file['fileRef'].' ('.strtoupper($ext[1]).')';
			endforeach;
		endif;					
		echo @form_dropdown('fileID',$options,set_value('fileID', $data['fileID']),'id="files" class="formelement"');
	?>
	<span class="tip">You can make this product a downloadable file (e.g. a premium MP3 or document).</span>
	<br class="clear" />

	<label for="bands">Shipping Band:</label>
	<?php
		$options = '';
		$options[0] = 'No product is not restricted';			
		if ($bands):
			foreach ($bands as $band):
				$options[$band['bandID']] = $band['bandName'];
			endforeach;
		endif;					
		echo @form_dropdown('bandID', $options, set_value('bandID', $data['bandID']),'id="bands" class="formelement"');
	?>
	<span class="tip">You can restrict this product to a shipping band if necessary.</span>
	<br class="clear" /><br />
	
	

</div>

<div id="variations2" class="tab" style="display: none;">

	


	<h2 class="underline">Variations</h2>
        <br class="clear" />	
        <div id="copy-variation" style="display: none;">
            <div>
                <input type="checkbox" class="chk-copy" name="chk-copy"/> Copy variations from other product
            </div>
            	
            <div class="product-list">
                <br class="clear" />	       
                <span class="tip nolabel">Copying variations from other product will automatically delete all existing variations/options of this product.</span>
                <br class="clear" />
                <?php echo $listOfProducts; ?>
            </div>
            
        </div>
        <br class="clear" />
	
        
        <div class="input-variations">
            <span class="tip nolabel">Suggested image sizes are 110px by 110px, 220px by 220px, 330px by 330px, 440px by 440px, 550px by 550px</span>
            <br class="clear" />	
            <br class="clear" />
            <div id="variations-list">
                
            </div>

            <br class="clear" />
            <div >
                    <div class="addvars">
                            <p><a href="#" class="addvar" id="btn-add"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_plus.gif" alt="Delete" class="padded" /> Add New Variation</a></p>
                            <br class="clear" />				
                    </div>

            </div>
        </div>
        
        
	
        
        

</div>




<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	
</form>
