<?php 
if ($cart):
//print_r($cart);

	foreach ($cart as $key => $item): 
                //print_r($item['variations']);
		$variationHTML = '';
		
                /*
		// get variation 1
		if ($item['variation1']) $variationHTML .= '<br />('.$this->site->config['shopVariation1'].': '.$item['variation1'].')';
		
		// get variations 2
		if ($item['variation2']) $variationHTML .= '<br />('.$this->site->config['shopVariation2'].': '.$item['variation2'].')';
	
		// get variations 3
		if ($item['variation3']) $variationHTML .= '<br />('.$this->site->config['shopVariation3'].': '.$item['variation3'].')';
                */

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
        <td ><a href="/shop/cart/remove/<?php echo $key; ?>"><img src="<?php echo site_url() . '/static/uploads/2695778843d7969509b323c79c5224c2.gif' ?>" alt="Remove" /></a></td>
	<td class="product-thumb"><a href="/shop/<?php echo $item['productID']; ?>/<?php echo strtolower(url_title($item['productName'])); ?>"><img class="cart-product-thumbs" src="<?php echo $item['productThumb'] ?>" alt="Thumbnail"/></a></td>
	<td class="product-name">
            <a style="text-decoration:none;font-weight:bold;" href="/shop/<?php echo $item['productID']; ?>/<?php echo strtolower(url_title($item['productName'])); ?>"><?php echo $item['productName']; ?><?php echo $variationHTML; ?></a>
        </td>
	
        
        <td><?php echo currency_symbol(); ?><?php echo number_format($item['price'] , 2, '.', ','); ?></td>
	<?php if ($this->uri->segment(2) == 'checkout'): ?>
		<td><?php echo $item['quantity']; ?></td>
	<?php else: ?>
		<td><input class="formelement" name="quantity[<?php echo $key; ?>]" type="text" maxlength="2" value="<?php echo $item['quantity']; ?>" /> </td>
	<?php endif; ?>
	<td class="product-price"><span style="font-weight:bold;" ><?php echo currency_symbol(); ?><?php echo number_format(($item['price'] * $item['quantity']), 2); ?></span></td>
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