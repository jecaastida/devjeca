<script>
function showUser(str) {
  if (str=="") {
    document.getElementById("txtHint").innerHTML="";
    return;
  } 
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
    }
  }
  xmlhttp.open("GET","/admin/artworks/artistinfo?q="+str,true);
  xmlhttp.send();
}
</script>



<style type="text/css">
.ac_results { padding: 0px; border: 1px solid black; background-color: white; overflow: hidden; z-index: 99999; }
.ac_results ul { width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0; }
.ac_results li { margin: 0px; padding: 2px 5px; cursor: default; display: block; font: menu; font-size: 12px; line-height: 16px; overflow: hidden; }
.ac_results li span.email { font-size: 10px; } 
.ac_loading { background: white url('/static/images/loader.gif') right center no-repeat; }
.ac_odd { background-color: #eee; }
.ac_over { background-color: #0A246A; color: white; }

</style>

<script language="javascript" type="text/javascript" src="/static/js/jquery.fieldreplace.js"></script>
<script type="text/javascript">
$(function(){
    $('#searchbox').fieldreplace();
	function formatItem(row) {
		if (row[0].length) return row[1]+'<br /><span class="email">('+row[0]+')</span>';
		else return 'No results';
	}
	$('#searchbox').autocomplete("<?php echo site_url(); ?>admin/users/ac_medium", { });
	$('#searchbox').result(function(event, data, formatted){
		$(this).parent('form').submit();
	});	
});
</script>





<script type="text/javascript">

/*function preview(el){

	$.post('<?php echo site_url('/admin/artworks/preview'); ?>', { body: $(el).val() }, function(data){

		$('div.preview').html(data);

	});

}*/





function preview(el, lang){

	$.post('<?php echo site_url('/admin/artworks/preview'); ?>', { body: $(el).val() }, function(data){

		if (lang) { 

			$('div#preview-nor').html(data); 

		} else {

			$('div#preview-en').html(data); 

		}

	});

}

	function formatting_nor(a, b) {

		var c = $('textarea#body-nor');

		if (b == 'bold') {

			$(c).insertAtCaret('**', '**')

		}

		if (b == 'italic') {

			$(c).insertAtCaret('*', '*')

		}

		if (b == 'h1') {

			$(c).insertAtCaret('# ', "\n\n")

		}

		if (b == 'h2') {

			$(c).insertAtCaret('## ', "\n\n")

		}

		if (b == 'h3') {

			$(c).insertAtCaret('### ', "\n\n")

		}

		$(c).focus()

	}



$(function(){

	$("input.datebox").datebox();

	/*$('textarea#body').focus(function(){

		$('.previewbutton').show();

	});

	$('textarea#body').blur(function(){

		preview(this);

	});

	preview($('textarea#body'));

*/

	$('textarea#body').focus(function(){

		$('.previewbutton').show();

	});

	$('textarea#body').focus(function(){

		$('.previewbutton').show();

	});

	$('textarea#body-nor').focus(function(){

		$('.previewbutton-nor').show();

	});	

	$('textarea#body').blur(function(){

		preview(this, false);

	});

	$('textarea#body-nor').blur(function(){

		preview(this, true);

	});

	$('a.previewbutton-nor').live('click', function () {

		$(this).hide();

		return false

	});	

	preview($('textarea#body'), false);

	preview($('textarea#body-nor'), true);



	$('a.boldbutton-nor').live('click', function () {

		formatting_nor(this, 'bold');

		return false

	});

	$('a.italicbutton-nor').live('click', function () {

		formatting_nor(this, 'italic');

		return false

	});

	$('a.h1button-nor').live('click', function () {

		formatting_nor(this, 'h1');

		return false

	});

	$('a.h2button-nor').live('click', function () {

		formatting_nor(this, 'h2');

		return false

	});

	$('a.h3button-nor').live('click', function () {

		formatting_nor(this, 'h3');

		return false

	});



	//added

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

	

	

});

</script>

<?php



if ($urlinfo = $this->artworks->selectartisturl($data['artist_link']))

{

	$url_string = $urlinfo['url_string'];

}



?>



	<script type="text/javascript">
    $(document).ready(function(){
							   
	<?php if ($data['frametype'] == 2 || $data['frametype'] == 3 ||$data['frametype'] == 4  ||$data['frametype'] == 5  ||$data['frametype'] == 6) { ?>
    $('#1').show();
	<?php }?>
	
	<?php if ($data['frametype'] == 1) { ?>
    $('#1').hide();
	<?php }?>
	
	<?php if (!$data['frametype']) { ?>
    $('#1').hide();
	<?php }?>
	
    $("#frametype").change(function(){
        $('#1').show('fast');
        $("#" + this.value).hide('fast');
    });
	
	
	<?php if ($data['signedtype'] == 2 || $data['signedtype'] == 3 ||$data['signedtype'] == 4  || $data['signedtype'] == 5  ||$data['signedtype'] == 6  ||$data['signedtype'] == 7) { ?>
    $('#11').show();
	<?php }?>
	
	<?php if ($data['signedtype'] == 11) { ?>
    $('#11').hide();
	<?php }?>
	
	<?php if (!$data['signedtype']) { ?>
    $('#11').hide();
	<?php }?>
	
	
	
	
    $("#signedtype").change(function(){
        $('#11').show('fast');
        $("#" + this.value).hide('fast');
    });
	
	
	
	
	<?php if ($data['artist_link'] == 241) { ?>
    $('#241').show();
	<?php }?>
	
	<?php if ($data['artist_link'] != 241) { ?>
    $('#241').hide();
	<?php }?>
	
	<?php if (!$data['artist_link']) { ?>
    $('#241').hide();
	<?php }?>
	
    $("#artist_link").change(function(){
        $('#241').hide('fast');
        $("#" + this.value).show('fast');
    });
	
	
	
	
    });
    </script>



<form name="form" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">

	<h1 class="headingleft">Edit Artwork <small>(<a href="<?php echo site_url('/admin/artworks/viewall?list=f'); ?>">Back to Artwork</a>)</small></h1>

	

	<div class="headingright">

  		<?php if  ($data['visible_on_website']) { ?>
        	<a href="<?php echo site_url(); ?>admin/artworks/email_owner/<?php echo $data['artworkID']; ?>" class="button">Email Owner</a>
		<?php } ?>


        <a href="<?php echo site_url(); ?>artists/<?php echo $data['artist_link']; ?>/<?php echo $url_string; ?>?artid=<?php echo $data['artworkID']; ?>" class="button">View Artwork Listing</a>

    

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
    
    <?php if ($this->session->userdata('owneremailmsg')) { ?>
    
		<div class="message">

			<p>Email Sent.</p>

		</div>
    	<?php  $this->session->unset_userdata('owneremailmsg'); ?>
    <?php } ?>

    <ul class="innernav clear">

        <li class="selected"><a href="#details" class="showtab">Details</a></li>

        <li><a href="#desc" class="showtab">Make an Offer</a></li>

        <li><a href="#variations" class="showtab">Comments</a></li>

        <li><a href="#variations2" class="showtab">Additional Images</a></li>	

    </ul>

    

    

    <br class="clear" />

    

    <div id="details" class="tab">

	<label for="artworkName">Title:</label>

	<?php echo @form_input('artworkTitle', set_value('artworkTitle', $data['artworkTitle']), 'id="artworkTitle" class="formelement"'); ?>

	<br class="clear" />

    

	<label for="artist_link">Artist Link:</label>

	<select name="artist_link" id="artist_link">

    <option>-- Select Artist --</option>

    <?php

	//select artists

	echo $this->artworks->artistlist($data['artist_link']);

	?>

    </select>

	<br class="clear" />

	<div id="241">
		<label for="artist_nolink_name">If artist not in list enter artist name:</label>
		<?php echo @form_input('artist_nolink_name', set_value('artist_nolink_name', $data['artist_nolink_name']), 'id="artist_nolink_name" class="formelement" placeholder=""'); ?>
    
        <br class="clear" />
	</div>

	<?php
	$owner_link_details = "";
	 if ($data['owner_link'])
	{
		$owner_link_details = $this->artworks->seller_details($data['owner_link']);
	}
	?>


   	<label for="owner_link">Seller Link:</label>

	<select name="owner_link" id="owner_link"  onchange="showUser(this.value)">

    <option>-- Select Owner --</option>

    <?php

	//select artists

	echo $this->artworks->owner_list($data['owner_link']);

	?>

    </select><span id="txtHint"><?php echo $owner_link_details; ?></span>

	<br class="clear" />

     

	<!--
    <label for="medium_link">Link to medium:</label>

	<select name="medium_link" id="medium_link">

    <option>-- Select Medium --</option>

    <?php

	//select mediums

	echo $this->artworks->mediumlist($data['medium_link']);

	?>

    </select>

	<br class="clear" />
	-->
    

	<label for="medium">Medium:</label>
	<?php echo @form_input('medium', set_value('medium', $data['medium']), 'id="searchbox" class="formelement"'); ?>
	<br class="clear" />
    
    

	<label for="category_link">Category:</label>

	<select name="category_link" id="category_link">

    <option>-- Select Category --</option>

    <?php

	//select categories

	echo $this->artworks->categorylist($data['category_link']);

	?>

    </select>

	<br class="clear" />

  
    
    <?php
	$frameval1 = '';
	$frameval2 = '';
	$frameval3 = '';
	$frameval4 = '';
	
	$frameval5 = '';
	$frameval6 = '';
	
	if ($data['frametype'] == '1')
	{
		$frameval1 = 'selected="selected"';
	}
	if ($data['frametype'] == '2')
	{
		$frameval2 = 'selected="selected"';
	}
	if ($data['frametype'] == '3')
	{
		$frameval3 = 'selected="selected"';
	}
	if ($data['frametype'] == '4')
	{
		$frameval4 = 'selected="selected"';
	}
	
	if ($data['frametype'] == '5')
	{
		$frameval5 = 'selected="selected"';
	}
	if ($data['frametype'] == '6')
	{
		$frameval6 = 'selected="selected"';
	}
	
	
	
	?>
    
    
	<label for="frametype">Frame:</label>
    <select id="frametype" name="frametype" >
    
    <option value="0">Blank</option>
    
    <option value="1" id="other" <?php echo $frameval1; ?>>Unframed</option>
    <option value="4"  <?php echo $frameval4; ?>>Framed with Plexiglas</option>
    <option value="3"  <?php echo $frameval3; ?>>Framed with Glass</option>
    <option value="2"  <?php echo $frameval2; ?>>Framed without Glass or Plexiglas</option>
    
    <option value="5"  <?php echo $frameval5; ?>>Other Frame</option>
    <option value="6"  <?php echo $frameval6; ?>>N/A</option>
    </select> 
    <br class="clear" /> 

	<div id="1">
        <label for="framingdescription">Please describe the framing</label>
    
        <?php echo @form_textarea('framingdescription', set_value('framingdescription', $data['framingdescription']), 'id="framingdescription" class="formelement code short"'); ?>
    
        <br class="clear" />
	</div>



	<label for="artworkDate">Date added:</label>

	<?php echo @form_input('artworkDate', date('d M Y', strtotime($data['artworkDate'])), 'id="artworkDate" class="formelement datebox"'); ?>

	<br class="clear" />



    
	<!--<label for="dateCreated">Date Changed:</label>

	<?php echo date('d M Y', strtotime($data['dateCreated'])); ?>

	<br class="clear" /><br/>
    
    
	<label for="manualdateCreated">Manually Edit "Date Changed" Field:</label>
	
    <?php  $manualdate = ""; ?>
	<?php echo @form_input('manualdateCreated', $manualdate, 'id="manualdateCreated" class="formelement datebox"'); ?>

	<br class="clear" />-->
    
    

	<label for="dateCreated">Date Changed:</label>
	<?php echo @form_input('dateCreated', date('d M Y', strtotime($data['dateCreated'])), 'id="dateCreated" class="formelement datebox"'); ?>
	<br class="clear" />
    
    

	<label for="consignment">Consignment?</label>

	<?php 

		$values = array(

			1 => 'Yes',

			0 => 'No',

		);

		echo @form_dropdown('consignment',$values,set_value('consignment', $data['consignment']), 'id="consignment"'); 

	?>

	<br class="clear" />

    

	<label for="new_release">New Release?</label>

	<?php 

		$values = array(

			1 => 'Yes',

			0 => 'No',

		);

		echo @form_dropdown('new_release',$values,set_value('new_release', $data['new_release']), 'id="new_release"'); 

	?>

	<br class="clear" />

    

	<label for="framed_size">Framed Size:</label>

	<?php echo @form_input('framed_size', set_value('framed_size', $data['framed_size']), 'id="framed_size" class="formelement"'); ?>

	<br class="clear" />

    

	<label for="unframed_size">Unframed size:</label>

	<?php echo @form_input('unframed_size', set_value('unframed_size', $data['unframed_size']), 'id="unframed_size" class="formelement"'); ?>

	<br class="clear" />

    

	<label for="series">Suite:</label>

	<?php echo @form_input('series', set_value('series', $data['series']), 'id="series" class="formelement"'); ?>

	<br class="clear" />

    

	<label for="edition_size">Edition Size:</label>

	<?php echo @form_input('edition_size', set_value('edition_size', $data['edition_size']), 'id="edition_size" class="formelement"'); ?>

	<br class="clear" />


	<label for="edition_number">Edition Number:</label>
	<?php echo @form_input('edition_number', set_value('edition_number', $data['edition_number']), 'id="edition_number" class="formelement"'); ?>
	<br class="clear" />
    

	<label for="collection">Collection:</label>

	<?php echo @form_input('collection', set_value('collection', $data['collection']), 'id="collection" class="formelement"'); ?>

	<br class="clear" />

    
    
    <?php
	$signedval11 = '';
	$signedval2 = '';
	$signedval3 = '';
	
	$signedval4 = '';
	$signedval5 = '';
	$signedval6 = '';
	$signedval7 = '';

	if ($data['signedtype'] == '11')
	{
		$signedval11 = 'selected="selected"';
	}
	if ($data['signedtype'] == '2')
	{
		$signedval2 = 'selected="selected"';
	}
	if ($data['signedtype'] == '3')
	{
		$signedval3 = 'selected="selected"';
	}
	
	if ($data['signedtype'] == '4')
	{
		$signedval4 = 'selected="selected"';
	}
	if ($data['signedtype'] == '5')
	{
		$signedval5 = 'selected="selected"';
	}
	if ($data['signedtype'] == '6')
	{
		$signedval6 = 'selected="selected"';
	}
	if ($data['signedtype'] == '7')
	{
		$signedval7 = 'selected="selected"';
	}
	
	
	?>
    
    
	<label for="signedtype">Signed:</label>
    <select name="signedtype" id="signedtype" class="">
    
    <option value="0" >Blank</option>
    
    <option value="3"  <?php  echo $signedval3; ?>>Hand-Signed</option>
    
    <option value="2"  <?php  echo $signedval2; ?>>Estate-Signed</option>
    
    <option value="4"  <?php  echo $signedval4; ?>>Plate-Signed</option>
    
    <option value="5"  <?php  echo $signedval5; ?>>Foundry Signature with Stamp</option>
    
    <option value="6"  <?php  echo $signedval6; ?>>Sculpture Foundry Mark</option>
    
    <option value="7"  <?php  echo $signedval7; ?>>Other</option>
    
    <option value="11" id="other" <?php  echo $signedval11; ?>>Not Signed</option>
    
    
    
    </select>  
	<br class="clear" />
    
	<div id="11">
    <label for="signaturelocation">Location of the signature</label>

    <?php echo @form_textarea('signaturelocation', set_value('signaturelocation', $data['signaturelocation']), 'id="signaturelocation" class="formelement code short"'); ?>

    <br class="clear" />
    </div>


    

	<label for="unframed_image">Unframed image:</label>

	<div class="uploadfile">

    

   <?php if ($data['unframed_image']) { ?>
   
   <p>
   <a href="<?php echo site_url()?>artworkcrop.php?type=1&artworkID=<?php echo $data['artworkID']; ?>">Crop image</a> | <a href="<?php echo site_url()?>artworkrotate.php?type=1&artworkID=<?php echo $data['artworkID']; ?>">Rotate image</a>
   </p>
   

	<img src="<?php echo site_url(); ?>/thumb.php?src=/static/uploads/artists/artworks/unframed_images/<?php echo $data['unframed_image']; ?>&w=120&r=<?php echo rand(5, 11115); ?>">

    <?php } ?>

    

	<input type="file" name="unframed_image" value="" size="16" id="unframed_image" />	

    

	<?php if ($data['unframed_image']) { ?>

    <br/><input name="remove_unframed_image" type="checkbox" id="remove_unframed_image" value="1" /> Delete

    <?php } ?>

    

    </div>

	<br class="clear" />

    

	<label for="unframed_alt_txt">Unframed alt text:</label>

	<?php echo @form_input('unframed_alt_txt', set_value('unframed_alt_txt', $data['unframed_alt_txt']), 'id="unframed_alt_txt" class="formelement"'); ?>

	<br class="clear" />

    

	<label for="framed_image">Framed image:</label>

	<div class="uploadfile">

    

   <?php if ($data['framed_image']) { ?>
   
   
  <p>
   <a href="<?php echo site_url()?>artworkcrop.php?type=2&artworkID=<?php echo $data['artworkID']; ?>">Crop image</a> | <a href="<?php echo site_url()?>artworkrotate.php?type=2&artworkID=<?php echo $data['artworkID']; ?>">Rotate image</a>
   </p>
   

	<img src="<?php echo site_url(); ?>/thumb.php?src=/static/uploads/artists/artworks/framed_images/<?php echo $data['framed_image']; ?>&w=120&r=<?php echo rand(5, 11115); ?>">

    <?php } ?>

    

	<input type="file" name="framed_image" value="" size="16" id="framed_image" />	

    

	<?php if ($data['framed_image']) { ?>

    <br/><input name="remove_framed_image" type="checkbox" id="remove_framed_image" value="1" /> Delete

    <?php } ?>

    

    </div>

	<br class="clear" />

    

	<label for="framed_alt_txt">Framed alt text:</label>

	<?php echo @form_input('framed_alt_txt', set_value('framed_alt_txt', $data['framed_alt_txt']), 'id="framed_alt_txt" class="formelement"'); ?>

	<br class="clear" />

	<label for="retail_price">Retail Price <strong><?php echo currency_symbol(); ?></strong>:</label>

	<?php echo @form_input('retail_price',number_format(set_value('retail_price', $data['retail_price']),2,'.',''), 'id="retail_price" class="formelement" style="width:120px"'); ?>

	<br class="clear" />

    

	<label for="retail_price_comment">Retail Price Comment:</label>

	<?php echo @form_input('retail_price_comment', set_value('retail_price_comment', $data['retail_price_comment']), 'id="retail_price_comment" class="formelement"'); ?>

	<br class="clear" />

	<label for="asking_price">Asking price <strong><?php echo currency_symbol(); ?></strong>:</label>

	<?php echo @form_input('asking_price',number_format(set_value('asking_price', $data['asking_price']),2,'.',''), 'id="asking_price" class="formelement" style="width:120px"'); ?>

	<br class="clear" />

    

	<label for="asking_price_comment">Asking Price Comment:</label>

	<?php echo @form_input('asking_price_comment', set_value('asking_price_comment', $data['asking_price_comment']), 'id="asking_price_comment" class="formelement"'); ?>

	<br class="clear" />
    
	<label for="net_price">Net Price <strong><?php echo currency_symbol(); ?></strong>:</label>

	<?php echo @form_input('net_price',number_format(set_value('net_price', $data['net_price']),2,'.',''), 'id="net_price" class="formelement" style="width:120px"'); ?>

	<br class="clear" />

    

	<label for="reduced_consignment">Reduced Consignment?</label>

	<?php 

		$values = array(

			0 => 'No',

			1 => 'Yes',

		);

		echo @form_dropdown('reduced_consignment',$values,set_value('reduced_consignment', $data['reduced_consignment']), 'id="reduced_consignment"'); 

	?>

	<br class="clear" />

	<label for="include_in_auction">Include in Auction page?</label>

	<?php 

		$values = array(

			0 => 'No',

			1 => 'Yes',

		);

		echo @form_dropdown('include_in_auction',$values,set_value('include_in_auction', $data['include_in_auction']), 'id="include_in_auction"'); 

	?>

	<br class="clear" />

    

	<label for="wanted">Wanted?</label>

	<?php 

		$values = array(

			0 => 'No',

			1 => 'Yes',

		);

		echo @form_dropdown('wanted',$values,set_value('wanted', $data['wanted']), 'id="wanted"'); 

	?>

	<br class="clear" />

    

	<label for="visible_on_website">Visible on Website?</label>
	<?php 
		$values = array(
			1 => 'Yes',
			0 => 'No',
		);
		echo @form_dropdown('visible_on_website',$values,set_value('visible_on_website', $data['visible_on_website']), 'id="visible_on_website"'); 
	?>
    <br class="clear" />

        

	<label for="condition">Condition</label>

	<?php echo @form_input('condition', set_value('condition', $data['condition']), 'id="condition" class="formelement"'); ?>

	<br class="clear" />

	<label for="Purchased Year">Purchased Year</label>

	<?php echo @form_input('purchase_year', set_value('purchase_year', $data['purchase_year']), 'id="purchase_year" class="formelement"'); ?>

	<br class="clear" />

	<label for="From ">From </label>

	<?php echo @form_input('from', set_value('from', $data['from']), 'id="from" class="formelement"'); ?>

	<br class="clear" />

	<!--<label for="Certificate">Certificate</label>

	<?php echo @form_input('certificate', set_value('certificate', $data['certificate']), 'id="certificate" class="formelement"'); ?>

	<br class="clear" />-->
    
    
	<label for="certificatestatus">Certificate</label>
	<?php 
		$values = array(
			0 => 'Blank',
			1 => 'Yes',
			2 => 'No',
		);
		echo @form_dropdown('certificatestatus',$values,set_value('certificatestatus', $data['certificatestatus']), 'id="certificatestatus"'); 
	?>
    <br class="clear" />

    
	<label for="certificate_issued_by">Certificate Issued By</label>
	<?php echo @form_input('certificate_issued_by', set_value('certificate_issued_by', $data['certificate_issued_by']), 'id="certificate_issued_by" class="formelement"'); ?>
	<br class="clear" />
    

    <label for="Private Information">Private Information</label>

    <?php echo @form_textarea('private_note', set_value('private_note', $data['private_note']), 'id="private_note" class="formelement code short"'); ?>

    <br class="clear" />



    <label for="owner_note">Owner Notes</label>

    <?php echo @form_textarea('owner_note', set_value('owner_note', $data['owner_note']), 'id="owner_note" class="formelement code short"'); ?>

    <br class="clear" />


	<div class="buttons-nor">

		<a href="#" class="boldbutton-nor"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_bold.png" alt="Bold" title="Bold" /></a>

		<a href="#" class="italicbutton-nor"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_italic.png" alt="Italic" title="Italic" /></a>

		<a href="#" class="h1button-nor"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_h1.png" alt="Heading 1" title="Heading 1"/></a>

		<a href="#" class="h2button-nor"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_h2.png" alt="Heading 2" title="Heading 2" /></a>

		<a href="#" class="h3button-nor"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_h3.png" alt="Heading 3" title="Heading 3" /></a>	

		<a href="#" class="previewbutton-nor"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_save.png" alt="Preview" title="Preview" /></a>	

		

	</div>

	

	<label for="summary_text">Artwork Summary Text:</label>

	<?php echo @form_textarea('summary_text', set_value('summary_text', $data['summary_text']), 'id="body-nor" class="formelement code short"'); ?>

	<div class="preview" id="preview-nor" style="height:78px"></div>

	<br class="clear" /><br />



<label for="url_text">URL text: <br /></label>



<?php echo @form_input('url_string', set_value('url_string', $data['url_string']), 'id="url_text" class="formelement"'); ?>







<span class="tip">This must be unique.</span>







<br class="clear" />

<label for="meta_keywords">Meta Keywords:</label>



<?php echo @form_textarea('meta_keywords', set_value('meta_keywords', $data['meta_keywords']), 'id="meta_tags" class="formelement code short"'); ?>



<br class="clear" />







<label for="meta_description">Meta Description:</label>



<?php echo @form_textarea('meta_desc', set_value('meta_desc', $data['meta_desc']), 'id="meta_description" class="formelement code short"'); ?>



<br class="clear" /><br />







<label for="feature">Feature In Homepage ?:</label>



<?php 



	$values = array(


		0 => 'No',


		1 => 'Yes',




	);



	echo @form_dropdown('feature',$values,set_value('feature', $data['feature']), 'id="feature"'); 



?>    

<br />

    

    </div>

    

	<div id="desc" class="tab">	

    

	<label for="include_on_MAO_page">Enable Make An Offer for this Artwork</label>

	<?php 

		$values = array(

			0 => 'No',

			1 => 'Yes',

		);

		echo @form_dropdown('include_on_MAO_page',$values,set_value('include_on_MAO_page', $data['include_on_MAO_page']), 'id="include_on_MAO_page" class="formelement"'); 

	?>

    <span class="tip">If enabled then the Asking price label is shown with the make an offer content. <!--and it will over-ride any asking price value or note.--></span>

	<br class="clear" />

    

	<label for="include_price">Include Price</label>

	<?php 

		$values = array(

			0 => 'No',

			1 => 'Yes',

		);

		echo @form_dropdown('include_price',$values,set_value('include_price', $data['include_price']), 'id="include_price" class="formelement"'); 

	?>

	<br class="clear" />

    

    

	<label for="MAO_content">Make an Offer content:</label>

	<?php echo @form_textarea('MAO_content', set_value('MAO_content', $data['MAO_content']), 'id="MAO_content" class="formelement code short"'); ?>

	<br class="clear" /><br />

    </div>

    

	<div id="variations" class="tab">	

	<!--<label for="comment">Comment:</label>

	<?php echo @form_textarea('comment', set_value('comment', $data['comment']), 'id="comment" class="formelement code short"'); ?>

	<br class="clear" /><br />-->

    

	<div class="buttons">

		<a href="#" class="boldbutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_bold.png" alt="Bold" title="Bold" /></a>

		<a href="#" class="italicbutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_italic.png" alt="Italic" title="Italic" /></a>

		<a href="#" class="h1button"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_h1.png" alt="Heading 1" title="Heading 1"/></a>

		<a href="#" class="h2button"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_h2.png" alt="Heading 2" title="Heading 2" /></a>

		<a href="#" class="h3button"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_h3.png" alt="Heading 3" title="Heading 3" /></a>	

		<a href="#" class="previewbutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_save.png" alt="Preview" title="Preview" /></a>	

	</div>	

	<label for="description">Comment:</label>

	<?php echo @form_textarea('description', set_value('description', $data['description']), 'id="body" class="formelement code short"'); ?>

	<div class="preview" id="preview-en" style="height:78px"></div>

	<br class="clear" /><br />



    

	</div>

    

	<div id="variations2" class="tab">	

    <a href="/admin/artworkimages/viewall?id=<?php echo $data['artworkID']; ?>">Manage Additional Images</a>

    </div>

	<br />

	<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>		

	

</form>

