<!--?php if ($bands): ?-->
	<!--?php foreach($bands as $band): ?-->
        
        <!--option value="<!--?php echo $band['multiplier']; ?-->" <!--?php echo ($band['multiplier'] == $shippingBand) ? 'selected="selected"' : ''; ?><!---->
            <!--?php echo $band['bandName']; ?></option-->
	<!--?php endforeach; ?-->
<!--?php else: ?-->
	<!--option value="">Select your courier</option-->
<!--?php endif; ?-->

	<!--option value="">Select your courier</option-->
    <option value="1"> Xend</option>
    <option value="2" > LBC</option>