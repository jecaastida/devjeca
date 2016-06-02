<?php 
if ($cart):

	foreach ($cart as $key => $item): 
		$variationHTML = '';

                foreach($item['variations'] as $vart){
                    $variationHTML .= '<br />('.$vart['name'].': '.$vart['variation'].')';
                }

                // check if oversize
                // if ($item['oversize'] == 1){ 
                    // $variationHTML .= '<br /> (Oversized Product)';                           
                // }
                
		$key = $this->core->encode($key);
                
                
?>

<tr>
    <td ><a href="/shop/cart_summary/remove/<?php echo $key; ?>?redirect=<?php echo $this->input->get('current');?>" target="_parent" onclick="return confirm('Are you sure you want to remove this from the shopping cart?')"><img border="0" src="<?php echo site_url() . '/static/uploads/2695778843d7969509b323c79c5224c3.gif' ?>" alt="Remove" /></a></td>
	<td><a href="/shop/<?php echo $item['productID']; ?>/<?php echo strtolower(url_title($item['productName'])); ?>" target="_parent"><img border="0" style="margin:2px 0 2px 0" src="<?php echo $item['productThumb'] ?>" alt="Thumbnail" height="40"/></a></td>
	<td>
        <a style="text-decoration:none;font-weight:bold;" href="/shop/<?php echo $item['productID']; ?>/<?php echo strtolower(url_title($item['productName'])); ?>" target="_parent"><?php echo $item['productName']; ?></a><br/>
        Qty: <?php echo $item['quantity']; ?>&nbsp;&nbsp;
        <?php echo currency_symbol(); ?><?php echo number_format($item['price'] , 2, '.', ','); ?>
    </td>
        
</tr>

<?php endforeach; ?>
<?php
	// find out if there is a donation (adding it after the postage)
	if ($this->session->userdata('cart_donation') > 0):
?>
<tr>
	<td>Donation</td>
	<td>1 <a href="/shop/cart/remove_donation/"><img src="http://ir.lr-dev.com/static/uploads/2695778843d7969509b323c79c5224c2.gif" alt="Remove" /></a></td>
	<td><?php echo currency_symbol(); ?><?php echo number_format($this->session->userdata('cart_donation'), 2); ?></td>
</tr>
<?php endif; ?>
<?php endif; ?>