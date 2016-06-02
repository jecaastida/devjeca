<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
#halogycms_admin {
display:none;
}
body {
background:none repeat scroll 0 0 #CCCCCC;
font:12px Arial,Helvetica,sans-serif;
}

.button {
	background: none repeat scroll 0 0 #696969;
	border: 1px solid #696969;
    color: #FFFFFF;
	cursor: pointer;
	text-decoration: none;
	vertical-align: middle;
	height:28px;
}
a.button {
	padding:5px 10px;
}
.button:hover {
	background: #383838;
	border: 1px solid #383838;
	text-decoration:none;
}

</style>
</head>
<body>
<div>
  {if cart:items}
  {page:totalitem}
    <br />
    <table rules="rows" frame="hsides" width="320">
      {cart:items}
    </table>
    {else}
    <div style="text-align:center"><h3>Your shopping cart is empty!</h3></div>
    <br />
    {/if}
    {if cart:items} <br />
    <table width="320">
            {/if}
            {if cart:discounts}
            <tr class="bold-font-row">
              <td>Discounts applied:</td>
              <td>({cart:discounts})</td>
            </tr>
            {/if}
            {if cart:items}
            <tr class="bold-font-row">
              <td>Sub total:</td>
              <td>{cart:subtotal}</td>
            </tr>
            <tr class="bold-font-row">
              <td>Shipping:</td>
              <td>{cart:postage}</td>
            </tr>
            {/if}
            {if cart:tax}
            <tr class="bold-font-row">
              <td>Tax:</td>
              <td>{cart:tax}</td>
            </tr>
            {/if}
            {if cart:items}
            <tr>
              <td><strong style="font-size:18px">TOTAL:</strong></td>
              <td><strong style="font-size:18px">{cart:total}</strong></td>
            </tr>
    </table>
    <br/><a href="{site:url}shop/cart" target="_parent" class="button">View Cart</a> <a href="{site:url}shop/checkout_method" target="_parent" class="button">Checkout</a>
    <br />
    {/if}
</div>
</body>
</html>

