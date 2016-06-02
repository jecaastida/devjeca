<?php if ($variations): ?>
        <!--
	<label class="variationlabel" for="variation1"><?php echo $this->site->config['shopVariation1']; ?>:</label>
	<br class="clear" />
	<select name="variation1" class="variation" id="variation1">
		<?php foreach ($variation1 as $variation): ?>
			<option value="<?php echo $variation['variationID']; ?>"><?php echo $variation['variation']; ?>
			<?php if ($variation['price'] > 0) echo '+'.currency_symbol().$variation['price']; ?>
			</option>
		<?php endforeach; ?>
	</select>
	<br class="clear" />
        -->
        
        <?php 
        $varID = "";
        $ctr = 0;
        $arr = array();
        //print_r($variations);
        foreach($variations as $var){
            if ($var['type'] != $varID) {
                    $ctr++;
                    $arr[$ctr][] = $var['name'];
                    $varID = $var['type'];
            }
            $arr[$ctr][] = $var['variation'].'~|~'.$var['price'].'~|~'.$var['variationID'].'~|~'.$var['img'];
            
            if($var['img'] != ''){
                if (strpos($arr[$ctr][0],'~|~img') == false) {
                    $arr[$ctr][0] = $arr[$ctr][0].'~|~img';
                }
            }
        }
        
        
        
        
        $str = '';       
        if (isset($arr) && is_array($arr)) {
                $varCounter = 1;
                foreach ($arr as $a) {
                    
                    $ctr2 = 0;
                    $isImage = false;
                    foreach ($a as $b) {                                              
                            if ($ctr2 == 0) {
                                $arrType = explode('~|~',$b);
                                if($arrType[1] == 'img'){
                                    $isImage = true;
                                }
                                
                                if(!$isImage){
                                    $str.= "<label class='variationlabel' for='variation1'>$b:</label>";
                                    $str.= "<br class='clear' />
                                                <select name='variation[]' class='variation' >";
                                }else{
                                    $str.= "<label class='variationlabel' for='variation1'>".$arrType[0].":</label>
                                        <ul id='mycarousel' class='mycarousel jcarousel-skin-tango'>";
                                }                
                            } else {
                                $arrB = explode('~|~', $b);
                                if(!$isImage){
                                    $str .= "<option value='".$arrB[2]."'>".$arrB[0];
                                    if($arrB[1] > 0){
                                        $str .= " + ".currency_symbol().$arrB[1];
                                    }
                                    $str .= "</option>";
                                }else{
                                    $p = "";
                                    if($arrB[1] > 0){
                                        $p = " + ".currency_symbol().$arrB[1];
                                    }
                                    
                                    if($arrB['3'] == ''){
                                        $arrB['3'] = 'no-image.jpg';
                                    }
                                    
                                    $str .= "<li class='var'>
                                                 <a style='text-decoration:none' class='lightbox' title='".$arrB[0].$p."' data-fancybox-group='gallery2' href='".  site_url('/static/uploads/').'/'.$arrB['3']."'>
                                                    <img src='".  site_url('/static/uploads/').'/'.$arrB['3']."' width='110'  alt='' />
                                                    
                                                 </a>
                                                 <br />
                                                 <div>
                                                    <input class='chk-variations' type='radio' name='variation[$varCounter]' value='".$arrB[2]."'>".$arrB[0].$p."
                                                 </div>
                                                 </li>";
                                }
                            }
                        
                        
                        $ctr2++;
                    }
                    
                    //to determine the closing tag
                    if(!$isImage){
                        $str .= "           </select>
                                    <br class='clear' />";
                    }else{
                        $str .= "</ul><br class='clear' />";
                    }
                    $varCounter++;
                }
                echo $str;
        }
        ?>
        
<?php endif; ?>
<!--
<div style="margin-bottom:5px;line-height:150%;">
                                   <ul id="mycarousel" class="mycarousel jcarousel-skin-tango">
    <li><img src="http://static.flickr.com/66/199481236_dc98b5abb3_s.jpg" width="75" height="75" alt="" /></li>
    <li><img src="http://static.flickr.com/75/199481072_b4a0d09597_s.jpg" width="75" height="75" alt="" /></li>
    <li><img src="http://static.flickr.com/57/199481087_33ae73a8de_s.jpg" width="75" height="75" alt="" /></li>
    <li><img src="http://static.flickr.com/77/199481108_4359e6b971_s.jpg" width="75" height="75" alt="" /></li>
    <li><img src="http://static.flickr.com/58/199481143_3c148d9dd3_s.jpg" width="75" height="75" alt="" /></li>
    <li><img src="http://static.flickr.com/72/199481203_ad4cdcf109_s.jpg" width="75" height="75" alt="" /></li>
    <li><img src="http://static.flickr.com/58/199481218_264ce20da0_s.jpg" width="75" height="75" alt="" /></li>
    <li><img src="http://static.flickr.com/69/199481255_fdfe885f87_s.jpg" width="75" height="75" alt="" /></li>
    <li><img src="http://static.flickr.com/60/199480111_87d4cb3e38_s.jpg" width="75" height="75" alt="" /></li>
    <li><img src="http://static.flickr.com/70/229228324_08223b70fa_s.jpg" width="75" height="75" alt="" /></li>
  </ul>
</div>
-->